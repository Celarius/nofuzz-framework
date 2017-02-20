# Documentation

**Table of Contents**
* Getting started
  * Order of loaded files
  * Routes
  * Controllers
  * Middleware
  * Database connections
  * Global functions
  * Helpers
    * JWT - JSON Web Token
* Configuring

---
# Getting started

## Order of loaded files
Order of autoloading:
```txt
/public/bootstrap.php     Loaded by PHP engine when request comes
/vendor/autoload.php      by bootstrap.php | All composer related autoloads
/Nofuzz/Application.php   by bootstrap.php | Nofuzz application init
/Nofuzz/Globals.php       by \nofuzz\Loader.php | Nofuzz globals & helpers
/app/globals.php          by bootstrap.php | Application globals & helpers
/app/Middleware/*         The Route Group-defined Before Middleware (if any)
/app/Controllers/*        The route-defined controller to handle request
/app/Middleware/*         The Route Group-defined After Middleware (if any)
```

## Routes
Routes are the heart of the application. App developers define the routes that map to Controllers, with Middlewares before and after.

All routes are defined in [routes.json](src/Config/routes.json). The reason this is a JSON file is that 3rd party applications/installers etc. can modify the file easily without any other knowledge than JSON and the structure.


## Controllers
Each route maps to a Controller, and optionally to a method.

Routes can define specific HTTP Methods to react on, but if omitted all methods are routed to the controller.

If the method name is omitted from the `handler` a default `handle()` is called that then further calls `handleGET()`, `handlePOST()` etc.

The framework will automatically call a controllers `initialize()` method before anythng else. 


## Middleware
Nofuzz has two types of Middleware: `Before` and `After`. As the names indicate the _Before Middlewares_ are called before the Controller handles  the request, and the _After Middlewares_ are called after the Controller has handled the request (regardless of outcome of Controller handler).

All user-defined Middleware must override the `handle()` method to perform actions in the Middleware.


## Database connections
Database connections are handeled by the Connection Manager, that stores all connections to all databases. An application can open any number of connections to different databases.

To obtain a connection to a database, a `db('<name_of_connection');` is issued. This will look in the `config/config.json` file in the `connections` section to identify the Connection and instanciate it. 


## Global functions
The following are predefined global functions usable anywhere in the code. These are defined in the `\Nofuzz\Globals.php` file.
```php
function env(string $var, $default=null)
function app(string $property=null)
function config(string $key=null)
function logger()
function db(string $connectionName)
function cache(string $driverName='')
function request()
function queryParam(string $paramName)
function postParam(string $paramName)
function cookieParam(string $paramName)
function request()
```


## Helpers
Helpers are usually static classes that can be used throughout the app to perform functions. The following helpers exist:
* Cipher - OpenSSL Encryption/Decryption
* Hash - OpenSSL Message Digests
* JWT - [Json Web Tokens](http://jwt.io) 
