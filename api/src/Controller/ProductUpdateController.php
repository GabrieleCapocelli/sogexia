<?php

namespace App\Controller;

use App\Dto\ProductDto;
use App\Entity\Product;
use App\Enum\Status;
use App\Service\ApiErrorService;
use App\Service\ApiSecurityService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/v1/products/{id}', name: 'update_product', methods: ['PUT'])]
class ProductUpdateController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private ApiSecurityService $apiSecurity;
    private ApiErrorService $apiErrorService;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;

    public function __construct(
        EntityManagerInterface $entityManager,
        ApiSecurityService $apiSecurity,
        ApiErrorService $apiErrorService,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {
        $this->entityManager = $entityManager;
        $this->apiSecurity = $apiSecurity;
        $this->apiErrorService = $apiErrorService;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    public function __invoke(Product $product, Request $request): JsonResponse
    {
        $route = '/products/' . $product->getId();

//        // 1. Verify auth and signature
        $validationError = $this->apiSecurity->validateRequest($request, $route);
        if ($response = $this->apiErrorService->createErrorResponse($validationError)) {
            return $response;
        }

        // 2. Mapping and validating DTO
        $productDto = $this->serializer->deserialize($request->getContent(), ProductDto::class, 'json');
        $errors = $this->validator->validate($productDto);
        if (count($errors) > 0) {
            return $this->apiErrorService->createErrorResponse($errors);
        }

        // 3. Updating product from DTO
        $product->setName($productDto->name);
        $product->setDescription($productDto->description);
        $product->setPrice($productDto->price);
        $product->setStatus(Status::from($productDto->status));
        $product->setStockSold($productDto->stock->sold);
        $product->setStockAvailable($productDto->stock->available);

        $this->entityManager->flush();

        // 4. Signed Response
        $responseBody = [
            'status' => $product->getStatus()->value,
            'price' => $product->getPrice(),
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'stock' => [
                'sold' => $product->getStockSold(),
                'available' => $product->getStockAvailable(),
            ],
        ];

        $signed = $this->apiSecurity->buildSignedResponse($responseBody, $route, $request->getMethod());

        return new JsonResponse($signed['body'], 200, $signed['headers']);
    }
}
