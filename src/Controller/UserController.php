<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\CustomerRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

class UserController extends AbstractController
{
    /**
     * Récupère les détails d'un client
     *
     * @Route("/api/user/{id}", name="user_detail", methods={"GET"})
     * @OA\Response(
     *     response=200,
     *     description="Returns the informations about the user ID",
     *     @OA\JsonContent(
     *      type="array",
     *      @OA\Items(ref=@Model(type=User::class, groups={"get:userList"}))
     *      )
     * )
     * @OA\Response(
     *     response=404,
     *     description="User not found",
     * )
     * @OA\Response(
     *     response=401,
     *     description="Unauthorized request"
     * )
     * @OA\Tag(name="User")
     */
    public function user(?User $user): Response
    {
        if($user === null)
        {
            return $this->json([
               'status' => 404,
               'message' => 'User not found'
            ], 404);
        }

        return $this->json($user, 200, [], ['groups' => 'get:infos']);
    }

    /**
     * Supprime un utlisateur
     *
     * @Route("/api/user/delete/{id}", name="user_delete", methods={"delete"})
     * @OA\Response(
     *     response=204,
     *     description="Delete user (return a empty body)"
     * )
     * @OA\Response(
     *     response=401,
     *     description="Unauthorized request"
     * )
     * @OA\Tag(name="User")
     */
    public function delete(?User $user, EntityManagerInterface $entityManager, UserRepository $userRepository)
    {
        $user = $userRepository->find($user->getId());
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->json('The user has been delete', 204, []);
    }

    /**
     * Permet d'ajouter un nouvel utilisateur
     *
     * @Route("/api/user/add", name="user_add", methods={"post"})
     * @OA\RequestBody(
     *     description="The new user to create",
     *     required=true,
     *     @OA\MediaType(
     *         mediaType="application/Json",
     *         @OA\Schema(
     *             type="object",
     *             @OA\Property(
     *                 property="fisrtname",
     *                 description="User's first name",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="lastname",
     *                 description="User's last name",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="email",
     *                 description="User's email address",
     *                 type="string"
     *             ),
     *         )
     *     )
     * )
     * @OA\Response(
     *     response=201,
     *     description="Create a user and returns it"
     * )
     * @OA\Response(
     *     response=400,
     *     description="Bad Json syntax or incorrect data"
     * )
     * @OA\Response(
     *     response=401,
     *     description="Unauthorized request"
     * )
     * @OA\Tag(name="User")
     */
    public function add(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer,
                        ValidatorInterface $validator, CustomerRepository $customerRepository)
    {
        try
        {
            $user = $serializer->deserialize($request->getContent(), User::class, 'json');
            $user->setCreatedAt(new \DateTimeImmutable());
            $user->setCustomerId($this->getUser());

            $errors = $validator->validate($user);

            if(count($errors) > 0 )
            {
                return $this->json($errors, 400);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->json($user, 201, [], ['groups' => 'post:user']);
        }
        catch(NotEncodableValueException $exception)
        {
            return $this->json([
                'status' => 400,
                'message' => $exception->getMessage()
            ], 400);
        }
    }
}
