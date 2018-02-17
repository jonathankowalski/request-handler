<?php

namespace JK;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestHandler implements RequestHandlerInterface
{

    /**
     * @var MiddlewareInterface[]
     */
    protected $middlewares = [];
    /**
     * @var MiddlewareInterface[]
     */
    private $reverseMiddlewares = [];
    private $once = false;

    /**
     * @var ResponseInterface
     */
    protected $response;

    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    public function pipe(MiddlewareInterface $middleWare): RequestHandler
    {
        $this->middlewares [] = $middleWare;
        return $this;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (!$this->once) {
            $this->reverseMiddlewares = array_reverse($this->middlewares);
            $this->once = true;
        }
        $middleware = $this->getNextMiddleWare();
        if (!$middleware) {
            return $this->response;
        }
        return $middleware->process($request, $this);
    }

    /**
     * @return null|MiddlewareInterface
     */
    protected function getNextMiddleWare()
    {
        return array_pop($this->reverseMiddlewares);
    }
}
