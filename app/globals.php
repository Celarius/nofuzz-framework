<?php
/**
 * globals.php
 *
 * Application Global Functions & Variables
 *
 * @package  [Application]
 */


if (!function_exists('srGet')) {
  /**
   * Do a SrCLient GET
   *
   * @param  string $service     [description]
   * @param  string $environment [description]
   * @param  string $path        [description]
   * @param  string $query       [description]
   * @param  array  $headers     [description]
   * @return Guzzle Response
   */
  function srGet(string $service, string $environment, string $path, string $query='',array $headers=[])
  {
    global $app;
    # Get the SrClient from the Dependency Container, or create it there
    $srClient = app()->container('srClient');
    # Call the SrClient
    $response = $srClient->doRequest('GET',$service,$environment,$path,$query,null,$headers);

    return $response;
  }
}

if (!function_exists('srPost')) {
  /**
   * Do a srCLient POST
   *
   * @param  string $service     [description]
   * @param  string $environment [description]
   * @param  string $path        [description]
   * @param  string $query       [description]
   * @param  string $body        [description]
   * @param  array  $headers     [description]
   * @return Guzzle Response
   */
  function srPost(string $service, string $environment, string $path, string $query, string $body,array $headers=[])
  {
    global $app;
    # Get the SrClient from the Dependency Container, or create it there
    $srClient = app()->container('srClient');
    # Call the SrClient
    $response = $srClient->doRequest('POST',$service,$environment,$path,$query,$body,$headers);

    return $response;
  }
}

if (!function_exists('srPut')) {
  /**
   * Do a srCLient PUT
   *
   * @param  string $service     [description]
   * @param  string $environment [description]
   * @param  string $path        [description]
   * @param  string $query       [description]
   * @param  string $body        [description]
   * @param  array  $headers     [description]
   * @return Guzzle Response
   */
  function srPut(string $service, string $environment, string $path, string $query, string $body,array $headers=[])
  {
    global $app;
    # Get the SrClient from the Dependency Container, or create it there
    $srClient = app()->container('srClient');
    # Call the SrClient
    $response = $srClient->doRequest('PUT',$service,$environment,$path,$query,$body,$headers);

    return $response;
  }
}

if (!function_exists('srPatch')) {
  /**
   * Do a srCLient PATCH
   *
   * @param  string $service     [description]
   * @param  string $environment [description]
   * @param  string $path        [description]
   * @param  string $query       [description]
   * @param  string $body        [description]
   * @param  array  $headers     [description]
   * @return Guzzle Response
   */
  function srPatch(string $service, string $environment, string $path, string $query, string $body,array $headers=[])
  {
    global $app;
    # Get the SrClient from the Dependency Container, or create it there
    $srClient = app()->container('srClient');
    # Call the SrClient
    $response = $srClient->doRequest('PATCH',$service,$environment,$path,$query,$body,$headers);

    return $response;
  }
}

if (!function_exists('srDelete')) {
  /**
   * Do a srCLient DELETE
   *
   * @param  string $service     [description]
   * @param  string $environment [description]
   * @param  string $path        [description]
   * @param  string $query       [description]
   * @param  string $body        [description]
   * @param  array  $headers     [description]
   * @return Guzzle Response
   */
  function srDelete(string $service, string $environment, string $path, string $query='', array $headers=[])
  {
    global $app;
    # Get the SrClient from the Dependency Container, or create it there
    $srClient = app()->container('srClient');
    # Call the SrClient
    $response = $srClient->doRequest('DELETE',$service,$environment,$path,$query,null,$headers);

    return $response;
  }
}

if (!function_exists('srDelete')) {
  /**
   * Do a srCLient OPTIONS
   *
   * @param  string $service     [description]
   * @param  string $environment [description]
   * @param  string $path        [description]
   * @param  string $query       [description]
   * @param  string $body        [description]
   * @param  array  $headers     [description]
   * @return Guzzle Response
   */
  function srOptions(string $service, string $environment, string $path, string $query='', array $headers=[])
  {
    global $app;
    # Get the SrClient from the Dependency Container, or create it there
    $srClient = app()->container('srClient');
    # Call the SrClient
    $response = $srClient->doRequest('OPTIONS',$service,$environment,$path,$query,null,$headers);

    return $response;
  }
}

#################################################################################################################################
#
# Register & Create dependencies
#
#################################################################################################################################

# Register srClient
$srClient = (new \App\Clients\Sr\Client());
app()->container('srClient', $srClient);
