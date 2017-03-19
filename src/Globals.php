<?php
/**
 * Nofuzz Globals
 *
 * Creates global functions to make life easier. These functions use
 * the $app global variable to access the Nofuzz application.
 *
 * Also registers global dependencies
 *
 * @package   Nofuzz
 */
#################################################################################################################################

if (!function_exists('env')) {
  /**
   * Gets the value of an environment variable. Supports boolean, empty and null.
   *
   * @param  string  $var
   * @param  mixed   $default
   * @return mixed
 */
  function env(string $var, $default=null)
  {
    # Get from Environmental vars
    $val = getenv($var);

    # If nothing found, return $default
    if ($val === false)
      return $default;

    # Extract "True"/"False" values
    switch (strtolower($val)) {
      case 'true':
      case '(true)':
        return true;
      case 'false':
      case '(false)':
        return false;
      case 'empty':
      case '(empty)':
        return '';
      case 'null':
      case '(null)':
        return null;
    }

    # Extract "" encapsulated values
    if ( $val[0]==='"' && $val[-1]==='"' ) {
      return trim($val,'"');
    }

    return $val;
  }
}

if (!function_exists('app')) {
  /**
   * Get the App "object", "property" or a "dependancy"
   *
   * @return object
   */
  function app(string $property=null)
  {
    global $app;

    if (is_string($property) && !empty($property) ) {
      return $app->getProperty($property);
    }

    return $app;
  }
}

if (!function_exists('config')) {
  /**
   * Get the Config object
   *
   * @return object
   */
  function config(string $key=null)
  {
    global $app;
    if (is_null($key)) {

      return $app->getConfig();
    } else {

      return $app->getConfig()->get($key);
    }
  }
}

if (!function_exists('logger')) {
  /**
   * Get the Logger object
   *
   * @return object
   */
  function logger()
  {
    global $app;
    return $app->getLogger();
  }
}

if (!function_exists('db')) {
  /**
   * Get a Connection object
   *
   * @return object
   */
  function db(string $connectionName='')
  {
    global $app;
    return $app->getConnectionManager()->getConnection($connectionName);
  }
}

if (!function_exists('cache')) {
  /**
   * Get the Cache object
   *
   * @return object
   */
  function cache(string $driverName='')
  {
    global $app;
    return $app->getCache($driverName);
  }
}

if (!function_exists('request')) {
  /**
   * Get the Request object
   *
   * @return object
   */
  function request()
  {
    global $app;
    return $app->getRequest();
  }
}

if (!function_exists('queryParam')) {
  /**
   * Get a Query Param ($_GET variable)
   *
   * @param  string $paramName
   * @param  mixed $default
   * @return mixed
   */
  function queryParam(string $paramName, $default=null)
  {
    global $app;
    return $app->getRequest()->getQueryParams()[$paramName] ?? $default;
  }
}

if (!function_exists('postParam')) {
  /**
   * Get a Post Param ($_POST variable)
   *
   * @param  string $paramName
   * @param  mixed $default
   * @return mixed
   */
  function postParam(string $paramName, $default=null)
  {
    global $app;
    return $app->getRequest()->getParsedBody()[$paramName] ?? $default;
  }
}

if (!function_exists('cookieParam')) {
  /**
   * Get a Cookie Param ($_COOKIE variable)
   *
   * @param  string $paramName
   * @param  mixed $default
   * @return mixed
   */
  function cookieParam(string $paramName, $default=null)
  {
    global $app;
    return $app->getRequest()->getCookieParams()[$paramName] ?? $default;
  }
}

if (!function_exists('response')) {
  /**
   * Get the Response object
   *
   * @return object
   */
  function response()
  {
    global $app;
    return $app->getResponse();
  }
}
