<?php

declare(strict_types=1);

namespace App\Exception;

use App\DTO\ApiExceptionDto;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AssetException extends ApiException
{
    /**
     * AssetException constructor.
     * @param string $message
     */
    public function __construct(string $message)
    {
        $apiExceptionDto = new ApiExceptionDto(
            400,
            $message,
        );
        parent::__construct($apiExceptionDto);
    }

}