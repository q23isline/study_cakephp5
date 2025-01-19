<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Core\Configure;
use Cake\Http\CallbackStream;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use Cake\View\Exception\MissingTemplateException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Tcpdf;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Static content controller
 *
 * This controller will render views from templates/Pages/
 *
 * @link https://book.cakephp.org/5/en/controllers/pages-controller.html
 */
class PagesController extends AppController
{
    /**
     * Displays a view
     *
     * @param string ...$path Path segments.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Http\Exception\ForbiddenException When a directory traversal attempt.
     * @throws \Cake\View\Exception\MissingTemplateException When the view file could not
     *   be found and in debug mode.
     * @throws \Cake\Http\Exception\NotFoundException When the view file could not
     *   be found and not in debug mode.
     * @throws \Cake\View\Exception\MissingTemplateException In debug mode.
     */
    public function display(string ...$path): ?Response
    {
        if (!$path) {
            return $this->redirect('/');
        }
        if (in_array('..', $path, true) || in_array('.', $path, true)) {
            throw new ForbiddenException();
        }
        $page = $subpage = null;

        if (!empty($path[0])) {
            $page = $path[0];
        }
        if (!empty($path[1])) {
            $subpage = $path[1];
        }
        $this->set(compact('page', 'subpage'));

        // エクセルでダウンロードする場合
        // return $this->exportSampleExcel();

        // PDF でダウンロードする場合
        // return $this->exportSamplePdf();

        // エクセルダウンロード、PDF ダウンロード試すときはコメントにする
        return $this->renderDisplay($path);
    }

    /**
     * 画面表示
     *
     * @param array<string> $path
     * @return \Cake\Http\Response
     */
    private function renderDisplay(array $path): Response
    {
        try {
            return $this->render(implode('/', $path));
        } catch (MissingTemplateException $exception) {
            if (Configure::read('debug')) {
                throw $exception;
            }
            throw new NotFoundException();
        }
    }

    /**
     * エクセル出力する
     *
     * @return \Cake\Http\Response
     * @see https://phpspreadsheet.readthedocs.io/en/latest/
     * @phpstan-ignore method.unused
     */
    private function exportSampleExcel(): Response
    {
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setCellValue('A1', 'Hello World !');

        $writer = new Xlsx($spreadsheet);
        $stream = new CallbackStream(function () use ($writer): void {
            $writer->save('php://output');
        });

        $encodedName = rawurlencode('hello world.xlsx');

        return $this->response->withType('xlsx')
            ->withHeader('Content-Disposition', "attachment;filename*=UTF-8''{$encodedName}")
            ->withBody($stream);
    }

    /**
     * PDF 出力する
     *
     * @return \Cake\Http\Response
     * @see https://phpspreadsheet.readthedocs.io/en/latest/
     * @see https://tcpdf.org/
     * @phpstan-ignore method.unused
     */
    private function exportSamplePdf(): Response
    {
        IOFactory::registerWriter('Pdf', Tcpdf::class);

        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setCellValue('A1', 'Hello World !');

        $writer = IOFactory::createWriter($spreadsheet, 'Pdf');
        $stream = new CallbackStream(function () use ($writer): void {
            $writer->save('php://output');
        });

        $encodedName = rawurlencode('hello world.pdf');

        return $this->response->withType('pdf')
            ->withHeader('Content-Disposition', "attachment;filename*=UTF-8''{$encodedName}")
            ->withBody($stream);
    }
}
