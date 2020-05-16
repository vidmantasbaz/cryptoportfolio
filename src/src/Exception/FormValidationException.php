<?php

declare(strict_types=1);

namespace App\Exception;

use App\DTO\ApiExceptionDto;
use Symfony\Component\HttpKernel\Exception\HttpException;

class FormValidationException extends ApiException
{
    /**
     * FormValidationException constructor.
     * @param array $errors
     */
    public function __construct( array $errors)
    {
        $apiExceptionDto = new ApiExceptionDto(
            400,
            self::ASSET_NOT_FOUND,
            $errors
        );
        parent::__construct($apiExceptionDto);
    }

}