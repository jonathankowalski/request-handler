# RequestHandler

Provide a simple implements of PSR-15 RequestHandlerInterface

## Installation

```
composer require jk/request-handler
```

## Usage

```php

$requestHandler = new RequestHandler;
$requestHandler->pipe($MiddlewareInteface);
$ResponseInterface = $requestHandler->handle($ServerRequestInterface)

```