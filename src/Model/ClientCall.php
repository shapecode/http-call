<?php

namespace Shapecode\HTTPCall\Model;

use Http\Client\Common\Plugin;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class ClientCall
 *
 * @package Shapecode\HTTPCall\Model
 * @author  Nikita Loges
 */
class ClientCall
{

    /** @var RequestInterface */
    protected $request;

    /** @var ResponseInterface */
    protected $response;

    /** @var float|null */
    protected $duration;

    /** @var float|null */
    protected $memory;

    /** @var Plugin[] */
    protected $plugins = [];

    /** @var array */
    protected $options = [];

    /** @var mixed */
    protected $content;

    /**
     * @param RequestInterface $request
     * @param array            $options
     * @param array            $plugins
     */
    public function __construct(RequestInterface $request, array $options = [], array $plugins = [])
    {
        $this->request = $request;
        $this->options = $options;

        foreach ($plugins as $plugin) {
            $this->addPlugin($plugin);
        }
    }

    /**
     * @return RequestInterface
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    /**
     * @param RequestInterface $request
     */
    public function setRequest(RequestInterface $request): void
    {
        $this->request = $request;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * @param ResponseInterface $response
     */
    public function setResponse(ResponseInterface $response): void
    {
        $this->response = $response;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        if ($this->content === null) {
            $this->content = $this->getResponse()->getBody()->getContents();
        }

        return $this->content;
    }

    /**
     * @return array
     */
    public function getPlugins(): array
    {
        return $this->plugins;
    }

    /**
     * @param Plugin $plugin
     */
    public function addPlugin(Plugin $plugin): void
    {
        $this->plugins[] = $plugin;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @return float|null
     */
    public function getDuration(): ?float
    {
        return $this->duration;
    }

    /**
     * @param float|null $duration
     */
    public function setDuration(?float $duration): void
    {
        $this->duration = $duration;
    }

    /**
     * @return float|null
     */
    public function getMemory(): ?float
    {
        return $this->memory;
    }

    /**
     * @param float|null $memory
     */
    public function setMemory(?float $memory): void
    {
        $this->memory = $memory;
    }
}
