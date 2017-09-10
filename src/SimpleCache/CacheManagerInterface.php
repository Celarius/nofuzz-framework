<?php
/**
 * CacheManagerInterface
 *
 * @package Nofuzz
 */
#################################################################################################################################

namespace Nofuzz\SimpleCache;

interface CacheManagerInterface
{
  /**
   * Get an array with all installed / supported driver names
   *
   * @return  array   An array with the possible driver names
   */
  public function getDriverNames(): array;

  /**
   * Generate a new Cache of $driver type
   *
   * @param  string $driver
   * @return object
   */
  public function createCache(string $driverName, array $options=null): \Nofuzz\SimpleCache\CacheInterface;

  /**
   * Get a created Cache Driver
   *
   * @param  string $driver
   * @return object
   */
  public function getCache(string $driverName): \Nofuzz\SimpleCache\CacheInterface;

}
