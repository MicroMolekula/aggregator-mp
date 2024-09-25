<?php

namespace App\Factory;

use App\Dto\ProductDto;

class ProductFactory
{
    public function oneFromArray(array $data, string $mpType): ProductDto
    {
        return new ProductDto(
            $data['article'],
            $data['name'],
            $data['brand'],
            $data['image'],
            $data['price'],
            $data['oldPrice'],
            $data['percentSale'],
            $data['link'],
            $mpType,
        );
    }

    public function allFromArray(array $data, string $mpType): array
    {
        $products = [];
        foreach ($data as $item) {
            $products[] = $this->oneFromArray($item, $mpType);
        }
        return $products;
    }

}