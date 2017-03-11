<?php
/**
 * OpenSSL Cipher Helper
 *
 *   Wraps the OpenSSL encrypt() and decrypt() methods into easily usable helper methods
 *
 *   $encryptedValue = \\Nofuzz\\Helpers\\Cipher::encrypt( $plain );
 *
 *   $plain = \\Nofuzz\\Helpers\\Cipher::decrypt( $encryptedValue );
 *
 * @package  Nofuzz
 */
#################################################################################################################################

namespace Nofuzz\Helpers;

class Cipher // implements \Nofuzz\Helpers\CipherInterface
{
  /**
   * Encrypt $data with $secret
   *
   * @param  string $data      [description]
   * @param  string $iv        [description]
   * @param  string $secret    [description]
   * @param  string $algorithm [description]
   * @return [type]            [description]
   */
  public static function encrypt(string $data, string $secret, string $algorithm='AES-256-CBC')
  {
    # If AES we will add a random 16 byte IV before the encrypted data
    if ( strtoupper(substr($algorithm,0,3))==='AES' ) {
      $iv = openssl_random_pseudo_bytes(16);
    } else {
      $iv = '';
    }

    # If no secret provided, use the one in config
    if (empty($secret))
      $secret = config()->get('application.secret');

    # Encrypt
    $result = openssl_encrypt($data,$algorithm,$secret,0,$iv);

    return base64_encode($iv.$result);
  }


  /**
   * Decrypt $data with $secret
   *
   * @param  string $data      [description]
   * @param  string $iv        [description]
   * @param  string $secret    [description]
   * @param  string $algorithm [description]
   * @return [type]            [description]
   */
  public static function decrypt(string $data, string $secret, string $algorithm='AES-256-CBC')
  {
    # if AES we extract the 16 bytes in the beginning as the IV
    if ( strtoupper(substr($algorithm,0,3))==='AES' ) {
      $encoded  = base64_decode($data);
      $iv       = substr($encoded,0,16);
      $encoded  = substr($encoded,16);
    } else {
      $encoded = $data;
      $iv = '';
    }

    # If no secret provided, use the one in config
    if (empty($secret))
      $secret = config()->get('application.secret');

    # Decrypt
    $result = openssl_decrypt($encoded,$algorithm,$secret,0,$iv);

    return $result;
  }

  /**
   * Return array of Cipher methods available
   *
   * @return array
   */
  public static function getMethods(): array
  {
    return openssl_get_cipher_methods();
  }

}
