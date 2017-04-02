<?php
/**
 * BaseDAO
 *
 * @package     Nofuzz
*/
################################################################################################################################

namespace Nofuzz\Database;

abstract class AbstractBaseDao implements \Nofuzz\Database\AbstractBaseDaoInterface
{
  protected $connectionName;
  protected $connection;

  /**
   * Constructor
   *
   * @param \Nofuzz\Database\PdoConnectionInterface $connection
   */
  public function __construct(string $connectionName='')
  {
    $this->connectionName = $connectionName;
    $this->connection = null;
  }

  /**
   * Get the DB connection assinged to this DAO object
   *
   * @return null|\Nofuzz\Database\PdoConnectionInterface
   */
  public function getConnection()
  {
    $this->connection = db($this->connectionName);

    return $this->connection;
  }

  /**
   * Get the DB connection assinged to this DAO object
   *
   * @return null|\Nofuzz\Database\PdoConnectionInterface
   */
  public function db()
  {
    return $this->getConnection();
  }

  /**
   * Begin transaction if not already started
   *
   * @return bool
   */
  public function beginTransaction()
  {
    if (!$this->getConnection()->inTransaction()) {
      return $this->getConnection()->beginTransaction();
    }
    return false;
  }

  /**
   * Commit active transaction
   *
   * @return bool
   */
  public function commit()
  {
    if ($this->getConnection()->inTransaction()) {
      return $this->getConnection()->commit();
    }
    return false;
  }

  /**
   * Rollback active transaction
   *
   * @return bool
   */
  public function rollback()
  {
    if ($this->getConnection()->inTransaction()) {
      return $this->getConnection()->rollback();
    }
    return false;
  }

}
