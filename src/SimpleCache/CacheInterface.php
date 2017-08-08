<?php
/**
 * SimpleCacheInterface
 *
 * PSR-16 compatible interface (SimpleCache)
 *
 */
#################################################################################################################################

namespace Nofuzz\SimpleCache;

interface CacheInterface extends \Psr\SimpleCache\CacheInterface
{
  #
  # PSR-16 methods :: https://github.com/php-fig/simple-cache/blob/master/src/CacheInterface.php
  #
  // public function get($key, $default = null);
  // public function set($key, $value, $ttl = null);
  // public function delete($key);
  // public function clear();
  // public function getMultiple($keys, $default = null);
  // public function setMultiple($items, $ttl = null);
  // public function deleteMultiple($keys);
  // public function has($key);

  #
  # Custom added methods
  #

 /**
   * Increment a $key's value and return the new value.
   *
   * @param  string      $key       Key name to increment
   * @param  int|integer $amount    Amount to increment with (default 1)
   * @return int|bool
   */
  public function inc(string $key, int $amount=1): int;

  /**
   * Decrement a $key's value and return the new value.
   *
   * @param  string      $key       Key name to decrement
   * @param  int|integer $amount    Amount to decrement with (default 1)
   * @return int|bool
   */
  public function dec(string $key, int $amount=1): int;

  /**
   * Get Cache Driver name
   *
   * @return string
   */
  public function getDriver(): string;

  /**
   * Get Cache Client Version
   *
   * @return string
   */
  public function getVersion(): string;

}
