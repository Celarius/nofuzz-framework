<?php declare(strict_types=1);

namespace Nofuzz;

class AppTest extends \PHPUnit_Framework_TestCase {
{
  protected $app;

  public function testAppCreate()
  {
    $this->app = new \Nofuzz\Application( realpath(__DIR__) );

    $this->assertSame($this->app->getBasePath(), realpath(__DIR__));
  }

}
