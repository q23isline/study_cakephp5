<?php
declare(strict_types=1);

namespace App\Log\Formatter;

use Cake\Log\Formatter\AbstractFormatter;
use DateTime;

/**
 * vendor/cakephp/cakephp/src/Log/Formatter/DefaultFormatter.php を大いに参考
 */
class CustomFormatter extends AbstractFormatter
{
    /**
     * Default config for this class
     *
     * @var array<string, mixed>
     */
    protected array $_defaultConfig = [
        'dateFormat' => 'Y-m-d H:i:s',
        'includeTags' => false,
        'includeDate' => true,
    ];

    /**
     * @inheritDoc
     */
    public function format(mixed $level, string $message, array $context = []): string
    {
        $message = $this->normalizeToSingleLine($message);

        if ($this->_config['includeDate']) {
            $message = sprintf('%s %s: %s', (new DateTime())->format($this->_config['dateFormat']), $level, $message);
        } else {
            $message = sprintf('%s: %s', $level, $message);
        }
        if ($this->_config['includeTags']) {
            return sprintf('<%s>%s</%s>', $level, $message, $level);
        }

        return $message;
    }

    /**
     * ログメッセージを必ず1行に正規化する
     * 改行が含まれる場合のみ置換（性能考慮）
     * 正規表現は使わない
     *
     * @param string $message
     * @return string
     */
    private function normalizeToSingleLine(string $message): string
    {
        if (!str_contains($message, "\n") && !str_contains($message, "\r")) {
            return $message;
        }

        return str_replace(
            ["\r\n", "\r", "\n"],
            ' ',
            $message
        );
    }
}
