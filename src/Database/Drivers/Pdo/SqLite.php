<?php
/**
 * MySQL Database Connection (extends PDO)
 *
 * @package     NOFUZZ
*/
################################################################################################################################

namespace Nofuzz\Database\Drivers\Pdo;

class SqLite extends \Nofuzz\Database\PdoConnection
{
  protected $filename;

  /**
   * Constructor
   *
   * @param string $connectionName [description]
   * @param array  $params         [description]
   */
  public function __construct(string $connectionName, array $params=[])
  {
    # Sqlite has it's own $filename property, extract it
    $this->setFilename($params['filename'] ?? '');

    # Parent Constructor (\Nofuzz\Database\PdoConnection)
    parent::__construct($connectionName,$params);
  }

  /**
   * Get DSN - SqLite formatting
   *
   *   sqlite:/tmp/foo.db
   *
   * @return string       [description]
   */
  public function getDsn(): string
  {

    # Build the DSN
    $_dsn = $this->getDriver().':'.$this->getFilename();
    # Set it
    $this->setDsn($_dsn);
    # Results
    return $this->pdo_dsn;
  }

  /**
   * Get the Filename
   *
   * @return string
   */
  public function getFilename(): string
  {
    return $this->filename;
  }

  /**
   * Set filename
   *
   * @param   string $filename
   * @return  self
   */
  public function setFilename(string $filename)
  {
    $this->filename = $filename;

    return $this;
  }

}
