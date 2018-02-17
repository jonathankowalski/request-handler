# RequestHandler

[![Build Status](https://travis-ci.org/jonathankowalski/request-handler.svg?branch=master)](https://travis-ci.org/jonathankowalski/request-handler) [![Coverage Status](https://coveralls.io/repos/github/jonathankowalski/request-handler/badge.svg?branch=master)](https://coveralls.io/github/jonathankowalski/request-handler?branch=master)

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