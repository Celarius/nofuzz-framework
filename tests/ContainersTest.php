<?php declare(strict_types=1);

namespace Nofuzz;

/** ContainerTestClass */
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

class ContainersTest extends \PHPUnit\Framework\TestCase
{
  protected $app;
  protected $secret;

  /** Setup test */
  public function setup()
  {
    $this->app = new \Nofuzz\Application( realpath(__DIR__) );
  }

  /** Test Container STRING */
  public function testContainerString()
  {
    $a = 'My Container String';

    # Set it
    app()->container('string', $a);

    # Get it
    $b = app()->container('string');

    $this->assertEquals($a, $b);
  }

  /** Test Container ARRAY */
  public function testContainerArray()
  {
    $a = [ 'a'=>'a value','b'=>'b value','c'=>'c value' ];

    # Set it
    app()->container('array', $a);

    # Get it
    $b = app()->container('array');

    $this->assertEquals($a, $b);
  }

  /** Test Container Object */
  public function testContainerObject()
  {
    $a = new \ContainerTestClass();
    $a->setProperty('I get set, therefore I exist');

    # Set it
    app()->container('object', $a);

    # Get it
    $b = app()->container('object');

    $this->assertEquals($a->getProperty(), $b->getProperty());
  }


  /** Test Container Callable */
  public function testContainerCallable()
  {
    # Set it
    app()->container('adder',
      function(int $x, int $y) {
        return $x+$y;
      }
    );

    # Call it
    $value = app()->container('adder')(1,2);

    $this->assertEquals(3, (int) $value);
  }


}
