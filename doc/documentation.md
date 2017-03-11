**Table of Contents**
- Getting started
  - Order of loaded files
- Routes
- Controllers
- Middleware
- Database connections
- Global functions
- Helpers
- JWT - JSON Web Token Encode/Decode
- Cipher - Encryption/Decryption

---
# Getting started
To start with Nofuzz a few core concepts need to be understood. These very basic core concepts are Routing, Controllers and Middleware.

Nofuzz is not a standalone application but rather a framework that enables rapid development of REST API's. Therefore just cloning the framework does not get you an application, instead take a look at [Nofuzz-Tutorial-Blog](https://github.com/Celarius/nofuzz-tutorial-blog-api) for a sample application using Nofuzz.

## Order of loaded files
When a request comes to the Napplication, the following files are loaded:
```txt
/public/bootstrap.php     Loaded by PHP engine when request comes
/Nofuzz/Application.php   by bootstrap.php | Nofuzz application init
/Nofuzz/Globals.php       by Applicaiton.php | Nofuzz globals & helpers
/app/globals.php          by bootstrap.php | Application globals & helpers
/app/Middleware/*         The route group Before Middlewares (if any)
/app/Controllers/*        The route controller to handle request
/app/Middleware/*         The route group After Middleware (if any)
```


# Routing
Routes are the heart of the application. App developers define the request routes that map to Controllers, with Middlewares before and after. All routes are defined in [routes.json](app/Config/routes.json). 
_The reason this is a JSON file is that 3rd party applications/installers etc. can modify the file easily without any other knowledge than JSON and the structure._

**"Groups" element parameters**
| Param | Type | Description |
|:------|:-----|:------------|
| name | `string` | The name of the Route Group (optional) |
| notes | `string` | Free text for developers |
| prefix | `string` | Route path prefix. Example `/api/v1` |
| before | `array` | Array wth full namespaced name of any middleware to run Before controller is called |
| after | `array` | Array wth full namespaced name of any middleware to run After controller is called |

**"Route" element parameters**
| Param | Type | Description |
|:------|:-----|:------------|
| methods | `string` or `array` | Defines the methods this route will match. Defaults is "" which matches ALL methods. An array may be specific with the matching methods: `"methods":["GET","POST"]`. The same can be done with a comma separated list: `"methods":"GET,POST"`. Both methods are equal |
| path | `string` | The matching path. Example `"path":"/api/v1/status"` |
| handler | `string` | The handling Controller class, with full namespace. Optionally a `@` can be specified to indicate the method to jump to. By default `handleGET()`, `handlePOST()` etc. methods are called based on the request method. |

**Route Example**
```txt
{ 
    "groups": [
    {
      "name": "",
      "notes": "",
      "prefix": "",
      "before": [],
      "routes": [
        {
            "methods": "",
            "path": "/health",
            "handler": "\\App\\Controllers\\HealthController"
        },
        {
            "methods": "GET",
            "path": "/status",
            "handler": "\\App\\Controllers\\StatusController@handleStatus"
        }
      ],
      "after": []
    }
}
```


# Controllers
Controllers are the classes that routes lead to. When defining a route to a Controller mapping the full Namespaced Classname of the Controller is given. Optionally a method name can be given. For each element in `Routes` there are is a "methods", "path" and "handler".

Before the specified method in the Controller is called, a special `initialize()` method gets called, so generic initiaization can be done.


# Middleware
Nofuzz has two types of Middleware: `Before` and `After`. As the names indicate the _Before Middlewares_ are called before the Controller handles  the request, and the _After Middlewares_ are called after the Controller has handled the request (regardless of outcome of Controller handler).

All app-defined Middleware extend `\Nofuzz\Middleware` and override the `handle()` method to perform actions in the Middleware.


# Database connections
Database connections are handeled by the Connection Manager. An application can open connections to different databases simultaneously.

To obtain a connection to a database, a `db('<name_of_connection')` is issued. This will look in the `/app/Config/config.json` file in the `connections` section to identify the Connection and connect to it. 


# Global functions
The following are predefined global functions usable anywhere in the code. These are defined in the `\Nofuzz\Globals.php` file.
```php
function app(string $property=null)
function env(string $var, $default=null)
function config(string $key=null)
function logger()
function db(string $connectionName)
function cache(string $driverName='')

function request()
function queryParam(string $paramName)
function postParam(string $paramName)
function cookieParam(string $paramName)
function response()
```


# Helpers
Helpers are static classes that can be used throughout the app to perform functions. These are bigger than just a global function, but contained in one helper.
The following helpers exist:
- Cipher - OpenSSL Encryption/Decryption
- Hash - OpenSSL Message Digests (Hash) library
- UUID - Create UUID values
- JWT - [Json Web Tokens](http://jwt.io) library
 
## Cipher Helper
The Cipher helper is a helper that encapsulates the OpenSSL library for Encryption and Decryption. By default the Cipher uses the secret value in the config as encryption key.

Encrypting example:
```php
  $plain = 'This will be encrypted';
  $encryptedValue = \\Nofuzz\\Helpers\\Cipher::encrypt( $plain );
```

Decrypting example:
```php
  $encryptedValue = '<encrypted value here>';
  $plain = \\Nofuzz\\Helpers\\Cipher::decrypt( $encryptedValue );
```

## Hash Helper
The Hash helper is a helper that encapsulates the OpenSSL library for Message Digest creation. 

Create Hash example:
```php
  $digest = \\Nofuzz\\Helpers\\Hash::generate('This is the data');
```

## UUID Helper
The UUID helper is a helper that generates v4 UUID values. Each value is guaranteed to be unique. 

Create Hash example:
```php
  $uuid = \\Nofuzz\\Helpers\\UUID::generate();
```

## JWT Helper
The JWT helper is a helper that wraps JWT Encoding and Decoding into a single class. Please see [php-jwt](https://github.com/firebase/php-jwt) for more information. 
