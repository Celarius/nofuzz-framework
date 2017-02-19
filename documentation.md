# Documentation

**Table of Contents**
* Getting started
  * How Nofuzz works
  * Order of loaded files
  * Routes
  * Controllers
  * Middleware
  * Database connections
* Global functions
* Helpers
  * JWT - JSON Web Token

* Configuring

* Examples
  * Controller
  * Middleware
  * Database

* Roadmap

---
# Getting started

## How Nofuzz works
<<TBD>>

## Order of loaded files
Order of autoloading:
```txt
/public/bootstrap.php     Loaded by PHP engine when request comes
/vendor/autoload.php      by bootstrap.php | All composer related autoloads
/Nofuzz/Loader.php        by bootstrap.php | Nofuzz application init
/Nofuzz/Globals.php       by \nofuzz\Loader.php | Nofuzz globals & helpers
/app/globals.php          by bootstrap.php | Application globals & helpers
/app/Middleware/*         The Route Group-defined Middleware (if any)
/app/Controllers/*        The route-defined controller to handle request
```

## Routes
Routes are the heart of the application. App developers define the routes that map to Controllers.

## Controllers
Each route will map to a Controller. The namespace for controllers is `App\Controllers\<ControllerClass>` by default, but this can be changed to anything that suits the developers.
Each Controller class extends `\Nofuzz\Controller`.

When a Controller is mapped in `routes.json` the framework will automatically call the `initialize()` method before anythng else. Afterh this the `handle()` method is called. This method will then call the appropriate `handleGET`, `handlePOST` etc. methods in the controller to handle the different HTTP Methods.

## Middleware
Nofuzz has two types of Middleware: `Before` and `After`. As the names indicate the _Before Middlewares_ are called before the Controller gains access to the request, and the _After Middlewares_ are called after the Controller has handled the request (regardless of outcome of Controller handler).

All user-defined Middleware must override the `handle()` method to perform actions in the Middleware.

Client --[request]--> "BeforeMiddleware > Controller > AfterMiddleware".

### Before Middleware
If a Before Middleware does not return `bool True` from the handler, the request is not processed further by other Middlewares or Controllers.

### After Middleware
The results from After Middleware handle() calls are ignored and all Middlewares are processed, even if Before Middleware or Controller returns false.

## Database connections
Database connections are handeled by the Connection Manager, that stores all connections to all databases. An application can open any number of connections to different databases.

To obtain a connection to a database, a `db('<name_of_connection');` is issued. This will look in the `config/config.json` file in the `connections` section to identify the Connection and instanciate it. 


---
# Configuring


---
# Global functions
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


---
# Helpers
Helpers are usually static classes that can be used throughout the app to perform functions.

## JWT - JSON Web Token
Support for the JSON Web Tokens. `JWT:encode()` and `JWT::decode()` methods are provided.


---
# Examples

## Controller
All Controllers extend from the `\Nofuzz\Controller` class. This class has default handlers for all the HTTP methods that need to be overridden in the descendant class.

### HealthController
The following example is a HealthController designed to respond to requests to `http://domain.com/health`.
#### routes.json
```json
  "default": {
    "routes": [
      { "path":"/health", "handler":"\\App\\Controllers\\HealthController" }
    ]
  }
```
The framework would look for the HealthController class file at `app\Controllers\HealthController.php`.

#### HealthController.php
```php
<?php namespace App\Controllers;

class HealthController extends \NoFuzz\Controller
{
  /**
   * Handle GET requests
   * 
   * @param array $args   Path variables as key=value array
   * @return bool         True if method handeled the request
   */
  public function handleGET(array $args)
  {
    # Send OK back to client
    response()
      ->setStatusCode(200)               // HTTP 200 OK
      ->setJsonBody(['result'=>'OK'] );  // JSON data as array

    return true;    
  }
}
```


## Middleware
Middleware is executed before the HTTP request is sent to the controller for processing.

## Database
Example:
```php
<?php 
  $dbCon = db('main_db'); // `\Nofuzz\Database\PdoConnection` object
  
  $dbCon->beginTransaction();
  $dbCon->prepare( $sql );
  $dbCon->execute( $args );
  $dbCon->commit();

```
