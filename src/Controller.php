<?php
/**
 * Controller
 *
 * @package     Nofuzz
 */
#################################################################################################################################

namespace Nofuzz;

abstract class Controller implements \Nofuzz\ControllerInterface
{
  /**
   * Initialization method
   *
   * This method is called right after the object has been created
   * before any Middleware handlers
   *
   * @param  array $args    Path variable arguments as name=value pairs
   */
  public function initialize(array $args)
  {
    // no code in abstract class
  }

  /**
   * Default handle() method for all HTTP Methods.
   *
   * Calls the appropriate handle*() method.
   *
   * @param  array $args    Path variable arguments as name=value pairs
   * @return bool           False if request failed, True for success
   */
  public function handle(array $args)
  {
    switch ( strtoupper(request()->getMethod()) ) {
      case "GET"    : return $this->handleGET($args); break;
      case "POST"   : return $this->handlePOST($args); break;
      case "PUT"    : return $this->handlePUT($args); break;
      case "PATCH"  : return $this->handlePATCH($args); break;
      case "DELETE" : return $this->handleDELETE($args); break;
      case "HEAD"   : return $this->handleHEAD($args); break;
      case "OPTIONS": return $this->handleOPTIONS($args); break;
      default       : return $this->handleCUSTOM($args); break;
    }
  }

  /**
   * Handle GET request
   *
   * @param  array $args    Path variable arguments as name=value pairs
   * @return bool           False if request failed, True for success
   */
  public function handleGET(array $args)
  {
    response()->error(405);

    return false;
  }

  /**
   * Handle POST request
   *
   * @param  array $args    Path variable arguments as name=value pairs
   * @return bool           False if request failed, True for success
   */
  public function handlePOST(array $args)
  {
    response()->error(405);

    return false;
  }

  /**
   * Handle PUT request
   *
   * @param  array $args    Path variable arguments as name=value pairs
   * @return bool           False if request failed, True for success
   */
  public function handlePUT(array $args)
  {
    response()->error(405);

    return false;
  }

  /**
   * Handle PATCH request
   *
   * @param  array $args    Path variable arguments as name=value pairs
   * @return bool           False if request failed, True for success
   */
  public function handlePATCH(array $args)
  {
    response()->error(405);

    return false;
  }

  /**
   * Handle DELETE request
   *
   * @param  array $args    Path variable arguments as name=value pairs
   * @return bool           False if request failed, True for success
   */
  public function handleDELETE(array $args)
  {
    response()->error(405);

    return false;
  }

  /**
   * Handle HEAD request
   *
   * @param  array $args    Path variable arguments as name=value pairs
   * @return bool           False if request failed, True for success
   */
  public function handleHEAD(array $args)
  {
    response()->error(405);

    return false;
  }

  /**
   * Handle OPTIONS request
   *
   * @param  array $args    Path variable arguments as name=value pairs
   * @return bool           False if request failed, True for success
   */
  public function handleOPTIONS(array $args)
  {
    response()->error(405);

    return false;
  }

  /**
   * Handle custom request
   *
   * @param  array $args    Path variable arguments as name=value pairs
   * @return bool           False if request failed, True for success
   */
  public function handleCUSTOM(array $args)
  {
    response()->error(405);

    return false;
  }

  /**
   * Return the Client HTTP Request object
   *
   * @return object
   */
  public function getRequest()
  {
    return request();
  }

  /**
   * Return the Client HTTP Response object
   *
   * @return object
   */
  public function getResponse()
  {
    return response();
  }

  /**
   * Return the Config object
   *
   * @return object
   */
  public function getConfig()
  {
    return config();
  }

  /**
   * Return the Logger object
   *
   * @return object
   */
  public function getLogger()
  {
    return logger();
  }

  /**
   * Return the Cache object
   *
   * @return object
   */
  public function getCache()
  {
    return cache();
  }

}
