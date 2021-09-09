<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

class CustomerController extends AbstractController
{
    /**
     * Récupère la liste des clients
     *
     * @Route("/api/customer", name="customer_list", methods={"GET"})
     * @OA\Response(
     *     response=200,
     *     description="Return all the customers find in the database",
     *     @OA\JsonContent(
     *      type="array",
     *      @OA\Items(ref=@Model(type=Customer::class, groups={"get:list"}))
     *      )
     * )
     * @OA\Tag(name="Customer")
     */
    public function list(CustomerRepository $customerRepository, CacheInterface $cache): Response
    {
        $customer = $cache->get('list', function(ItemInterface $item) use ($customerRepository):array
        {
            $item->expiresAfter(3600);
            return $customerRepository->findAll();
        });

        return $this->json($customer, 200, [], ['groups' => 'get:list']);
    }

    /**
     * Récupère les détails d'un client
     *
     * @Route("/api/customer/{id}", name="customer_detail", methods={"GET"})
     * @OA\Response(
     *     response=200,
     *     description="Returns the informations about the customer ID",
     *     @OA\JsonContent(
     *      type="array",
     *      @OA\Items(ref=@Model(type=Customer::class, groups={"get:detail"}))
     *      )
     * )
     * @OA\Response(
     *     response=404,
     *     description="Customer not found.",
     * )
     * @OA\Tag(name="Customer")
     */
    public function detail(?Customer $customer)
    {
        if($customer === null)
        {
            return $this->json([
                'status' => 404,
                'message' => 'Customer not found'
            ], 404);
        }

        return $this->json($customer, 200, [], ['groups' => 'get:detail']);
    }

    /**
     * Permet de savoir la liste des utilisateurs lié à un client
     *
     * @Route("/api/customer/{id}/list", name="customer_user", methods={"GET"})
     * @OA\Response(
     *     response=200,
     *     description="Returns the informations about the users affiliates to the customer ",
     *     @OA\JsonContent(
     *      type="array",
     *      @OA\Items(ref=@Model(type=Customer::class, groups={"get:detail"}))
     *      )
     * )
     * @OA\Parameter(
     *     name="page",
     *     in="query",
     *     description="Enter a page number : ?page=number",
     *     @OA\Schema(type="integer")
     * )
     * @OA\Parameter(
     *     name="limit",
     *     in="query",
     *     description="Number of users by page",
     *     @OA\Schema(type="int", default = 5)
     * )
     * @OA\Response(
     *     response=404,
     *     description="Customer not found.",
     * )
     * @OA\Tag(name="Customer")
     */
    public function userList(Customer $customer, UserRepository $userRepository, PaginatorInterface $paginator,
                             Request $request, CacheInterface $cache)
    {
        $users = $cache->get('userList', function(ItemInterface $item) use ($userRepository, $customer):array
        {
            $item->expiresAfter(3600);
            return $userRepository->findBy(['customerId'=>$customer->getId()]);
        });

        $users = $paginator->paginate(
            $users,
            $request->query->getInt('page', 1)/*page number*/,
            5/*limit per page*/
        );

        return $this->json($users, 200, [], ['groups'=>'get:userList']);
    }

    /**
     * Permet d'ajouter un nouvel utilisateur
     *
     * @Route("/api/customer/add", name="customer_post", methods={"POST"})
     * @OA\RequestBody(
     *     description="The new customer to create",
     *     required=true,
     *     @OA\MediaType(
     *         mediaType="application/Json",
     *         @OA\Schema(
     *             type="object",
     *             @OA\Property(
     *                 property="email",
     *                 description="Username for user identification",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 description="Add password",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="compagny",
     *                 description="Compagny name",
     *                 type="string"
     *             ),
     *         )
     *     )
     * )
     * @OA\Response(
     *     response=JsonResponse::HTTP_CREATED,
     *     description="Create a customer and returns it"
     * )
     * @OA\Response(
     *     response=JsonResponse::HTTP_BAD_REQUEST,
     *     description="Bad Json syntax or incorrect data"
     * )
     * @OA\Response(
     *     response=JsonResponse::HTTP_UNAUTHORIZED,
     *     description="Unauthorized request"
     * )
     * @OA\Tag(name="Customer")
     */
    public function add(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager,
                        ValidatorInterface $validator, UserPasswordEncoderInterface $encoder)
    {
        $customer = new Customer();
        $plainPassword = "password";
        $encoded = $encoder->encodePassword($customer, $plainPassword);

        try
        {
            $customer = $serializer->deserialize($request->getContent(), Customer::class, 'json');

            $customer->setCreatedAt(new \DateTimeImmutable());
            $customer->setPassword($encoded);

            $errors = $validator->validate($customer);

            if(count($errors) > 0)
            {
                return $this->json($errors, 400);
            }

            $entityManager->persist($customer);
            $entityManager->flush();

            return $this->json($customer, 201, [], ['groups' => 'get:detail']);
        }

        catch (NotEncodableValueException $exception)
        {
            return $this->json([
                'status' => 400,
                'message' => $exception->getMessage()
            ], 400);
        }
    }
}
