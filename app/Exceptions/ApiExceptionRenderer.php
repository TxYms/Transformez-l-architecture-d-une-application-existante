<?php

namespace App\Exceptions;

use App\Http\Responses\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

final class ApiExceptionRenderer
{
    public function __invoke(Throwable $e, Request $request): ?JsonResponse
    {
        if (! $request->is('api/*')) {
            return null;
        }

        return match (true) {
            $e instanceof ValidationException => ApiResponse::error(
                'The given data was invalid.', 422, ['errors' => $e->errors()]
            ),
            $e instanceof AuthenticationException => ApiResponse::error('Unauthenticated.', 401),
            $e instanceof InvalidCredentialsException => ApiResponse::error($e->getMessage(), 401),
            $e instanceof LoginThrottledException => ApiResponse::error(
                $e->getMessage(), 429, null, ['Retry-After' => $e->retryAfter]
            ),
            $e instanceof AuthorizationException,
            $e instanceof AccessDeniedHttpException => ApiResponse::error('This action is unauthorized.', 403),
            $e instanceof ModelNotFoundException => ApiResponse::error($this->notFoundMessage($e), 404),
            $e instanceof NotFoundHttpException => ApiResponse::error(
                $e->getPrevious() instanceof ModelNotFoundException
                    ? $this->notFoundMessage($e->getPrevious())
                    : 'Resource not found.',
                404
            ),
            $e instanceof HttpExceptionInterface => ApiResponse::error(
                $this->httpMessage($e), $e->getStatusCode(), null, $e->getHeaders()
            ),
            default => ApiResponse::error(
                'Server error.', 500, config('app.debug') ? ['exception' => $e->getMessage()] : null
            ),
        };
    }

    private function notFoundMessage(ModelNotFoundException $e): string
    {
        return class_basename($e->getModel()).' not found.';
    }

    private function httpMessage(HttpExceptionInterface $e): string
    {
        return match ($e->getStatusCode()) {
            405 => 'Method not allowed.',
            429 => 'Too many attempts.',
            default => $e->getMessage() !== '' ? $e->getMessage() : 'HTTP error.',
        };
    }
}
