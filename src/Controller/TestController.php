<?php

namespace App\Controller;

use App\Client\OzonClient;
use App\Service\WildberriesParserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class TestController extends AbstractController
{
    public function __construct(
        private WildberriesParserService $wildberriesParserService,
        private OzonClient $ozonClient,
    ) {
    }

    #[Route('/api/test', name: 'app_test_contoller', methods: ['POST'])]
    public function index(Request $request): JsonResponse
    {
        $this->ozonClient->getSearchUrl('');
        return $this->json([]);
    }
}
