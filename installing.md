# Installing

**Table of Contents**
* Installing
  * Windows additional downloads
    - APCu (Cache)
  * PHP extensions
  * Apache changes
    * conf/httpd.conf
    * conf/extra/httpd-vhosts.conf
    * Using .htaccess file
* Deploying an Application
  * Directory structure
  * Composer update

---

# Installing
<< download :: composer installation >>

## Windows additional downloads
### APCu (Cache)
Go to [https://pecl.php.net/package/APCu] and download the appropriate version (check PHP version and x86 or x64 bitness).

Extract and copy the `php_apcu.dll` file to your php's `ext/` folder

## PHP extensions
Nofuzz needs PHP v7.1 or newer.  PHP extensions to be enabled:
- PDO (Firebird, MySQL or Oracle)
- MBString
- OpenSSL
- FileInfo
- cUrl
- Intl

3rd party frameworks (via Composer):
- Guzzle 6 or newer

### Recommended extensions
- Zend OPCache is highly recommended
- APUc


## PHP.INI changes
In a clean `php.ini` based on `php.ini-production` the following needs to be changed/set:

```ini
; Set Error Log path
error_log = "<path_to_php>/php/logs/error.log"

;
; Dynamic Extensions
;
extension=php_curl.dll
extension=php_fileinfo.dll
extension=php_intl.dll
extension=php_mbstring.dll
extension=php_openssl.dll
; Enable all needed PDO drivers 
extension=php_pdo_firebird.dll
extension=php_pdo_mysql.dll

; WINDOWS: Enable Zend OPCache on Windows
zend_extension=php_opcache.dll

[opcahce]
opcache.enable=1
opcache.memory_consumption=192
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=7963
opcache.fast_shutdown=1

[APCu]
extension=php_apcu.dll
apc.enabled=1
apc.shm_size=32M
apc.ttl=7200
```

## Web-Server changes

## Apache Configuration

## conf/httpd.conf
Make sure the following are set in httpd.conf
```txt
Include conf/extra/httpd-mpm.conf
```


## Apache Changes

### conf/httpd.conf
Make sure the following are set in httpd.conf
```txt
Include conf/extra/httpd-mpm.conf
Include conf/extra/httpd-vhosts.conf
```

### conf/extra/httpd-vhosts.conf
_Tip: In the VHOST definition you can SET ENVIRONMENT options with `SetEnv <variable> <value>`, and later access these in PHP via `$_ENV[<variable>]` or the helper `env('<variable>')`_

```txt
<VirtualHost *:80>
  ServerName myserver.domain.com
  ServerAdmin webmaster@domain.com

  DocumentRoot "<path_to_nofuzz_src_public>"

  ErrorLog "logs/myserver-error.log"
  CustomLog "logs/myserver-access.log" common

  <Directory "<path_to_nofuzz_src_public>">
    Options -Indexes +FollowSymLinks
    AllowOverride All
    Order allow,deny
    Allow from all
    Require all granted

    # Set Variables
    SetEnv ENVIRONMENT PROD

    # Disable appending a "/" and 301 redirection when a directory
    # matches the requested URL
    DirectorySlash Off
    
    # Set Rewrite Engine ON to direct all requests to
    # the `bootstrap.php` file
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-d   # Not a dir
    RewriteCond %{REQUEST_FILENAME} !-f   # Not a file
    RewriteRule . bootstrap.php [QSA,L]
  </Directory>
</VirtualHost>
```
### Using .htaccess file
```txt
    SetEnv ENVIRONMENT PROD

    # Rewrite Engine to direct all requests to nofuzz bootstrap.php file
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule . bootstrap.php [QSA,L]
```




## Config options
Config is loaded from the `/app/config/config.json`:
```json
{
  "sessions": {
    "type": "database",
    "sid": "PHPSESS",
    "timeout": 3600,
    "refresh": 600,
  }
}
```


---
# Deploying to production
* Install Apache & PHP
* Configure Apache httpd.conf & vhost.conf
* Copy php application files to target dir
* Set config options in `app\Config\config.json`
* Update Composer (see below) 

## Directory Structure
```txt
src/
    app/                          Application custom files
      config/                     Configuration files    
        config.json               Main configuration file
        services.json             List of known MicroService instances
        routes.json               App defined routes
      Controllers/                App controllers
      Middlewares/                App middlewares
      globals.php                 App Globals file (optional)
        
    public/                       Application public files
      bootstrap.php               App's Front Controller/Bootstrap file
        
    storage/        
      cache/        
      temp/       
      logs/                       Application logs (default logpath)      
        
    vendor/                       Composer packages
        
    Nofuzz/                       NoFuzz Micro-Service framework
      Clients/        
      Config/                     Config class
      Database/                   Database Connection
      Exception/                  Exception classes
      Helpers/                    Helper classes
      Http/                       HTTP classes
      Middleware/                 Predefined Middleware
      Log/                        Logger class
      Route/                      Route classes
      SimpleCache/                SimppleCache classes 
      Application.php             Main Nofuzz application class
      Controller.php              Abstract Controller
      ControllerInterface.php     ControllerInterface
      Globals.php                 Nofuzz global functions
      Loader.php                  Nofuzz loader that creates an App
      Middleware.php              Abstract Middleware
      MiddlewareInterface.php     MiddlewareInterface
```


## Composer update
Run the following composer command to update & generate optimized autoload files: 
```txt
composer self-update
composer update --no-dev -o
```
