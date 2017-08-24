<?php
/**
 * HTTPResponse class
 *
 * Represents a HTTP Response
 *
 * @package     Nofuzz
 */
#################################################################################################################################

namespace Nofuzz\Http;

class HttpResponse implements \Nofuzz\Http\HttpResponseInterface
{
  /** @var int $statusCode HTTP Response Status Code */
  protected $statusCode = 200;

  /** @var int $statusCode HTTP Response Status Code */
  protected $statusText = '';

  /** @var string $protocolVersion HTTP protocol version */
  protected $protocolVersion = '1.0';

  /** @var array $headers All HTTP Headers */
  protected $headers = array();

  /** @var array $cookies */
  protected $cookies = array();

  /** @var string $cacheControl HTTP Cache-Control header */
  protected $cacheControl = 'no-cache';

  /** @var string $charSet HTTP charset */
  protected $charSet = 'utf8';

  /** @var string $body The POST body as a string */
  protected $body = '';

  /** @var string $filename The filename to output as raw-data */
  protected $fileBody = '';


  /** [__construct description] */
  public function __construct()
  {
    # Clear our properties
    $this->clear();
  }

  /** Clear properties */
  public function clear()
  {
    $this->setCookies( array() );
    $this->setStatusCode(200);
    $this->setHeaders( array() );
    $this->setHeader('Content-Type', 'application/json; charset=utf8');
    $this->setCacheControl('no-cache');
    $this->setCharSet('utf8');
    $this->setBody('');
    $this->setFileBody('');

    return $this;
  }

  /**
   * Sets Output Compression On or OFf
   *
   * @param boolean $active   True for activating compression
   * @param int $level        Compression level. -1=Auto, 0=None, 9=Max
   */
  public function setCompression(bool $active = true, int $level = -1)
  {
    if ($active) {
      # Extract Accept-Encoding request header (from Apache)
      $acceptEncoding = getAllheaders()['Accept-Encoding'] ?? '';

      # If the Client support GZip/Deflate Compression - do it!
      if (strpos($acceptEncoding, 'gzip') !== FALSE) {
        ini_set("zlib.output_compression", 2048); // Compression block size
        ini_set("zlib.output_compression_level", -1); // -1=Auto, 0=None, 9=Max
      } else {
        ini_set("zlib.output_compression_level", 0); // -1=Auto, 0=None, 9=Max
      }
    } else {
      ini_set("zlib.output_compression_level", 0); // -1=Auto, 0=None, 9=Max
    }

    return $this;
  }


  /**
   * Send response to client
   */
  public function send()
  {
    # Set HTTP Response Code
    http_response_code($this->statusCode);

    # Update Cache-Control Header
    if ( empty($this->getHeader('Cache-Control')) ) {
      $this->setHeader('Cache-Control', $this->cacheControl);
    }

    # Set All HTTP headers
    foreach ($this->headers as $header => $value) {
      header($header.': '.$value);
    }

    # Set all cookies
    foreach ($this->cookies as $idx => $cookie) {
      setCookie( $cookie['name'], $cookie['value'], $cookie['expire'], $cookie['path'], $cookie['domain'], $cookie['secure'], $cookie['httponly'] );
    }

    # Send Response Body -- if we have something in the $body param
    if ( !empty($this->getBody()) ) {
      echo $this->getBody();
    } else
    if ( !empty($this->getFileBody()) ) {
      readfile($this->getFileBody());
    }

    # Results
    return $this;
  }

