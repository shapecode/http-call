<?php

namespace Shapecode\HTTPCall\Client;

use Http\Client\Common\Plugin\StopwatchPlugin;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Message\RequestFactory;
use Shapecode\HTTPCall\Http\Plugin\ClientCallPlugin;
use Shapecode\HTTPCall\Model\ClientCall;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Class HttpClient
 *
 * @package Shapecode\HTTPCall\Client
 * @author  Nikita Loges
 */
class HttpClient
{

    /** @var RequestFactory */
    protected $requestFactory;

    /** @var ClientFactory */
    protected $clientFactory;

    /**
     * @param ClientFactory $clientFactory
     */
    public function __construct(ClientFactory $clientFactory)
    {
        $this->requestFactory = MessageFactoryDiscovery::find();
        $this->clientFactory = $clientFactory;
    }

    /**
     * @param       $method
     * @param       $url
     * @param array $options
     * @param array $plugins
     *
     * @return ClientCall
     */
    public function createCall($method, $url, array $options = [], array $plugins = []): ClientCall
    {
        $headers = [];
        if (isset($options['headers'])) {
            $headers = $options['headers'];
            unset($options['headers']);
        }

        if (isset($options['user_agent']) && !isset($headers['User-Agent'])) {
            $headers['User-Agent'] = $options['user_agent'];
        }

        $request = $this->requestFactory->createRequest($method, $url, $headers);

        return new ClientCall($request, $options, $plugins);
    }

    /**
     * @param       $method
     * @param       $url
     * @param array $options
     * @param array $plugins
     *
     * @return ClientCall
     */
    public function call($method, $url, array $options = [], array $plugins = []): ClientCall
    {
        $call = $this->createCall($method, $url, $options, $plugins);
        $this->submit($call);

        return $call;
    }

    /**
     * @param ClientCall $call
     */
    public function submit(ClientCall $call): void
    {
        $plugins = $call->getPlugins();

        $stopwatch = new Stopwatch();
        $plugins[] = new StopwatchPlugin($stopwatch);
        $plugins[] = new ClientCallPlugin($call);

        $client = $this->clientFactory->createClient($call->getOptions(), $plugins);
        $client->sendRequest($call->getRequest());

        $duration = 0;
        $memory = 0;
        foreach ($stopwatch->getSections() as $section) {
            foreach ($section->getEvents() as $event) {
                $duration += $event->getDuration();
                $memory += $event->getMemory();
            }
        }

        $call->setDuration($duration);
        $call->setMemory($memory);
    }
}
