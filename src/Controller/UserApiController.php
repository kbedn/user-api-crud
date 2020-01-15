<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{
    JsonResponse,
    Response,
    Request
};
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api", name="user_api")
 */
class UserApiController extends AbstractController
{
    /**
     * @param UserRepository $userRepository
     * @return JsonResponse
     * @Route("/users", name="users", methods={"GET"})
     */
    public function getUsers(UserRepository $userRepository): JsonResponse
    {
        $data = $userRepository->findAll();
        return $this->response($data);
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return JsonResponse
     * @Route("/users", name="users_add", methods={"POST"})
     */
    public function addUser(
        EntityManagerInterface $entityManager,
        Request $request,
        UserPasswordEncoderInterface $encoder
    ): ?JsonResponse {
        try{
            $request = $this->transformJsonBody($request);

            if (!$request || !$request->get('username') || !$request->request->get('password')){
                throw new \InvalidArgumentException();
            }

            $user = new User();
            $user->setUsername($request->get('username'));
            $user->setPassword($encoder->encodePassword($user, $request->get('password')));

            $entityManager->persist($user);
            $entityManager->flush();

            $data = [
                'status' => 200,
                'success' => 'User added successfully',
            ];
            return $this->response($data);

        }catch (\Exception $e){
            $data = [
                'status' => 422,
                'errors' => 'Data no valid',
            ];
            return $this->response($data, 422);
        }
    }

    /**
     * @param UserRepository $userRepository
     * @param string $username
     * @return JsonResponse
     * @Route("/users/{username}", name="users_get", methods={"GET"})
     */
    public function getApiUser(UserRepository $userRepository, $username): JsonResponse
    {
        $user = $userRepository->loadUserByUsername($username);

        if (!$user){
            $data = [
                'status' => Response::HTTP_NOT_FOUND,
                'errors' => Response::$statusTexts[Response::HTTP_NOT_FOUND],
            ];
            return $this->response($data, Response::HTTP_NOT_FOUND);
        }

        return $this->response($user);
    }

    /**
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository
     * @param $id
     * @return JsonResponse
     * @Route("/users/{id}", name="users_put", methods={"PUT"})
     */
    public function updateUser(
        Request $request,
        UserPasswordEncoderInterface $encoder,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        $id
    ): ?JsonResponse {
        try{
            $user = $userRepository->find($id);

            if (!$user){
                $data = [
                    'status' => Response::HTTP_NOT_FOUND,
                    'errors' => Response::$statusTexts[Response::HTTP_NOT_FOUND],
                ];
                return $this->response($data, Response::HTTP_NOT_FOUND);
            }

            $request = $this->transformJsonBody($request);

            if (!$request || !$request->get('username') || !$request->request->get('password')){
                throw new \Exception();
            }

            $user = new User();
            $user->setUsername($request->get('username'));
            $user->setPassword($encoder->encodePassword($user, $request->get('password')));

            $entityManager->flush();

            $data = [
                'status' => Response::HTTP_OK,
                'errors' => Response::$statusTexts[Response::HTTP_OK],
            ];
            return $this->response($data);

        }catch (\Exception $e){
            $data = [
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
            ];
            return $this->response($data, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository ,
     * @param $id
     * @return JsonResponse
     * @Route("/users/{id}", name="userss_delete", methods={"DELETE"})
     */
    public function deleteUser(EntityManagerInterface $entityManager, UserRepository $userRepository, $id): JsonResponse
    {
        $user = $userRepository->find($id);

        if (!$user){
            $data = [
                'status' => Response::HTTP_NOT_FOUND,
                'errors' => Response::$statusTexts[Response::HTTP_NOT_FOUND],
            ];
            return $this->response($data, Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($user);
        $entityManager->flush();
        $data = [
            'status' => Response::HTTP_OK,
            'errors' => Response::$statusTexts[Response::HTTP_OK],
        ];
        return $this->response($data);
    }

    /**
     * @param mixed $data
     * @param int $status
     * @param array $headers
     * @return JsonResponse
     */
    protected function response($data, $status = Response::HTTP_OK, $headers = []): JsonResponse
    {
        return new JsonResponse($data, $status, $headers);
    }

    /**
     * @param Request $request
     * @return Request
     */
    protected function transformJsonBody(Request $request): Request
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            return $request;
        }

        $request->request->replace($data);

        return $request;
    }
}
