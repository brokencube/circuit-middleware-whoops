# Whoops Middleware for Circuit

[![Latest Stable Version](https://poser.pugx.org/brokencube/circuit-middleware-whoops/v/stable)](https://packagist.org/packages/brokencube/circuit-middleware-whoops) 

Middleware to add Whoops error handling to a site using Circuit.
(Glibly stolen and altered from https://github.com/franzliedke/whoops-middleware)

## Basic Usage
```
$router->registerMiddleware('whoops', new \CircuitMiddleware\Whoops\Whoops);
$router->addPrerouteMiddleware('whoops');
```
