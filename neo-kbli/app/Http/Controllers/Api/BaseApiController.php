<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\AbstractPaginator;

class BaseApiController extends Controller
{
    /**
     * Return a success JSON response.
     */
    protected function success($data = null, string $message = 'Data berhasil diambil', int $code = 200, $meta = null): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ];

        if ($data instanceof ResourceCollection && $data->resource instanceof AbstractPaginator) {
            $paginator = $data->resource;
            $response['data'] = $data->response()->getData(true)['data'];
            $response['meta'] = [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
            ];
        } elseif ($meta !== null) {
            $response['meta'] = $meta;
        } else {
            $response['meta'] = null;
        }

        return response()->json($response, $code);
    }

    /**
     * Return a created JSON response (HTTP 201).
     */
    protected function created($data = null, string $message = 'Data berhasil dibuat'): JsonResponse
    {
        return $this->success($data, $message, 201);
    }

    /**
     * Return a generic error JSON response.
     */
    protected function error(string $message = 'Terjadi kesalahan', int $code = 400, $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Return a validation error JSON response (HTTP 422).
     */
    protected function validationError($errors, string $message = 'Validasi gagal'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ], 422);
    }

    /**
     * Return a not found JSON response (HTTP 404).
     */
    protected function notFound(string $message = 'Data tidak ditemukan'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], 404);
    }

    /**
     * Return a forbidden JSON response (HTTP 403).
     */
    protected function forbidden(string $message = 'Anda tidak memiliki akses ke sumber daya ini'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], 403);
    }

    /**
     * Return an unauthorized JSON response (HTTP 401).
     */
    protected function unauthorized(string $message = 'Unauthenticated'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], 401);
    }

    /**
     * Return an internal server error JSON response (HTTP 500).
     */
    protected function serverError(string $message = 'Terjadi kesalahan internal pada server'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], 500);
    }
}
