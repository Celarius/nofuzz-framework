<?php
/**
 * ConfigInterface
 *
 * @package Nofuzz
 */

#################################################################################################################################

namespace Nofuzz\Config;

interface ConfigInterface
{
  function clear(): \Nofuzz\Config\Config;

  /**
   * Load Configuration file
   *
   * @param  string $filename
   * @return self
   */
  function load(string $filename): \Nofuzz\Config\Config;

  /**
   * Save Configuration file
   *
   * @param  string $filename
   * @return self
   */
  function save(string $filename=null): bool;

  /**
   * Get a config item
   *
   * @param  string $key          "." notationed key to retreive
   * @param  string $default      Optional Default value if group::section::key not found
   * @return mixed
   */
  function get(string $key,string $default=null);

  /**
   * Set a config item
   *
   * @param  string $key          "." notationed key to retreive
   * @param  mixed $value         Value to set
   * @return self
   */
  function set(string $key, $value): \Nofuzz\Config\Config;

  /**
   * Get config filename
   *
   * @return array
   */
  function getFilename(): string;

  /**
   * Return all config values
   *
   * @return array
   */
  function getValues(): array;

}
