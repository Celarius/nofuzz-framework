<?php
/**
 * UUID Helper Interface
 *
 * @package  Nofuzz
 */
#################################################################################################################################

namespace Nofuzz\Helpers;

interface UUIDInterface
{
  /**
   * Generate v4 UUID
   *
   * @return string
   */
  public static function generate(): string;
}
