<?php

namespace App\Client;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

class OzonClient
{
    private RemoteWebDriver $driver;

    public function __construct(
        private Client $client,
        private string $url,
    ) {
        $this->driver = $this->client->getDriver();
    }

    public function getSearchUrl(string $text): string
    {
        $this->driver->get($this->url);
        $this->driver->wait()->until(function () use ($text) {
            try {
                $this->driver->findElement(WebDriverBy::cssSelector('.search-bar-wrapper'));
                return true;
            } catch (\Throwable $e) {
                return false;
            }
        });
        $form = $this->driver->findElement(WebDriverBy::cssSelector('.search-bar-wrapper'));
        dd($form);
    }
}