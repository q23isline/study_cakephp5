<?php
declare(strict_types=1);

namespace App\Domain\Shared\Exception;

/**
 * @property-read string $pointer
 * @property-read string $title
 * @property-read string $detail
 */
final class ExceptionItem
{
    /**
     * @param string $pointer
     * @param string $title
     * @param string $detail
     */
    public function __construct(
        public readonly string $pointer,
        public readonly string $title,
        public readonly string $detail
    ) {
    }
}
