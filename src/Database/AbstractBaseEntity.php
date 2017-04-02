<?php
/**
 * BaseDbObject.php
 *
 * @package     Service Registry
 */
#######################################################################################################################

namespace Nofuzz\Database;

abstract class AbstractBaseEntity
{
  /**
   * Constructor
   *
   * @param array $args [description]
   */
  public function __construct($args=null)
  {
    if ( is_array($args) ) {
      # Decode from array
      $this->fromArray($args);
    } else
    if ( is_string($args) ) {
      # Decoe from JSON string
      $this->fromJSON($args);
    } else {
      # Just clear properties
      $this->clear();
    }
  }


  /**
   * Clear properties
   */
  public function clear()
  {
    return $this;
  }

  /**
   * Return properties as Array
   *
   * @return array              Array with properties
   */
  public function fromArray(array $a)
  {
    return $this;
  }

  /**
   * Return properties as Array
   *
   * @return array              Array with properties
   */
  public function asArray(): array
  {
    return $a;
  }

}
