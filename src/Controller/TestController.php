<?php

namespace App\Controller;

use App\Service\WildberriesParserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class TestController extends AbstractController
{
    public function __construct(
        private WildberriesParserService $wildberriesParserService,
    ) {
    }

    #[Route('/api/test', name: 'app_test_contoller', methods: ['POST'])]
    public function index(Request $request): JsonResponse
    {
        $request = $request->toArray();
        $searchText = $request['search'];
        $page = $request['page'] ??  1;
        $productsArray = $this->wildberriesParserService->getSearchProducts($searchText, $page);
        return $this->json($productsArray);
    }
}
