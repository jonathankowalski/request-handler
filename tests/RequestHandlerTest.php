<?php

namespace JK\RequestHandler\Tests;

use JK\RequestHandler;
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
     * @throws \ReflectionException
     */
    public function setUp()
    {
        $this->response = $this->createMock(ResponseInterface::class);
        $this->request = $this->createMock(ServerRequestInterface::class);
        $this->middleware = $this->createMock(MiddlewareInterface::class);
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
}