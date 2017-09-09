# Changelog
See [Roadmap](roadmap.md) for details on whats in the pipe

# v0.5.7
Breaking changes in 0.5.7:
* New: Config file loaded based on "ENVIRONMENT" variable. This env-var can be set in Apache VHOST/.htaccess. Defaults to `dev` environment
* New: Config param `application.environment` deprecated, and is now read from the environment variable `ENVIRONMENT`

Non-breaking changes:
* Added a sample `app/Config/config-dev.json` file to the framework


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
