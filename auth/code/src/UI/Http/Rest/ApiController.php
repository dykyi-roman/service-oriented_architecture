<?php

declare(strict_types=1);

namespace App\UI\Http\Rest;

use App\Application\Common\Error;
use App\Application\Common\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @OA\Info(title="Auth API", version="1.0.3")
 * @OA\SecurityScheme(
 *   securityScheme="bearerAuth",
 *   type="http",
 *   scheme="bearer",
 * )
 */
abstract class ApiController extends AbstractController
{
    protected int $statusCode = 200;

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    protected function setStatusCode(int $statusCode): self
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    public function response(array $data, array $headers = []): JsonResponse
    {
        return new JsonResponse($data, $this->getStatusCode(), $headers);
    }

    public function respondWithError(Error $error, int $statusCode = 500, array $headers = []): JsonResponse
    {
        return new JsonResponse(Response::error($error), $statusCode, $headers);
    }

    public function respondWithSuccess(array $data = [], int $statusCode = 200, array $headers = []): JsonResponse
    {
        return new JsonResponse(Response::success($data), $statusCode, $headers);
    }

    public function respondUnauthorized(Error $error): JsonResponse
    {
        return $this->respondWithError($error, 401);
    }

    public function respondValidationError(Error $error): JsonResponse
    {
        return $this->respondWithError($error, 422);
    }

    public function respondNotFound(Error $error): JsonResponse
    {
        return $this->respondWithError($error, 404);
    }

    public function respondCreated(array $data = []): JsonResponse
    {
        return $this->respondWithSuccess($data, 201);
    }

    public function respondUpdated(array $data = []): JsonResponse
    {
        return $this->respondWithSuccess($data, 202);
    }

    // this method allows us to accept JSON payloads in POST requests
    // since Symfony 4 doesn’t handle that automatically:
    protected function transformJsonBody(Request $request): Request
    {
        if ('' === $request->getContent()) {
            return $request;
        }

        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        if ($data === null) {
            return $request;
        }

        $request->request->replace($data);

        return $request;
    }
}