  /**
   * Sets the corresponding HTTP Text for the Code
   *
   * @param int $code           The HTTP code
   * @param string $customText  If defined, will overwrite the default HTTP text
   * @return string             The text set
   */
  protected function setTextFromCode(int $code, string $customText='')
  {
    if (strlen($customText)>0) {
      $this->statusText = $customText;
      return $customText;
    }

    switch ( $code ) {
      case 100: $this->statusText = 'Continue'; break;
      case 101: $this->statusText = 'Switching Protocols'; break;
      case 102: $this->statusText = 'Processing (WebDAV)'; break;

      case 200: $this->statusText = 'OK'; break;
      case 201: $this->statusText = 'Created'; break;
      case 202: $this->statusText = 'Accepted'; break;
      case 203: $this->statusText = 'Non-Authoritative Information'; break;
      case 204: $this->statusText = 'No Content'; break;
      case 205: $this->statusText = 'Reset Content'; break;
      case 206: $this->statusText = 'Partial Content'; break;
      case 207: $this->statusText = 'Multi-Status (WebDAV)'; break;
      case 208: $this->statusText = 'Already Reported (WebDAV)'; break;
      case 226: $this->statusText = 'IM Used'; break;

      case 300: $this->statusText = 'Multiple Choices'; break;
      case 301: $this->statusText = 'Moved Permanently'; break;
      case 302: $this->statusText = 'Found'; break;
      case 303: $this->statusText = 'See Other'; break;
      case 304: $this->statusText = 'Not Modified'; break;
      case 305: $this->statusText = 'Use Proxy'; break;
      case 307: $this->statusText = 'Temporary Redirect'; break;
      case 308: $this->statusText = 'Permanent Redirect (experiemental)'; break;

      case 400: $this->statusText = 'Bad Request'; break;
      case 401: $this->statusText = 'Unauthorized'; break;
      case 402: $this->statusText = 'Payment Required'; break;
      case 403: $this->statusText = 'Forbidden'; break;
      case 404: $this->statusText = 'Not Found'; break;
      case 405: $this->statusText = 'Method Not Allowed'; break;
      case 406: $this->statusText = 'Not Acceptable'; break;
      case 407: $this->statusText = 'Proxy Authentication Required'; break;
      case 408: $this->statusText = 'Request Timeout'; break;
      case 409: $this->statusText = 'Conflict'; break;
      case 410: $this->statusText = 'Gone'; break;
      case 411: $this->statusText = 'Length Required'; break;
      case 412: $this->statusText = 'Precondition Failed'; break;
      case 413: $this->statusText = 'Request Entity Too Large'; break;
      case 414: $this->statusText = 'Request-URI Too Long'; break;
      case 415: $this->statusText = 'Unsupported Media Type'; break;
      case 416: $this->statusText = 'Requested Range Not Satisfiable'; break;
      case 417: $this->statusText = 'Expectation Failed'; break;
      case 418: $this->statusText = 'I\'m a teapot (RFC 2324)'; break;
      case 420: $this->statusText = 'Enhance Your Calm (Twitter)'; break;
      case 422: $this->statusText = 'Unprocessable Entity (WebDAV))'; break;
      case 423: $this->statusText = 'Locked (WebDAV)'; break;
      case 424: $this->statusText = 'Failed Dependency (WebDAV)'; break;
      case 425: $this->statusText = 'Reserved for WebDAV'; break;
      case 426: $this->statusText = 'Upgrade Required'; break;
      case 428: $this->statusText = 'Precondition Required'; break;
      case 429: $this->statusText = 'Too Many Requests'; break;
      case 431: $this->statusText = 'Request Header Fields Too Large'; break;
      case 444: $this->statusText = 'No Response (Nginx)'; break;
      case 449: $this->statusText = 'Retry With (Microsoft)'; break;
      case 450: $this->statusText = 'Blocked by Windows Parental Controls (Microsoft)'; break;
      case 451: $this->statusText = 'Unavailable For Legal Reasons'; break;
      case 499: $this->statusText = 'Client Closed Request (Nginx)'; break;

      case 500: $this->statusText = 'Internal Server Error'; break;
      case 501: $this->statusText = 'Not Implemented'; break;
      case 502: $this->statusText = 'Bad Gateway'; break;
      case 503: $this->statusText = 'Service Unavailable'; break;
      case 504: $this->statusText = 'Gateway Timeout'; break;
      case 505: $this->statusText = 'HTTP Version Not Supported'; break;
      case 506: $this->statusText = 'Variant Also Negotiates (Experimental)'; break;
      case 507: $this->statusText = 'Insufficient Storage (WebDAV)'; break;
      case 508: $this->statusText = 'Loop Detected (WebDAV)'; break;
      case 509: $this->statusText = 'Bandwidth Limit Exceeded (Apache)'; break;
      case 510: $this->statusText = 'Not Extended'; break;
      case 511: $this->statusText = 'Network Authentication Required'; break;
      case 598: $this->statusText = 'Network read timeout error'; break;
      case 599: $this->statusText = 'Network connect timeout error'; break;

      default: $this->statusText = '(Unused)'; break;
    }
    # Return the text
    return $this->statusText;
  }

  /**
   * Generate a succss response, with HTTP status code $code and body $body
   *
   * @param  int    $code       2xx series HTTP status code
   * @param  string $body       Optional body to send
   */
  function success(int $code, string $body=''): \Nofuzz\Http\HTTPResponse
  {
    $this->setStatusCode($code);
    $this->setBody($body);

    return $this;
  }

  /**
   * Generate a redirect response, with HTTP status code $code to $url
   *
   * @param  int    $code       3xx series HTTP status code
   * @param  string $url        URL to redirect to ("Location:" header)
   */
  function redirect(int $code, string $url): \Nofuzz\Http\HTTPResponse
  {
    $this->setStatusCode($code);
    $this->setHeader('Location',$url);
    $this->setBody('');

    return $this;
  }

  /**
   * Generate an error response, with HTTP status code $code and body $body
   *
   * @param   int    $code       4xx or 5xx series HTTP status code
   * @param   string $body       Optional body to send
   * @return  self
   */
  function error(int $code, string $body=''): \Nofuzz\Http\HTTPResponse
  {
    $this->setStatusCode($code);
    $this->setBody($body);

    return $this;
  }

  /**
   * Generate an Error Response (in JSON)
   *
   * @param   int    $code          HTTP Code to set
   * @param   string $message       Message to send
   * @param   string $details       Details. Optional.
   * @param   int|integer $options  Optional json_encode() options. Defaults to JSON_PRETTY_PRINT to maintain backwards compatibilitiy
   * @return  self
   */
  public function errorJson(int $code, string $message, string $details='', int $options=JSON_PRETTY_PRINT): \Nofuzz\Http\HTTPResponse
  {
    $message = $this->setTextFromCode($code, $message);

    # Build array
    $data = array();
    $data['message'] = $message;
    if (strlen($details)>0) $data['details'] = $details;

    # Set Headers
    $this->setContentType('application/json');

    return $this->error($code,json_encode($data,$options));
  }

