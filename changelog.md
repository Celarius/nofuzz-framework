# Changelog
See [Roadmap](roadmap.md) for details on whats in the pipe

# v0.5.1
Status: In Dev
* Added HTTP Authentication middleware (Basic,Apikey,Bearer(JWT))
* Added Dependency injection container (supports anonymous funcs)
* Added `Method` definition to Routes array
* Added `@<method>` definition to Routes handler
* Added exception logging for invalid `routes.json` file format
* Added Encryption/Decryption [Cipher Helper](src/Nofuzz/Helpers/Cipher.php) helper
* Added Message Digest/Hashing [Hash Helper](src/Nofuzz/Helpers/Cipher.php) helper
* Added [UUID Helper](src/Nofuzz/Helpers/UUID.php) helper

## App clients
* Added [SrClient](srv/app/Clients/Sr) to access the Service Registry
* Added srGet,srPost,srPut,srPatch,srDelete,srOptions functions to [globals.php](src/app/globals.php)

## Examples
* Added [Service Registry](src/app/Services/Sr) example 


---
# v0.5.0
Status: Alpha Release
* Added Middleware ("Common","Before" and "After" Controller is called)
* Renamed `ServerMiddleware` to just `Middleware`


---
# v0.4.0
Status: Released 2017-02-10
* PHP version >= 7.0
* Added Composer (PSR-4) support
* Added PSR-16 compliant [SimpleCache](src/Nofuzz/SimpleCache)
* Added [JWT Helper](src/Nofuzz/Helpers/JWT.php)
* Added BaseDAO class
* Add Database (PDO) support
* Add SimpleCache (PSR-16) support

## Examples
* Wrote example controller to access database and use a DAO object 

---
# v0.3.x (internal releases)
* v0.3 and prior releases are all internal releases

