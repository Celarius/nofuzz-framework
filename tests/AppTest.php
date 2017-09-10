<?php declare(strict_types=1);

namespace Nofuzz;

class AppTest extends \PHPUnit\Framework\TestCase
{
  protected $app;

  public function testAppCreate()
  {
    global $app;
    $this->app = $app;

    $this->assertSame($this->app->getBasePath(), realpath(__DIR__));
  }

}
