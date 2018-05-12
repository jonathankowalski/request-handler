<?php

namespace Openjk\RequestHandler\Tests;

use GuzzleHttp\Psr7\ServerRequest;
use Openjk\RequestHandler;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RealLifeTest extends TestCase
{

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var ServerRequestInterface
     */
    private $request;

    public function setUp()
    {
        $this->response = $this->createMock(ResponseInterface::class);
        $this->request = $this->createMock(ServerRequestInterface::class);
    }

    public function testContent()
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getBody')->willReturn('coucou');
        $handler = new RequestHandler($this->response);
        $handler->pipe(new class($response) implements MiddlewareInterface {
            private $response;
            public function __construct($response)
            {
                $this->response = $response;
            }

            public function process(
                ServerRequestInterface $request,
                RequestHandlerInterface $handler
            ): ResponseInterface {
                return $this->response;
            }
        });
        $response = $handler->handle($this->request);
        $this->assertEquals('coucou', (string) $response->getBody());
    }

    public function testWriteAttribute()
    {
        $response = $this->createMock(ResponseInterface::class);
        $handler = new RequestHandler($this->response);
        $handler->pipe(new class($response) implements MiddlewareInterface {
            private $response;
            public function __construct($response)
            {
                $this->response = $response;
            }

            public function process(
                ServerRequestInterface $request,
                RequestHandlerInterface $handler
            ): ResponseInterface {
                $this->response->request = $request;
                return $this->response;
            }
        });
        $handler->pipe(new class implements MiddlewareInterface {
            public function process(
                ServerRequestInterface $request,
                RequestHandlerInterface $handler
            ): ResponseInterface {
                $request->alt = 'coucou';
                return $handler->handle($request);
            }
        });

        $handler->pipe(new class implements MiddlewareInterface {
            public function process(
                ServerRequestInterface $request,
                RequestHandlerInterface $handler
            ): ResponseInterface {
                $request->alt2 = 'yhouou';
                return $handler->handle($request);
            }
        });
        $response = $handler->handle($this->request);
        $this->assertSame($this->request, $response->request);
        $this->assertEquals('coucou', $response->request->alt);
        $this->assertEquals('yhouou', $response->request->alt2);
    }
}