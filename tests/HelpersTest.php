<?php declare(strict_types=1);

namespace Nofuzz;

class HelpersTest extends \PHPUnit\Framework\TestCase
{
  protected $app;
  protected $secret;

  /** Setup test */
  public function setup()
  {
    $this->app = new \Nofuzz\Application( realpath(__DIR__) );

    $this->secret = 'There be dragons here';
  }

  /** Test OpenSSL Encryption / Decryption */
  public function testCipher()
  {
    $plain = 'Let this be the light';

    $encrypted = \Nofuzz\Helpers\Cipher::encrypt( $plain, $this->secret );
    $a = \Nofuzz\Helpers\Cipher::decrypt( $encrypted, $this->secret );

    $this->assertEquals($plain, $a);
  }

  /** Test OpenSSL Message Digest (SHA256) */
  public function testHash()
  {
    $plain = 'Let this be the light';
    $hash  = '3c47c0efe106197074e89d6eb28babb90d2ad6fcc5dd7b37fec77b3bb00003d0';

    $a = \Nofuzz\Helpers\Hash::generate( $plain, 'SHA256' );

    $this->assertEquals($hash, $a);
  }

  /** Test UUID generation */
  public function testUuid()
  {
    $a = \Nofuzz\Helpers\UUID::generate();

    $this->assertTrue( strlen($a)>0 );
  }

}
