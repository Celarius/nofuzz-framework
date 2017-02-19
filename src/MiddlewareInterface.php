<?php
/**
 * ServerMiddlewareInterface
 *
 * @package     Nofuzz
 */
#################################################################################################################################

namespace Nofuzz;

interface MiddlewareInterface // extends MiddlewareInterface
{
  /**
   * Let the Middleware do it's job
   *
   * @param  array  $args           URI parameters as key=value array
   * @return bool                   True=OK, False=Failed to handle it
   */
  function handle(array $args): bool;

}
