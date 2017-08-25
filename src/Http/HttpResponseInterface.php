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

interface HttpResponseInterface
{
  function clear();
  function setCompression(bool $active=true, int $level=-1);
  function send();

  # Getters
  function getStatusCode(): int;
  function getStatusText(): string;
  function getCookies()
  function getHeaders()
  function getHeader(string $header, string $default=''): string;
  function getBody();
  function getFileBody(): string;
  function getCharSet();

  # Setters
  function setStatusCode(int $code);
  function setHeaders(array $headers);
  function setHeader(string $header, string $value);
  function setBody(string $body='');
  function setJsonBody(array $data, int $options=JSON_PRETTY_PRINT);
  function setFileBody(string $fileBody, string $contentType='');
  function setCacheControl(string $value);
  function setContentType(string $value);
  function setCharSet(string $charSet);
  function setCookie(string $name, string $value="", int $expire=0, string $path="", string $domain="", bool $secure=false, bool $httponly=false);

  # Response Generator methods
  function success(int $code, string $body=''): \Nofuzz\Http\HTTPResponse;
  function redirect(int $code, string $url): \Nofuzz\Http\HTTPResponse;
  function error(int $code, string $body=''): \Nofuzz\Http\HTTPResponse;
  function errorJson(int $code, string $message, string $details='', int $options=JSON_PRETTY_PRINT): \Nofuzz\Http\HTTPResponse;
}
