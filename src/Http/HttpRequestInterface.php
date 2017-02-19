<?php
/**
 * HTTPRequest class
 *
 * Represents a HTTP Request
 *
 * @package     Nofuzz
 */
#################################################################################################################################

namespace Nofuzz\Http;

interface HttpRequestInterface
{
  function getClientIp(): string;
}
