<?php

namespace App\Controller;

use App\Entity\Product;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;


class ProductController extends AbstractController
{
    /**
     * Récupère la liste des produits
     *
     * @Route("/api/product", name="product_list", methods={"GET"})
     * @OA\Response(
     *     response=200,
     *     description="Return all the product found in the database",
     *     @OA\JsonContent(
     *      type="array",
     *      @OA\Items(ref=@Model(type=Product::class, groups={"get:list"}))
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
     *     description="Number of items by page",
     *     @OA\Schema(type="int", default = 5)
     * )
     * @OA\Tag(name="Product")
     */
    public function list(ProductRepository $productRepository, PaginatorInterface $paginator, Request $request, CacheInterface $cache): Response
    {
        $product = $cache->get('list', function (ItemInterface $item) use ($productRepository):array
        {
            $item->expiresAfter(3600);
            return $productRepository->findAll();
        });

        $product = $paginator->paginate(
            $product,
            $request->query->getInt('page', 1),
            5
        );

        return $this->json($product, 200, [], ['groups'=>'get:list']);
    }

    /**
     * Récupère les détails d'un client
     *
     * @Route("/api/product/{id}", name="product_detail", methods={"GET"})
     * @OA\Response(
     *     response=200,
     *     description="Returns the informations about the product ID",
     *     @OA\JsonContent(
     *      type="array",
     *      @OA\Items(ref=@Model(type=Product::class))
     *      )
     * )
     * @OA\Response(
     *     response=404,
     *     description="Product not found.",
     * )
     * @OA\Tag(name="Product")
     */
    public function detail(?Product $product)
    {
        if($product === null)
        {
            return $this->json([
                'status' => 404,
                'message' => 'Product not found'
            ], 404);
        }

        return $this->json($product, 200, []);
    }
}
