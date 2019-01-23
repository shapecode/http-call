<?php

namespace Shapecode\HTTPCall\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request as Psr7Request;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use Http\Client\Common\Plugin\ContentLengthPlugin;
use Http\Client\Common\Plugin\LoggerPlugin;
use Http\Client\Common\Plugin\RedirectPlugin;
use Http\Client\Common\Plugin\RetryPlugin;
use Http\Client\Common\PluginClient;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Message\RequestFactory;
use Psr\Log\LoggerInterface;

/**
 * Class ClientFactory
 *
 * @package Shapecode\HTTPCall\Client
 * @author  Nikita Loges
 */
class ClientFactory
{

    /** @var LoggerInterface|null */
    protected $logger;

    /** @var RequestFactory */
    protected $requestFactory;

    protected const MAX_RETRIES = 4;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
        $this->requestFactory = MessageFactoryDiscovery::find();
    }

    /**
     * @inheritdoc
     */
    public function createClient(array $options = [], array $plugins = []): PluginClient
    {
        $client = $this->createRawClient($options);

        $plugins[] = new ContentLengthPlugin();

        if ($this->logger) {
            $plugins[] = new LoggerPlugin($this->logger);
        }

        $plugins[] = new RedirectPlugin();
        $plugins[] = new RetryPlugin([
            'retries' => self::MAX_RETRIES
        ]);

        return new PluginClient($client, $plugins);
    }

    /**
     * @inheritdoc
     */
    protected function createRawClient(array $options = []): GuzzleAdapter
    {
        $options['http_errors'] = false;
        $options['allow_redirects'] = true;

        if (!isset($options['timeout'])) {
            $options['timeout'] = 5;
        }

        return GuzzleAdapter::createWithConfig($options);
    }
}
