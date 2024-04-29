<?php

declare(strict_types=1);

namespace App\Service\Exception;

class SubresourceNotFoundException extends ServiceException
{
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct('Subresource not found: '.$message, $code, $previous);
    }
}
