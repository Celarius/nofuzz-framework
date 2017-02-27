<?php
/**
 * Nofuzz\Http\Client
 *
 * Generic HTTP Client that has some quick helper funcs for quick GET,POST,PUT etc.
 *
 * Example:
 *
 *    if ($httpClient->get('http://<domain>/<path>;;')) {
 *      $responseBody = $httpClient->getResponse()->getBody();
 *    } else {
 *      // error
 *    }
 *
 * @package   [Nofuzz]
 */
#################################################################################################################################

namespace Nofuzz\Http;

class Client // extends ... implements ...
{
  protected $guzzleClient = null;
  protected $cookieJar = null;

  protected $timeoutConnect = 3;
  protected $timeoutReceive = 10;
  protected $httpRequest = null;
  protected $httpResponse = null;
  protected $retries = 1;

  protected $timeStart = 0;
  protected $timeHeaders = 0;
  protected $timeResponse = 0;

  protected $opts = [];

  protected $lastErrorStr = '';

  /**
   * Constructor
   *
   * @param array $opts             Guzzle compatible parameters array
   */
  public function __construct(array $opts=[])
  {
    $this->guzzleClient = null;
    $this->setRequestOptions($opts);
  }

  /**
   * Create the internal GuzzleHttp\Client
   *
   * @param  array  $opts [description]
   * @return object
   */
  protected function createClient(array $opts=[])
  {
    if ( !$this->guzzleClient ) {
      $this->guzzleClient = new \GuzzleHttp\Client();
      $this->cookieJar = new \GuzzleHttp\Cookie\CookieJar();
    }

    return $this->guzzleClient;
  }

  /**
   * Creates & returns a GuzzleHttp\Request
   *
   * @param  string $method     [description]
   * @param  string $url        [description]
   * @param  mixed  $body       [description]
   * @param  array  $headers    [description]
   * @return object
   */
  public function createRequest(string $method, string $url, $body=null, array $headers=[])
  {
    # Merge Default Headers with user provided headers
    $headers = array_merge(
      [
         'Accept'          => '*/*',
         'Accept-Encoding' => 'gzip, deflate'
      ],
      $headers
    );

    # Create the httpRequest
    $this->httpRequest = new \GuzzleHttp\Psr7\Request( $method, $url, $headers, $body );

    return $this->httpRequest;
  }

  /**
   * Send a GuzzleHttp\Request
   *
   * @param  \GuzzleHttp\Psr7\Request $request
   * @param  array                    $opts     Guzzle Send Options
   * @return \GuzzleHttp\Response
   */
  public function sendRequest(\GuzzleHttp\Psr7\Request $request, array $opts=[])
  {
    # Set params
    $retriesLeft = $this->retries;
    $this->lastErrorStr = '';

    # Retry loop
    while ( $retriesLeft>0 )
    {
      # Decrease retry count
      $retriesLeft--;

      $this->timeStart = 0;
      $this->timeHeaders = 0;
      $this->timeResponse = 0;

      try {
        # Make sure we have a GuzzleHttp\Client
        $this->createClient($this->opts);
        $this->timeStart = microtime(true);

        # Request Options
        $opts = array_merge(
          [
            'http_errors' => true,                               // Exceptions on 4xx response codes
            'connect_timeout' => $this->getTimeoutConnect(),     // Connect Timeout (seconds)
            'timeout'  => $this->getTimeoutReceive(),            // Receive Timeout (seconds)
            'verify' => false                                    // Disable SSL verification (handle self generated SSL certs)
          ]
          ,$this->getRequestOptions()
        );

        $sendOpts = array_merge([
           'on_headers' => function (\GuzzleHttp\Psr7\Response $response) {
             $this->timeHeaders = microtime(true)-$this->timeStart;
           },
           'synchronous'      => true,
           'cookies'          => $this->cookieJar,
           'debug'            => false
           // 'on_headers' => function ( \Psr\Http\Message\ResponseInterface $response ) {
           //   error_log( print_r($response->getHeaders(), true) );
           // },
           // 'on_stats' => function ( \GuzzleHttp\TransferStats $status ) {
           //   error_log( $status->getTransferTime().' - '.print_r($status->getHandlerStats(),true) );
           // }
         ],$opts
        );


        # Send the Request
        $this->httpResponse = $this->guzzleClient->send($request, $sendOpts);

        # Successful response, break the loop
        $retriesLeft = 0;

      } catch (\GuzzleHttp\Exception\ConnectException  $e) {
        # Network errors
        #
        # Note: For "Host not found", "DNS failure", "Network errors" etc.
        #       we might not have no Response Object!

        # Set error String
        $this->lastErrorStr = $e->getMessage();

        # Get the Response Object from the Exception
        if ( $e->hasResponse() ) {
          $this->httpResponse = $e->getResponse();
        }

        # No point in retrying
        $retriesLeft = 0;

      } catch (\GuzzleHttp\Exception\BadResponseException $e) {
        # 4xx (ClientException)
        # 5xx (ServerException)
        #
        # Note:  We have a response object available, meaning the target service responded
        #        so no retry needed (and it exists so no SDR reporting)

        # Set error String
        $this->lastErrorStr = $e->getMessage();

        # Get the Response Object from the Exception
        if ( $e->hasResponse() ) {
          $this->httpResponse = $e->getResponse();
        }

        # Break the while loop (no retries for this error)
        $retriesLeft = 0;

      } catch (\GuzzleHttp\Exception\RequestException $e) {
        # Also Catches
        #   TooManyRedirectsException

        # Set error String
        $this->lastErrorStr = $e->getMessage();

        # Get the Response Object from the Exception
        if ( $e->hasResponse() ) {
          $this->httpResponse = $e->getResponse();
        }

        # Should we do a retry?
        if ($retriesLeft>0) {
          $_seco = 0.1; // 0.1 seconds = 100 ms
          usleep($_sec * 1000000);
          unset($_sec);
        }

      } catch (\Exception $e) {
        # Set error String
        $this->lastErrorStr = $e->getMessage();

        # Break the while loop (no retries for this error)
        $retriesLeft = 0;

      } finally {
        $this->timeResponse = microtime(true) - $this->timeStart;

      }

    }

    return $this->httpResponse;
  }

