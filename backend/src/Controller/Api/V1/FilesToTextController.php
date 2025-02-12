<?php
declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Controller\AppController;
use Cake\Utility\Text;
use Exception;
use Laminas\Diactoros\UploadedFile;
use PhpOffice\PhpWord\IOFactory;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Smalot\PdfParser\Parser;
use SplFileInfo;
use ZipArchive;

class FilesToTextController extends AppController
{
    /**
     * initialize
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->viewBuilder()->setClassName('Json')->setOption('serialize', true);
    }

    /**
     * @return void
     */
    public function invoke(): void
    {
        $params = [
            'zip_file' => $this->request->getData('zip_file'),
        ];

        if (!$this->validate($params)) {
            $result = $this->createBadRequestResult();
            $this->set($result);
            $this->response = $this->response->withStatus(400);

            return;
        }

        $command = [
            'zipFile' => $params['zip_file'],
        ];

        $data = $this->handle($command);
        $result = $this->format($data);

        $this->set($result);
    }

    /**
     * クエリパラメータのバリデーションする
     *
     * @param array{zip_file: mixed} $params
     * @return bool
     */
    private function validate(array $params): bool
    {
        if (!($params['zip_file'] instanceof UploadedFile)) {
            return false;
        }

        // ZIP 拡張子しか許可しない
        if ($params['zip_file']->getClientMediaType() !== 'application/zip') {
            return false;
        }

        return true;
    }

    /**
     * 400 エラーのレスポンス作成
     *
     * @return array{errors: array{source: array{pointer: string}, title: string, detail: string}[]}
     */
    private function createBadRequestResult(): array
    {
        $result = [
            'errors' => [
                [
                    'source' => [
                        'pointer' => 'zip_file',
                    ],
                    'title' => 'Invalid Request Body',
                    'detail' => '不正なファイルです.',
                ],
            ],
        ];

        return $result;
    }

    /**
     * ユースケースを表現する
     *
     * @param array{zipFile: \Laminas\Diactoros\UploadedFile} $command
     * @return array{link: string}
     */
    private function handle(array $command): array
    {
        $outputFileName = Text::uuid();
        $outputBasePath = 'file' . DS . 'toText' . DS . $outputFileName . '.txt';
        $outputFilePath = WWW_ROOT . $outputBasePath;

        $this->init($outputFilePath);
        $extractToPath = $this->uncompress($command['zipFile']);
        $files = $this->getFilesRecursive($extractToPath);
        $this->write($files, $outputFilePath);

        $result = [
            'link' => DS . $outputBasePath,
        ];

        return $result;
    }

    /**
     * 解凍する
     *
     * @param \Laminas\Diactoros\UploadedFile $zipFile
     * @return string
     */
    private function uncompress(UploadedFile $zipFile): string
    {
        $timeStamp = date('YmdHis') . substr(explode('.', microtime(true) . '')[1], 0, 3);
        $extractToPath = TMP . 'file' . DS . 'toText' . DS . $timeStamp;
        /** @var string $tmpName */
        $tmpName = $zipFile->getStream()->getMetadata('uri');
        $zip = new ZipArchive();
        $result = $zip->open($tmpName);
        if ($result === true) {
            $zip->extractTo($extractToPath);
            $zip->close();
        } else {
            throw new Exception('ZIP ファイルを開けませんでした。');
        }

        return $extractToPath;
    }

    /**
     * ファイルダウンロード用のディレクトリとファイルを用意する
     *
     * @param string $outputFilePath
     * @return void
     */
    private function init(string $outputFilePath): void
    {
        $directoryPath = dirname($outputFilePath);

        if (!is_dir($directoryPath)) {
            if (!mkdir($directoryPath, 0777, true) && !is_dir($directoryPath)) {
                throw new Exception('ダウンロード用のファイルを作れませんでした。');
            }
        }

        file_put_contents($outputFilePath, '');
    }

    /**
     * 再帰的にディレクトリ内のファイルを取得する
     *
     * @param string $directory
     * @return array<string>
     */
    private function getFilesRecursive(string $directory): array
    {
        $files = [];
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
        foreach ($iterator as $file) {
            if ($file instanceof SplFileInfo && $file->isFile()) {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }

    /**
     * 書き込む
     *
     * @param array<string> $files
     * @param string $outputFilePath
     * @return void
     */
    private function write(array $files, string $outputFilePath): void
    {
        foreach ($files as $file) {
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            switch ($extension) {
                case 'docx':
                    $content = $this->extractWordContent($file);
                    file_put_contents($outputFilePath, $content . PHP_EOL, FILE_APPEND);
                    break;
                case 'pdf':
                    $content = $this->extractPdfContent($file);
                    file_put_contents($outputFilePath, $content . PHP_EOL, FILE_APPEND);
                    break;
            }

            file_put_contents($outputFilePath, '------' . PHP_EOL, FILE_APPEND);
        }
    }

    /**
     * Word ファイルの内容を抽出する
     *
     * @param string $filePath
     * @return string
     */
    private function extractWordContent(string $filePath): string
    {
        $phpWord = IOFactory::load($filePath);
        $text = '';
        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getText')) {
                    $text .= $element->getText() . PHP_EOL;
                }
            }
        }

        return $text;
    }

    /**
     * PDF ファイルの内容を抽出する
     *
     * @param string $filePath
     * @return string
     */
    private function extractPdfContent(string $filePath): string
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($filePath);

        return $pdf->getText();
    }

    /**
     * JSON レスポンス用にフォーマットする
     *
     * @param array{link: string} $data
     * @return array{data: array{type: string, id: string, attributes: array{download_link: string}}}
     */
    private function format(array $data): array
    {
        $result = [
            'data' => [
                'type' => 'files_to_text',
                'id' => '',
                'attributes' => [
                    'download_link' => $data['link'],
                ],
            ],
        ];

        return $result;
    }
}
