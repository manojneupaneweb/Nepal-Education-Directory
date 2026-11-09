<?php

namespace App\Services;

use App\Models\ProjectError;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class ProjectErrorRecorder
{
    public function record(Throwable $exception): void
    {
        if ($exception instanceof ValidationException || $exception instanceof AuthenticationException) {
            return;
        }

        if ($exception instanceof HttpExceptionInterface && $exception->getStatusCode() < 500) {
            return;
        }

        try {
            $message = mb_substr($exception->getMessage() !== '' ? $exception->getMessage() : $exception::class, 0, 2000);
            $existing = ProjectError::query()
                ->open()
                ->where('exception', $exception::class)
                ->where('message', $message)
                ->where('file', $exception->getFile())
                ->where('line', $exception->getLine())
                ->first();

            if ($existing) {
                $existing->increment('occurrences');

                return;
            }

            ProjectError::query()->create([
                'exception' => $exception::class,
                'message' => $message,
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'url' => request()?->fullUrl(),
                'occurrences' => 1,
            ]);
        } catch (Throwable) {
            // Recording must not hide the original error.
        }
    }
}
