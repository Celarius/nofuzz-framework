**Table of Contents**
- Installing
  - Server requirements
  - Installing on Windows
  - PHP.ini settings
  - Apache Configuration
    - /conf/httpd.conf
    - Using .htaccess file
- Deploying an Application
  - Directory structure
  - Composer update

---
# Installing
Nofuzz works as a Composer package itself so any web-server running PHP should be compatible.
To see the Nofuzz framework in action have a look at [Nofuzz-Tutorial-Blog](https://github.com/Celarius/nofuzz-tutorial-blog-api)

## Server requirements
Nofuzz needs the following from PHP:
- PHP >= 7.0
- PDO PHP Extension (and the DB specific PDO)
- Mbstring PHP Extension

**Optional extensions**
- OPCache (highly recommended)
- APCu (highli recommended)

**Composer packages**
- guzzlehttp/guzzle
- nikic/fast-route
- psr/simple-cache
- monolog/monolog
- firebase/php-jwt

## Installing on Windows
This guide assumes a working installation of Apache+PHP, and that you will be using a VHost for the new Nofuzz based application.

## PHP.INI changes
In a clean `php.ini` based on `php.ini-production` the following needs to be changed/set:

```ini
; Set Error Log path
error_log = "<path>/php/logs/error.log"

;
; Dynamic Extensions
;
extension=php_mbstring.dll
extension=php_openssl.dll

; Enable all needed PDO drivers
;extension=php_pdo_firebird.dll
;extension=php_pdo_mysql.dll

; WINDOWS: Enable Zend OPCache on Windows
zend_extension=php_opcache.dll

; OPCache DEV settings
[opcahce]
opcache.enable=1
opcache.memory_consumption=192
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=7963
opcache.fast_shutdown=1

; APCu settings
[APCu]
extension=php_apcu.dll
apc.enabled=1
apc.shm_size=32M
apc.ttl=7200
```

## Apache Configuration

### /conf/httpd.conf
Make sure the following are set in httpd.conf (on windows platforms). On *nix platforms include it as is best practice for the platform.
```txt
Include conf/extra/httpd-vhosts.conf
```

### conf/extra/httpd-vhosts.conf
_Tip: In the VHOST definition you can set environment options with `SetEnv <variable> <value>`, and later access these in PHP via `$_ENV[<variable>]` or the global function `env('<variable>')`_

```txt
# Define constants for the application
Define NF_APP_DOMAIN    "api.mydomain.com"
Define NF_APP_CODE      "myapp"
Define NF_APP_PATH      "/var/www/applications/my_app"
Define NF_APP_EMAIL     "admin@mydomain.com"
Define NF_APP_ENV       "dev"

# VHost definition
<VirtualHost *:80>
  ServerName ${NF_APP_DOMAIN}
  ServerAdmin ${NF_APP_EMAIL}
  DocumentRoot "${NF_APP_PATH}/src/public"

  ErrorLog "logs/${NF_APP_CODE}-error.log"
  CustomLog "logs/${NF_APP_CODE}-access.log" common

  <Directory "${NF_APP_PATH}/src/public">
    <IfModule mod_negotiation.c>
        Options -MultiViews
    </IfModule>

    SetEnv ENVIRONMENT ${NF_APP_ENV}

    # Try to load files in order of appearance if requeest matches a dir
    DirectoryIndex bootstrap.php index.html index.htm index.php

    Options -Indexes +FollowSymLinks
    AllowOverride All
    Order allow,deny
    Allow from all
    Require all granted

    DirectorySlash Off

    # Rewrite Engine to direct all requests to bootstrap.php file
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ bootstrap.php [L]
  </Directory>
</VirtualHost>
```

### Using .htaccess file
You can also use the `.htaccess` file to rewrite requests to the bootstrap file, but this is not recommeded.
The biggest reason being performance.

```txt
    SetEnv ENVIRONMENT PROD

    # Rewrite Engine to direct all requests to nofuzz bootstrap.php file
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule . bootstrap.php [QSA,L]
```

## Config options
The default Config files is located at `/app/config/config-${environment}.json`
Below is an example configuration from the [Nofuzz-Tutorial-Blog](https://github.com/Celarius/nofuzz-tutorial-blog-api) application.
Values you should change for every application are:
- Application
  - Code     (The applications unique code (appears in Logs etc))
  - Name     (Human readable name)
  - Version  (Application version)
  - Secret (`!Important!` Used as Secret key in encrytpion by default (JWT))

```txt
    "application": {
        "code": "Nofuzz-Tutorial-Blog",
        "name": "Nofuzz Tutorial Blog",
        "version": "1.0.0",
        "global": {
            "maintenance": false,
            "message": "We are in maintenance mode, back shortly",
            "timezone": "Europe\/Stockholm"
        },
        "secret": "This Value Needs To Be Changed To Something Random"
    },
    "log": {
        "level": "error",
        "driver": "php",
        "drivers": {
            "php": {
                "line_format": "[%channel%] [%level_name%] %message% %context%",
                "line_datetime": "Y-m-d H:i:s.v e"
            },
            "file": {
                "file_path": "storage\/log",
                "file_format": "Y-m-d",
                "line_format": "[%datetime%] [%channel%] [%level_name%] %message% %context%",
                "line_datetime": "Y-m-d H:i:s.v e"
            }
        }
    },
    "cache": {
        "driver": "Apcu",
        "options": null
    },
```


# Deploying to production (Apache)
In order to deploy to production do the following:
1. Install Apache & PHP
2. Configure Apache httpd.conf & vhost.conf
3. Copy/Clone php application files to target dir
4. Set environment specific config options in `/app/Config/config.json`
5. Update Composer & Packages (see below)
6. Restart Apache

## Directory Structure
A basic Nofuzz application has the following structure. The `/app` folder will contain most of the files you create for an application in various sub-folders (depending on namespace usage).
```txt
<app_name>/
    .git/                         Git folder
    app/                          Application files folder
    public/                       Application public files
    storage/                      Application storage foleder (logs/temp)
    tests/                        Application unit-tests
    vendor/                       Composer packages
    composer.json                 Composer requirements
```


## Composer update
Run the following composer command to update & generate optimized autoload files:
```txt
composer self-update
composer update --no-dev -o
```
