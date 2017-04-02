<?php
/**
 * BaseDAO
 *
 * @package     Nofuzz
*/
################################################################################################################################

namespace Nofuzz\Database;

interface BaseDaoInterface
{
  /**
   * Constructor
   *
   * @param \Nofuzz\Database\PdoConnectionInterface $connection
   */
  function __construct(string $connectionName);

  /**
   * Get the DB connection assinged to this DAO object
   *
   * @return null|\Nofuzz\Database\PdoConnectionInterface
   */
  function getConnection();

  /**
   * Get the DB connection assinged to this DAO object
   *
   * @return null|\Nofuzz\Database\PdoConnectionInterface
   */
  function db();

  /**
   * Begin transaction if not already started
   *
   * @return bool
   */
  function beginTransaction();

  /**
   * Commit active transaction
   *
   * @return bool
   */
  function commit();

  /**
   * Rollback active transaction
   *
   * @return bool
   */
  function rollback();
}
