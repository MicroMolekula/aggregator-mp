<?php

namespace App\Client;

use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverKeys;

class WildberriesClient
{
    private RemoteWebDriver $driver;

    public function __construct(
        private Client $client,
        private string $url,
    ) {
        $this->driver = $this->client->getDriver();
    }

    public function getCountProductsBySearch(string $text, int $page = 1): int
    {
        try {
            $this->driver->get($this->url . urlencode($text) . '&page=' . $page);
            $this->driver->wait()->until(
                function () {
                    $productHtml = $this->driver->findElements(WebDriverBy::cssSelector('.product-card__wrapper'));
                    if (empty($productHtml)) {
                        return false;
                    }
                    return true;
                }
            );
            $countHtml = $this->driver->findElement(WebDriverBy::cssSelector('.searching-results__count'));
            $countText = str_replace(' товаров найдено', '', $countHtml->getText());
            return (int)implode('', explode(' ', $countText));
        } catch (NoSuchElementException $e) {
            return 0;
        }
    }

    public function getProductsArrayBySearch(string $text, int $page = 1): array
    {
        $this->driver->get($this->url . urlencode($text) . '&page=' . $page);
        $this->waitLoadAllProducts();
        $productsHtml = $this->driver->findElements(WebDriverBy::cssSelector('.product-card__wrapper'));
        $productsArray = [];

        foreach ($productsHtml as $productHtml) {
            $productsArray[] = [
                'brand' => $this->getProductBrand($productHtml),
                'name' => $this->getProductName($productHtml),
                'article' => $this->getProductArticle($productHtml),
                'image' => $this->getProductsImage($productHtml),
                'price' => $this->getProductPrice($productHtml),
                'oldPrice' => $this->getPriceWithoutSale($productHtml),
                'percentSale' => $this->getPercentSale($productHtml),
                'link' => $this->getLinkProduct($productHtml),
            ];
        }

        return $productsArray;
    }

    private function waitLoadAllProducts()
    {
        while (true) {
            try {
                $productsHtml = $this->driver->findElements(WebDriverBy::cssSelector('.product-card__wrapper'));
                if (count($productsHtml) === 100) {
                    break;
                } else {
                    $this->driver->getKeyboard()->sendKeys(WebDriverKeys::PAGE_DOWN);
                }
            } catch (\Exception $e) {
                $this->driver->wait(2);
            }
        }
    }

    private function getProductName(RemoteWebElement $htmlElement): string
    {
        $product = $htmlElement
            ->findElement(WebDriverBy::cssSelector('.product-card__name'))
            ->getText();
        return str_replace('/ ', '', $product);
    }

    private function getProductBrand(RemoteWebElement $htmlElement): string
    {
        return $htmlElement
            ->findElement(WebDriverBy::cssSelector('.product-card__brand'))
            ->getText();
    }

    private function getProductArticle(RemoteWebElement $htmlElement): string
    {
        $link = $htmlElement
            ->findElement(WebDriverBy::cssSelector('.product-card__link'))
            ->getAttribute('href');
        return explode('/',$link)[4];
    }

    private function getProductsImage(RemoteWebElement $htmlElement): string
    {
        return $htmlElement
            ->findElement(WebDriverBy::cssSelector('.product-card__img-wrap'))
            ->findElement(WebDriverBy::tagName('img'))
            ->getAttribute('src');
    }

    private function getProductPrice(RemoteWebElement $htmlElement): int
    {
         $price = $htmlElement
            ->findElement(WebDriverBy::cssSelector('.product-card__price'))
            ->findElement(WebDriverBy::cssSelector('.price__lower-price'))
            ->getText();
         return (int)implode(explode(' ', $price));
    }

    private function getPriceWithoutSale(RemoteWebElement $htmlElement): int
    {
        try {
            $oldPrice = $htmlElement
                ->findElement(WebDriverBy::cssSelector('.product-card__price'))
                ->findElement(WebDriverBy::cssSelector('.price__wrap'))
                ->findElement(WebDriverBy::tagName('del'))
                ->getText();
            return (int)implode(explode(' ', $oldPrice));
        } catch (NoSuchElementException $e) {
            return $this->getProductPrice($htmlElement);
        }
    }

    private function getPercentSale(RemoteWebElement $htmlElement): string
    {
        try {
            return $htmlElement
                ->findElement(WebDriverBy::cssSelector('.product-card__tip--sale'))
                ->getText();
        } catch (NoSuchElementException $e) {
            return "-0%";
        }
    }

    private function getLinkProduct(RemoteWebElement $htmlElement): string
    {
        return $htmlElement
            ->findElement(WebDriverBy::cssSelector('.product-card__link'))
            ->getAttribute('href');
    }

    public function __destruct()
    {
        $this->driver->quit();
    }
}