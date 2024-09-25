<?php

namespace App\Service;

use App\Client\WildberriesClient;
use App\Factory\ProductFactory;

class WildberriesParserService
{
    public function __construct(
        private WildberriesClient $wildberriesClient,
        private ProductFactory $productFactory,
    ) {
    }

    public function getSearchProducts(string $search, int $page = 1): array
    {
        $productsArray = $this->wildberriesClient->getProductsArrayBySearch($search, $page);
        return $this->productFactory->allFromArray($productsArray, 'wb');
    }

}