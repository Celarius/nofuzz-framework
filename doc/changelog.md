# Changelog
See [Roadmap](roadmap.md) for details on whats in the pipe

# v0.6.x
*Breaking changes:*
* New: Config file loaded based on env var `ENVIRONMENT`. This env var can be set in Apache VHOST/.htaccess. Defaults to `dev`
* New: Config param `application.environment` deprecated, and is now read from the env var `ENVIRONMENT`

*Non-breaking changes:*
* Added General Runtime Exception on config file not found. Returns 500 to user.
* Added a sample `app/Config/config-dev.json` file to the framework
* Added JSON_NUMERIC_CHECK to default options for `HTTPResponse->errorJson()` and `HTTPResponse->setJsonBody()`
* Changed `HTTPResponse->errorJson()` to use `HTTPResponse->setJsonBody()` when finally setting the body
* Changed request decoding to use Guzzles `ServerRequest::fromGlobals()`. Guzzle bug now fixed
* Updated documentation to SimpleCache/* files
* Changed Cache initialization to handle situation when driver libs for cache missing

# v0.5.6
* Added param to define JSON encoding options on `setJsonBody()` and `errorJson()` methods. Defaults to `JSON_PRETTY_PRINT` to maintain compatibility
* Bugfix for retry-sleep in Nofuzz\Http\Client
* Bootstrap.php comments cleanup, small code cleanup
* Added method getCookies() to Nofuzz\Http\HttpResponse()
* Comments and Code cleanup in several places

# v0.5.5
* Moved rawQuery() and rawExec() to PDOConnection.php
* Deprecated db() in AbstractBaseDao.php

# v0.5.4
* Added CockroachDb Database Driver
* Added PostgreSql Database Driver
* Moved Route Loading to application->run(), to better catch errors in routes.json

# v0.5.3
* Accessing the database connection with db() or db('') now uses 1st connection in `config.json`
* Added UUID v5 generation to UUID helper
* Added Units-Tests for many things (work in progress)
* PHP 7.1.3 compatibility verified
* errorHandler() in application.php fixed to not log unwanted data
* Renamed "BaseDao" to "AbstractBaseDao" and "BaseDBObject" to "AbstractBaseEntity"
* AbstractBaseDao rawQuery() and rawExec() methods added

# v0.5.2
* Added HTTP Authentication middleware (Basic,Apikey,Bearer(JWT))
* Added Dependency injection container (supports anonymous funcs)
* Added `Method` definition to Routes array
* Added `@<method>` definition to Routes handler
* Added exception logging for invalid `routes.json` file format
* Added Encryption/Decryption [Cipher Helper](src/Helpers/Cipher.php) helper
* Added Message Digest/Hashing [Hash Helper](src/Helpers/Hash.php) helper
* Added [UUID Helper](src/Helpers/UUID.php) helper
* Added `Config->loadAndMerge()` method to `\Nofuzz\Config\Client` class
