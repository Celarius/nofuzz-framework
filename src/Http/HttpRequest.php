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

class HttpRequest extends \GuzzleHttp\Psr7\ServerRequest implements \Nofuzz\Http\HttpRequestInterface
{
  /** @var string $clientIp Client IP address */
  protected $clientIp = '';

  /**
   * Constructor
   *
   * @param string                               $method       HTTP method
   * @param string|UriInterface                  $uri          URI
   * @param array                                $headers      Request headers
   * @param string|null|resource|StreamInterface $body         Request body
   * @param string                               $version      Protocol version
   * @param array                                $serverParams Typically the $_SERVER superglobal
   */
  public function __construct(
    $method,
    $uri,
    array $headers = [],
    $body = null,
    $version = '1.1',
    array $serverParams = []
  )
  {
    # Client IP & Host - 0.0.0.0 means we could not find the remote addr
    $this->clientIp = ( filter_var($_SERVER['REMOTE_ADDR'] ?? null, FILTER_VALIDATE_IP) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0' );

    parent::__construct($method, $uri, $headers, $body, $version, $serverParams);
  }

  /**
   * Get the ClientIP
   *
   * @return string           The ClientIP
   */
  public function getClientIp(): string
  {
    return $this->clientIp;
  }

}
