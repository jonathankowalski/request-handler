<?php

namespace Openjk\RequestHandler\Tests;

use Openjk\RequestHandler;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;

class RequestHandlerTest extends TestCase
{
    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @var ServerRequestInterface
     */
    protected $request;

    /**
     * @var MiddlewareInterface
     */
    protected $middleware;

    /**
     * @var MiddlewareInterface
     */
    protected $middlewareWithFirstException;

    /**
     * @var MiddlewareInterface
     */
    protected $middlewareWithSecondException;

    /**
     * @throws \ReflectionException
     */
    public function setUp()
    {
        $this->response = $this->createMock(ResponseInterface::class);
        $this->request = $this->createMock(ServerRequestInterface::class);
        $this->middleware = $this->createMock(MiddlewareInterface::class);
        $this->middlewareWithFirstException = $this->createMock(MiddlewareInterface::class);
        $this->middlewareWithFirstException
            ->method('process')
            ->willThrowException(new \Exception('', 1));
        $this->middlewareWithSecondException = $this->createMock(MiddlewareInterface::class);
        $this->middlewareWithSecondException
            ->method('process')
            ->willThrowException(new \Exception('', 2));
    }

    public function testNoMiddlewares()
    {
        $handler = new RequestHandler($this->response);
        $this->assertEquals($this->response, $handler->handle($this->request));
    }

    public function testAddMiddleware()
    {
        $handler = new RequestHandler($this->response);
        $this->assertInstanceOf(RequestHandler::class, $handler->pipe($this->middleware));
        $this->assertInstanceOf(ResponseInterface::class, $handler->handle($this->request));
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionCode 2
     */
    public function testOrderMiddleWare()
    {
        $handler = new RequestHandler($this->response);
        $handler
            ->pipe($this->middlewareWithFirstException)
            ->pipe($this->middlewareWithSecondException)
        ;
        $handler->handle($this->request);
    }
}