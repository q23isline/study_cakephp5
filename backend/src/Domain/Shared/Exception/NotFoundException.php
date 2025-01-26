<?php
declare(strict_types=1);

namespace App\Domain\Shared\Exception;

use Exception;

final class NotFoundException extends Exception
{
    /**
     * @param array<\App\Domain\Shared\Exception\ExceptionItem> $errors
     * @param \Exception|null $previous
     */
    public function __construct(
        protected array $errors = [],
        ?Exception $previous = null
    ) {
        $this->errors = $errors;

        $message = 'Not Found';
        $code = 404;

        parent::__construct($message, $code, $previous);
    }

    /**
     * 整形する
     *
     * @return array{errors: array{status: string, detail: string}[]}
     */
    public function format(): array
    {
        $errors = [];
        foreach ($this->getErrors() as $error) {
            $errors[] = [
                'status' => '404',
                'detail' => $error->detail,
            ];
        }

        $result = [
            'errors' => $errors,
        ];

        return $result;
    }

    /**
     * @return array<\App\Domain\Shared\Exception\ExceptionItem>
     */
    private function getErrors(): array
    {
        return $this->errors;
    }
}
