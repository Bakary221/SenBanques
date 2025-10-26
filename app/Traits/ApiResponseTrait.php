<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    /**
     * Retourne une réponse de succès standardisée
     */
    protected function successResponse(
        mixed $data = null,
        string $message = 'Opération réussie',
        int $statusCode = 200,
        ?array $pagination = null,
        ?array $links = null
    ): JsonResponse {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        if ($pagination !== null) {
            $response['pagination'] = $pagination;
        }

        if ($links !== null) {
            $response['links'] = $links;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Retourne une réponse d'erreur standardisée
     */
    protected function errorResponse(
        string $message = 'Une erreur est survenue',
        int $statusCode = 400,
        mixed $errors = null,
        ?string $errorCode = null
    ): JsonResponse {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        if ($errorCode !== null) {
            $response['error_code'] = $errorCode;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Génère les informations de pagination
     */
    protected function generatePaginationData($paginator): array
    {
        return [
            'currentPage' => $paginator->currentPage(),
            'totalPages' => $paginator->lastPage(),
            'totalItems' => $paginator->total(),
            'itemsPerPage' => $paginator->perPage(),
            'hasNext' => $paginator->hasMorePages(),
            'hasPrevious' => $paginator->currentPage() > 1,
        ];
    }

    /**
     * Génère les liens de pagination
     */
    protected function generatePaginationLinks($paginator, string $baseUrl): array
    {
        $links = [
            'self' => $baseUrl . '?page=' . $paginator->currentPage() . '&limit=' . $paginator->perPage(),
            'first' => $baseUrl . '?page=1&limit=' . $paginator->perPage(),
            'last' => $baseUrl . '?page=' . $paginator->lastPage() . '&limit=' . $paginator->perPage(),
        ];

        if ($paginator->hasMorePages()) {
            $links['next'] = $baseUrl . '?page=' . ($paginator->currentPage() + 1) . '&limit=' . $paginator->perPage();
        }

        if ($paginator->currentPage() > 1) {
            $links['previous'] = $baseUrl . '?page=' . ($paginator->currentPage() - 1) . '&limit=' . $paginator->perPage();
        }

        return $links;
    }
}