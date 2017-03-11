<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

namespace Nofuzz;

class AppTest extends TestCase
{
  protected $app;

  public function testAppCreate()
  {
    $this->app = new \Nofuzz\Application( realpath(__DIR__) );

    $this->assertSame($this->app->getBasePath(), realpath(__DIR__));
  }

}
