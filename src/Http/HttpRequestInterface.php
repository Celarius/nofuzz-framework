<?php
/**
 * Nofuzz\Http\HTTPRequestInterface
 *
 * Represents a HTTP Request (Server request)
 *
 * @package     Nofuzz
 */
#################################################################################################################################

namespace Nofuzz\Http;

interface HttpRequestInterface
{
  /**
   * getClientIp - Return the Remote Client's IP address
   * 
   * @return string
   */
  function getClientIp(): string;

}
