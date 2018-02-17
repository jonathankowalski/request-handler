# RequestHandler

[![Build Status](https://travis-ci.org/jonathankowalski/request-handler.svg?branch=master)](https://travis-ci.org/jonathankowalski/request-handler)

Provide a simple implements of PSR-15 RequestHandlerInterface

## Installation

```
composer require openjk/request-handler
```

## Usage

```php

$requestHandler = new RequestHandler;
$requestHandler->pipe($MiddlewareInteface);
$ResponseInterface = $requestHandler->handle($ServerRequestInterface)

```