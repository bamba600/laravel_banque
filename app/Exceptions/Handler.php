<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException as LaravelValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Convertit les exceptions en réponses JSON pour l'API
     */
    public function render($request, Throwable $exception)
    {
        // Exclure la route Swagger UI qui doit retourner une vue HTML
        if ($request->is('api/documentation')) {
            return parent::render($request, $exception);
        }

        // Pour les requêtes API et Swagger, retourner JSON
        if ($request->is('api/*') || $request->is('docs/*')) {
            // Nos exceptions personnalisées
            if ($exception instanceof ApiException) {
                return $exception->render();
            }

            // Validation Laravel
            if ($exception instanceof LaravelValidationException) {
                return app(ValidationException::class, [
                    'errors' => $exception->errors()
                ])->render();
            }

            // Erreurs 404
            if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                return app(ResourceNotFoundException::class)->render();
            }

            // Autres erreurs
            return app(ApiException::class, [
                'message' => 'Erreur interne du serveur',
                'statusCode' => 500,
                'errorCode' => 'INTERNAL_ERROR'
            ])->render();
        }

        // Pour les autres requêtes, comportement normal
        return parent::render($request, $exception);
    }
}
