<?php
/**
 * MySQL Database Connection (extends PDO)
 *
 * @package     NOFUZZ
*/
################################################################################################################################

namespace Nofuzz\Database\Drivers\Pdo;

class MySql extends \Nofuzz\Database\PdoConnection
{
  /**
   * Constructor
   *
   * @param string $dbHost    [description]
   * @param int    $dbPort    [description]
   * @param string $dbName    [description]
   * @param string $dbUser    [description]
   * @param string $dbPass    [description]
   * @param string $dbCharset [description]
   * @param array  $dbOpts    [description]
   */
  public function __construct(string $dbHost, int $dbPort, string $dbName, string $dbUser='', string $dbPass='', string $dbCharset='UTF8', array $dbOpts=array())
  {
    # MySql Default options
    if (count($dbOpts)==0) {
      $dbOpts =
        array(
          \PDO::ATTR_PERSISTENT => TRUE,
          \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
          \PDO::ATTR_AUTOCOMMIT => FALSE
        );
    }
    # Parent Constructor - \Nofuzz\Database\Connection
    parent::__construct('mysql',$dbHost,$dbPort,$dbName,$dbUser,$dbPass,$dbCharset,$dbOpts);

    # Set the Default Database so we do not have to add $schema to SQL statements
    if ($this->beginTransaction()) {
      if ($sth = $this->query('USE '.$dbName)) {
        $sth->closeCursor();
      }
      $this->commit();
    }
  }

  /**
   * Get DSN - MySql formatting
   *
   * @return string       [description]
   */
  public function getDsn(): string
  {
    # Build the DSN
    $_dsn = $this->getDriver().':'.
            'host='.$this->getHost().($this->getPort()!=0 ? ';port='.$this->getPort() : '' ).
            ';dbname='.$this->getName().
            ';charset='.$this->getCharset();
    # Set it
    $this->setDsn($_dsn);
    # Results
    return $this->pdo_dsn;
  }

}
