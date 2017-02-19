<?php
/**
 * ServerMiddleware
 *
 * @package     Nofuzz
 */
#################################################################################################################################

namespace Nofuzz;

abstract class Middleware implements \Nofuzz\MiddlewareInterface
{
  /**
   * Let the Middleware do it's job
   *
   * @param  array  $args           URI parameters as key=value array
   * @return bool                   True=OK, False=Failed to handle it
   */
  function handle(array $args): bool
  {
    return true;
  }

}
