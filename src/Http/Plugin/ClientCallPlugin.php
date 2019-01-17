<?php

namespace Shapecode\HTTPCall\Http\Plugin;

use Http\Client\Common\Plugin;
use Http\Client\Exception;
use Http\Promise\Promise;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Shapecode\HTTPCall\Model\ClientCall;

/**
 * Class ClientCallPlugin
 *
 * @package Shapecode\HTTPCall\Http\Plugin
 * @author  Nikita Loges
 */
class ClientCallPlugin implements Plugin
{

    /** @var ClientCall */
    protected $call;

    /**
     * @param ClientCall $call
     */
    public function __construct(ClientCall $call)
    {
        $this->call = $call;
    }

    /**
     * {@inheritdoc}
     */
    public function handleRequest(RequestInterface $request, callable $next, callable $first): Promise
    {
        $this->call->setRequest($request);

        return $next($request)->then(function (ResponseInterface $response) {
            $this->call->setResponse($response);

            return $response;
        }, function (Exception $exception) {
            if ($exception instanceof Exception\HttpException) {
                $this->call->setResponse($exception->getResponse());
            }

            throw $exception;
        });
    }
}
