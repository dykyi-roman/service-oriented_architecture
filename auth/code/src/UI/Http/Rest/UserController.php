<?php

declare(strict_types=1);

namespace App\UI\Http\Rest;

use App\Application\Common\Error;
use App\Application\User\Command\UserRegisterCommand;
use App\Domain\User\Entity\User;
use App\Domain\User\Transformer\Api\UserApiTransformer;
use App\Domain\User\ValueObject\FullName;
use App\Domain\User\ValueObject\UserRegistrationRequest;
use Exception;
use Immutable\Exception\ImmutableObjectException;
use InvalidArgumentException;
use League\Tactician\CommandBus;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @OA\Tag(name="User")
 */
class UserController extends ApiController
{
    /**
     * @OA\Post(
     *     tags={"User"},
     *     path="/api/user/login",
     *     summary="Get current user from token storage",
     *     @OA\Parameter(
     *          name="query",
     *          in="query",
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="email",
     *                  type="string",
     *              ),
     *              @OA\Property(
     *                  property="password",
     *                  type="string",
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *     ),
     * )
     */

    /**
     * @OA\Post(
     *     tags={"User"},
     *     path="/api/user/registration",
     *     summary="Register a new user",
     *     @OA\Parameter(
     *          name="query",
     *          in="query",
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="email",
     *                  type="string",
     *              ),
     *              @OA\Property(
     *                  property="password",
     *                  type="string",
     *              ),
     *              @OA\Property(
     *                  property="firstName",
     *                  type="string",
     *                  description="phone|email"
     *              ),
     *              @OA\Property(
     *                  property="lastName",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="phone",
     *                  type="string",
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Success",
     *     ),
     * )
     */

    /**
     * @Route(path="/api/user/registration", methods={"POST"}, name="user_registration")
     */
    public function register(Request $request, CommandBus $commandBus): JsonResponse
    {
        try {
            $uuid = Uuid::uuid4();
            $request = $this->transformJsonBody($request);
            $commandBus->handle(new UserRegisterCommand(
                $uuid,
                new UserRegistrationRequest(
                    $request->get('email'),
                    $request->get('password'),
                    $request->get('phone'),
                    new FullName(
                        $request->get('firstName'),
                        $request->get('lastName')
                    )
                )
            ));
        } catch (Exception | InvalidArgumentException | ImmutableObjectException $exception) {
            return $this->respondWithError(Error::create($exception->getMessage(), $exception->getCode()));
        }

        return $this->respondCreated(['uuid' => $uuid->toString()]);
    }

    /**
     * @OA\Get(
     *     tags={"User"},
     *     security= { { "bearerAuth": {} } },
     *     path="/api/user/current",
     *     summary="Get current user from token storage",
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *     ),
     * )
     */

    /**
     * @Route(path="/api/user/current", methods={"GET"}, name="api.user.current_info")
     */
    public function user(Request $request, TokenStorageInterface $tokenStorage): JsonResponse
    {
        $token = $tokenStorage->getToken();
        if (null === $token) {
            return $this->respondNotFound(Error::create('Token not Found!'));
        }

        $user = $token->getUser();
        if (!$user instanceof User) {
            return $this->respondNotFound(Error::create('User not Found!'));
        }

        return $this->respondWithSuccess(UserApiTransformer::transform($user));
    }
}