  /**
   * Send a GET request
   *
   * @param  string $uri                The uri to call in the service
   * @param  array $headers             The http headers
   * @return bool                       True for success
   */
  public function get(string $uri, array $headers=[])
  {
    $this->sendRequest($this->createRequest('GET',$uri), $headers);

    return !is_null($this->getResponse());
  }

  /**
   * Send a POST request
   *
   * @param  string $uri                The uri to call in the service
   * @param  string $body               The body to send
   * @param  array $headers             The http headers
   * @return bool                       True for success
   */
  public function post(string $uri, string $body='', array $headers=[])
  {
    $this->sendRequest($this->createRequest('POST',$uri,$body), $headers);

    return !is_null($this->getResponse());
  }

  /**
   * Send a PUT request
   *
   * @param  string $uri                The uri to call in the service
   * @param  string $body               The body to send
   * @param  array $headers             The http headers
   * @return bool                       True for success
   */
  public function put(string $uri, string $body='', array $headers=[])
  {
    $this->sendRequest($this->createRequest('PUT',$uri,$body), $headers);

    return !is_null($this->getResponse());
  }

  /**
   * Send a DELETE request
   *
   * @param  string $uri                The uri to call in the service
   * @param  array $headers             The http headers
   * @return bool                       True for success
   */
  public function delete(string $uri, array $headers=[])
  {
    $this->sendRequest($this->createRequest('DELETE',$uri), $headers);

    return !is_null($this->getResponse());
  }

  /**
   * Send a PATCH request
   *
   * @param  string $uri                The uri to call in the service
   * @param  array $headers             The http headers
   * @return bool                       True for success
   */
  public function patch(string $uri, array $headers=[])
  {
    $this->sendRequest($this->createRequest('PATCH',$uri), $headers);

    return !is_null($this->getResponse());
  }

  /**
   * Send a HEAD request
   *
   * @param  string $uri                The uri to call in the service
   * @param  array $headers             The http headers
   * @return bool                       True for success
   */
  public function head(string $uri, array $headers=[])
  {
    $this->sendRequest($this->createRequest('HEAD',$uri), $headers);

    return !is_null($this->getResponse());
  }

  /**
   * Send a custom HTTP request
   *
   * @param  string $uri                The uri to call in the service
   * @param  array $headers             The http headers
   * @return bool                       True for success
   */
  public function custom(string $method, string $uri, string $body='', array $headers=[])
  {
    $this->sendRequest($this->createRequest($method,$uri), $headers);

    return !is_null($this->getResponse());
  }

  public function getLastErrorStr(): string
  {
    return $this->lastErrorStr;
  }
  /**
   * Get Timeout for Connect
   *
   * @return float
   */
  function getTimeoutConnect(): float
  {
    return $this->timeoutConnect;
  }

  /**
   * Set Connect Timeout
   *
   * @param   float $timeout          The timeout in seconds
   * @return  self
   */
  function setTimeoutConnect(float $timeout)
  {
    $this->timeoutConnect = $timeout;
    return $this;
  }

  /**
   * Get Timeout for Receive
   *
   * @return float
   */
  function getTimeoutReceive(): float
  {
    return $this->timeoutReceive;
  }

  /**
   * Set Receive Timeout
   *
   * @param   float $timeout          The timeout in seconds
   * @return  self
   */
  function setTimeoutReceive(float $timeout)
  {
    $this->timeoutReceive = $timeout;
    return $this;
  }

  /**
   * Get Retries count
   *
   * @return int
   */
  function getRetires(): int
  {
    return $this->retires;
  }

  /**
   * Set Retires count
   *
   * @param   int $retires          The number of times to retry a failed request
   * @return  self
   */
  function setRetries(int $retries)
  {
    $this->retires = $retries;
    return $this;
  }

  /**
   * Get last Request Object or null
   *
   * @param   float $timeout          The timeout in seconds
   * @return  null | \Guzzle\Psr7\HttpResquest
   */
  public function getRequest()
  {
    return $this->httpRequest;
  }

  /**
   * Get last Response Object or null
   *
   * @return  null | \Guzzle\Psr7\HttpResponse
   */
  public function getResponse()
  {
    return $this->httpResponse;
  }

  /**
   * Get TTFB (Time to First Byte) in seconds
   *
   * @return float
   */
  public function getHeadersTime(): float
  {
    return $this->timeHeaders;
  }

  /**
   * Get Response Time in seconds
   *
   * @return float
   */
  public function getResponseTime(): float
  {
    return $this->timeResponse;
  }

  /**
   * Get Opts
   *
   * @return array
   */
  public function getRequestOptions(): array
  {
    return $this->opts;
  }

  /**
   * Set Request Options
   *
   * @return self
   */
  public function setRequestOptions(array $opts)
  {
    $this->opts = $opts;

    return $this;
  }

}