  #
  # Getters
  #

  /**
   * Gets the value of statusCode
   *
   * @return int
   */
  public function getStatusCode(): int
  {
    return $this->statusCode;
  }

  /**
   * Gets the value of statusText
   *
   * @return string
   */
  public function getStatusText(): string
  {
    return $this->statusText;
  }

  /**
   * Gets all headers as array
   *
   * @return array
   */
  public function getHeaders()
  {
    return $this->headers;
  }

  /**
   * Gets the value of a header
   *
   * @param string $header        header to fetch
   * @return string               Empty if header is not found
   */
  public function getHeader(string $header, string $default=''): string
  {
    return array_change_key_case( $this->headers, CASE_LOWER)[ strtolower($header) ] ?? $default;
  }

  /**
   * Gets the value of body
   *
   * @return mixed
   */
  public function getBody()
  {
    return $this->body;
  }

  /**
   * Gets the value of filename
   *
   * @return mixed
   */
  public function getFileBody(): string
  {
    return $this->fileBody;
  }

  /**
   * Gets the value of CharSet
   *
   * @return mixed
   */
  public function getCharSet()
  {
    return $this->charSet;
  }

  #
  # Setters
  #

  /**
   * Sets the value of statusCode
   */
  public function setStatusCode(int $code)
  {
    $this->statusCode = $code;

    return $this;
  }

  /**
   * Sets the value of statusText
   */
  public function setStatusText(string $text)
  {
    $this->statusText = $text;

    return $this;
  }

  /**
   * Sets the value of headers
   */
  public function setHeaders(array $headers)
  {
    $this->headers = $headers;

    return $this;
  }

  /**
   * Sets a header (preserves the case of header keys)
   */
  public function setHeader(string $header, string $value)
  {
    $found = false;
    # Teaverse the headers, searching for a matching header (case insensitive)
    foreach ( $this->headers as $idx => $oldValue )
    {
      if (strcasecmp($this->headers[$idx],$header)==0)
      {
        # Found the header, now replace the value
        $found = true;
        $this->headers[$idx] = $value;
        break;
      }
    }
    # Add the header
    if (!$found) {
      $this->headers[$header] = $value;
    }

    return $this;
  }

  /**
   * Sets the value of body
   */
  public function setBody(string $body='')
  {
    $this->body = $body;

    return $this;
  }

  /**
   * setJsonBody - Encodes array as JSON response
   *
   * @param   array       $data     Array to send as JSON
   * @param   int|integer $options  Optional json_encode() options. Defaults to JSON_PRETTY_PRINT to maintain backwards compatibilitiy
   * @return  self
   */
  public function setJsonBody(array $data, int $options=JSON_PRETTY_PRINT)
  {
    $this->setContentType('application/json')
         ->setBody( json_encode($data,$options) );

    return $this;
  }

  /**
   * Set a file as the response, with optional content-type
   *
   * @param string $fileBody        [description]
   * @param string $contentType     [description]
   */
  public function setFileBody(string $fileBody, string $contentType='')
  {
    # Clear the normal body
    $this->setBody('');

    # Set content type
    if (!empty($contentType)) {
      $this->setContentType($contentType);
    }

    # Set the filename
    $this->fileBody = $fileBody;

    return $this;
  }

  /** Sets the value of CharSet */
  public function setCharSet(string $charSet)
  {
    $this->charSet = $charSet;
    return $this;
  }

  /**
   * Set header Cache-Control
   */
  public function setCacheControl(string $value)
  {
    $this->cacheControl = $value;
    return $this;
  }

  /**
   * Set header Content-Type
   */
  public function setContentType(string $value)
  {
    $this->setHeader('Content-Type', $value);

    return $this;
  }

  /**
   * Set Cookies array
   *
   * @param array $cookieArray [description]
   */
  public function setCookies(array $cookieArray)
  {
    $this->cookies = $cookieArray;

    return $this;
  }

  /**
   * Sets a Cookie in the Response
   *
   * @param string       $name     [description]
   * @param string       $value    [description]
   * @param int|integer  $expire   [description]
   * @param string       $path     [description]
   * @param string       $domain   [description]
   * @param bool|boolean $secure   [description]
   * @param bool|boolean $httponly [description]
   */
  public function setCookie(string $name, string $value='', int $expire=0, string $path='', string $domain='', bool $secure=false, bool $httponly=false)
  {
    # Prepare the Cookie
    $cookie = array();
    $cookie['name'] = $name;
    $cookie['value'] = $value;
    $cookie['expire'] = $expire;
    $cookie['path'] = $path;
    $cookie['domain'] = $domain;
    $cookie['secure'] = $secure;
    $cookie['httponly'] = $httponly;
    # Add the cookie to the cookies array
    $this->cookies[$name] = $cookie;

    return $this;
  }

}
