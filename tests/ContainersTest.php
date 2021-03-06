<?php declare(strict_types=1);

namespace Nofuzz;

/**
 * ContainerTestClass class
 */
class ContainerTestClass
{
  protected $property = '';

  public function getProperty()
  {
    return $this->property;
  }

  public function setProperty(string $value)
  {
    $this->property = $value;
  }
}

/**
 * ContainersTest class
 */
class ContainersTest extends \PHPUnit\Framework\TestCase
{
  protected $app;
  protected $secret;

  /** Setup test */
  public function setup()
  {
    global $app;
    $this->app = $app;
  }

  /** Test Container STRING */
  public function testContainerString()
  {
    $a = 'My Container String';

    # Set it
    $this->app->container('string', $a);

    # Get it
    $b = $this->app->container('string');

    $this->assertEquals($a, $b);
  }

  /** Test Container ARRAY */
  public function testContainerArray()
  {
    $a = [ 'a'=>'a value','b'=>'b value','c'=>'c value' ];

    # Set it
    $this->app->container('array', $a);

    # Get it
    $b = $this->app->container('array');

    $this->assertEquals($a, $b);
  }

  /** Test Container Object */
  public function testContainerObject()
  {
    $a = new ContainerTestClass();
    $a->setProperty('I get set, therefore I exist');

    # Set it
    $this->app->container('object', $a);

    # Get it
    $b = $this->app->container('object');

    $this->assertEquals($a->getProperty(), $b->getProperty());
  }

  /** Test Container Callable */
  public function testContainerCallable()
  {
    # Set it
    $this->app->container('adder',
      function(int $x, int $y) {
        return $x+$y;
      }
    );

    # Call it
    $value = $this->app->container('adder')(1,2);

    $this->assertEquals(3, (int) $value);
  }


}
