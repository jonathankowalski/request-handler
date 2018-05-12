<?php

namespace Openjk;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestHandler implements RequestHandlerInterface
{

    /**
     * @var MiddlewareInterface[]
     */
    private $middlewares = [];
    private $indexMiddleware = 0;

    /**
     * @var ResponseInterface
     */
    protected $defaultResponse;

    public function __construct(ResponseInterface $defaultResponse)
    {
        $this->defaultResponse = $defaultResponse;
    }

    public function pipe(MiddlewareInterface $middleWare): RequestHandler
    {
        $this->middlewares[] = $middleWare;
        return $this;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = $this->getNextMiddlewareFunction(array_reverse($this->middlewares))($this->indexMiddleware++);
        if ($middleware instanceof MiddlewareInterface) {
            return $middleware->process($request, $this);
        }
        return $this->defaultResponse;
    }

    private function getNextMiddlewareFunction(array $middlewares = []): \Closure
    {
        return function ($index) use ($middlewares) {
          return $middlewares[$index] ?? null;
        };
    }
}
