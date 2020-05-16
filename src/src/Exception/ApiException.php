<?php

declare(strict_types=1);

namespace App\Exception;

use App\DTO\ApiExceptionDto;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ApiException extends HttpException
{
    public const FORM_VALIDATION_ERROR = 'FORM_VALIDATION_ERROR';
    public const ASSET_NOT_FOUND = 'ASSET_NOT_FOUND';
    public const ASSET_NOT_USERS = 'ASSET_NOT_USERS';

    /** @var ApiExceptionDto */
    private $apiExceptionDto;

    /**
     * ApiException constructor.
     * @param ApiExceptionDto $apiExceptionDto
     * @param array $headers
     * @param int $code
     */
    public function __construct(ApiExceptionDto $apiExceptionDto, array $headers = array(), $code = 0)
    {
        $this->apiExceptionDto = $apiExceptionDto;
        $statusCode = $apiExceptionDto->getStatusCode();
        $message = $apiExceptionDto->getMessage();

        parent::__construct($statusCode, $message, null, $headers, $code);
    }

    /**
     * @return ApiExceptionDto
     */
    public function getApiDto(): ApiExceptionDto
    {
        return $this->apiExceptionDto;
    }
}