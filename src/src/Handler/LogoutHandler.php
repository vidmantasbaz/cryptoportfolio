<?php

declare(strict_types=1);

namespace App\Handler;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;

class LogoutHandler implements LogoutSuccessHandlerInterface
{

    /**
     * @{inheritDoc}
     */
    public function onLogoutSuccess(Request $request)
    {
        return new JsonResponse('Successfully logout from system.');
    }
}