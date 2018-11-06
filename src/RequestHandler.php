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
    private $nextMiddlewareFn;
    private $shouldInit = true;
    private $init = 0;

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
        $this->shouldInit = $this->initHandle($this->shouldInit);
        $middleware = $this->getNextMiddleware($this->nextMiddlewareFn, $this->indexMiddleware++);
        if ($middleware instanceof MiddlewareInterface) {
            return $middleware->process($request, $this);
        }
        return $this->defaultResponse;
    }

    /**
     * @return boolean
     */
    private function initHandle($test)
    {
        return $test
            && $this->init(
                $this->middlewares,
                function ($middlewares) {
                    return function ($index) use ($middlewares) {
                        return $middlewares[$index] ?? null;
                    };
                }
            );
    }

    /**
     * @return boolean
     */
    private function init(array $middlewares, Callable $getNextMiddlewareFn)
    {
        $this->nextMiddlewareFn = $getNextMiddlewareFn(
            array_reverse($middlewares)
        );
        return false;
    }

    private function getNextMiddleware(Callable $getNextMiddlewareFn, $index) : ?MiddlewareInterface
    {
        return $getNextMiddlewareFn($index);
    }
}
