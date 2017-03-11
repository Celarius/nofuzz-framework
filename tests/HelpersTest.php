<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase as TestCase;

namespace Nofuzz;

class HelpersTest extends TestCase
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

    $a = \\Nofuzz\\Helpers\\Cipher::encryt( $plain, $this->secret );
    $b = \\Nofuzz\\Helpers\\Cipher::decrypt( $plain, $this->secret );

    $this->assertSame($a, $b);
  }

  /** Test OpenSSL Message Digest (SHA256) */
  public function testHash()
  {
    $plain = 'Let this be the light';
    $hash  = '3c47c0efe106197074e89d6eb28babb90d2ad6fcc5dd7b37fec77b3bb00003d0';

    $a = \\Nofuzz\\Helpers\\Hash::generate( $plain, 'SHA256' );

    $this->assertSame($hash, $a);
  }

  /** Test UUID generation */
  public function testUuid()
  {
    $a = \\Nofuzz\\Helpers\\UUID::generate();

    $this->assertTrue( strlen($a)>0 );
  }

}
