<?php
/**
 * Nofuzz\Helpers\Hash
 *
 *   OpenSSL Hash Helper. Wraps the OpenSSL digest() method into easily usable helper method
 *
 * Example:
 *   $digest = \Nofuzz\Helpers\Hash::generate('This is the data','SHA256');
 *
 * @package  Nofuzz
 */
#################################################################################################################################

namespace Nofuzz\Helpers;

class Hash implements \Nofuzz\Helpers\HashInterface
{

  /**
   * Genreate a Hash (digest) of the $data using $method
   *
   * @param  string $data       [description]
   * @param  string $method     [description]
   * @return string
   */
  public static function generate(string $data, string $method='SHA256'): string
  {
    $hash = '';
    $hash = openssl_digest($data,$method);

    return $hash;
  }

  /**
   * Check that the $hash of $data is correct, using $method
   *
   * @param  string $data       [description]
   * @param  string $hash       [description]
   * @param  string $method     [description]
   * @return bool
   */
  public static function check(string $data, string $hash, string $method='SHA256'): bool
  {
    $hash_compare = openssl_digest($data,$method);

    return (strcmp($hash, $hash_compare) === 0);
  }


  /**
   * Return array of hash Methods available
   *
   * @return array
   */
  public static function getMethods(): array
  {
    return openssl_get_md_methods();
  }

}
