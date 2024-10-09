<?php

namespace App\Client;

use Facebook\WebDriver\Chrome\ChromeDevToolsDriver;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

class Client
{
    protected const USER_AGENTS = [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:124.0) Gecko/20100101 Firefox/124.0',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36 Edg/91.0.864.59',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
    ];

    protected RemoteWebDriver $driver;

    public function __construct(
        private string $seleniumUrl,
    ) {
        $host = $this->seleniumUrl;
        $capabilities = DesiredCapabilities::chrome();
        $chromeOptions = new ChromeOptions();
        $chromeOptions->addArguments(['--headless']);

        $capabilities->setCapability(ChromeOptions::CAPABILITY, $chromeOptions);

        $this->driver = RemoteWebDriver::create($host, $capabilities);

        $devTools = new ChromeDevToolsDriver($this->driver);
        $devTools->execute(
            'Network.setUserAgentOverride',
            ['userAgent' => self::USER_AGENTS[random_int(0, count(self::USER_AGENTS) - 1)]],
        );
    }

    public function getDriver(): RemoteWebDriver
    {
        return $this->driver;
    }

    public function close(): void
    {
        $this->driver->quit();
    }
}