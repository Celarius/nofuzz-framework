<?php
/**
 * CacheManager
 *
 * Cache manager class, has methods for return list of drivers
 * and getCache($driver) for return an instance of a specific cache driver.
 *
 * PSR-16 compatible (SimpleCache)
 */
#################################################################################################################################

namespace Nofuzz\SimpleCache;

class CacheManager implements \Nofuzz\SimpleCache\CacheManagerInterface
{
  protected $driverName;
  protected $drivers;

  /**
   * Constructor
   */
  public function __construct()
  {
    $this->driverNames = array();
    $this->drivers = array();
  }

  /**
   * Get an array with all installed / supported driver names
   *
   * @return  array   An array with the possible driver names
   */
  public function getDriverNames(): array
  {
    if ($this->isAPCuAvailable()) {
      $this->driverNames[] = 'apcu';
    }

    return $this->driverNames;
  }


  /**
   * Generate a new Cache of $driver type
   *
   * @param  string $driver
   * @return object
   */
  public function createCache(string $driverName, array $options=null): \Nofuzz\SimpleCache\CacheInterface
  {
    # Build the name
    $driverClassName = '\\Nofuzz\\SimpleCache\\Drivers\\'.ucfirst($driverName);

    # Create the Class
    $driverClass = new $driverClassName($options);

    # Add to the list of drivers
    if ( !is_null($driverClass) ) {
      $this->drivers[strtolower($driverName)] = $driverClass;
    }

    return $driverClass;
  }

  /**
   * Get a generated Cache Driver
   *
   * @param  string $driverName
   * @return null | object
   */
  public function getCache(string $driverName=''): \Nofuzz\SimpleCache\CacheInterface
  {
    if (empty($driverName)) {
      # Return the 1st Cache in the list
      return (array_values($this->drivers)[0] ?? null);
    } else {
      # Return the Cache for the $driverName
      return $this->drivers[strtolower($driverName)] ?? null;
    }
  }


  /**
   * isAPCuAvailable
   *
   * Checks if the APCu extension is loaded and enabled
   */
  protected function isAPCuAvailable(): bool
  {
    # Check if APCu loaded and available
    return ( extension_loaded('apcu') && (ini_get('apc.enabled')==='1') );
  }

}
