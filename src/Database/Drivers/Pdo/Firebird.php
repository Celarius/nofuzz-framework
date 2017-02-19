<?php
/**
 * Firebird Database Connection (extends PDO)
 *
 * @package     NOFUZZ
 * @copyright   Copyright (c) 2016, Winwap Technologies
 * @author      Kim Sandell <sandell@celarius.com>
*/
################################################################################################################################

namespace Nofuzz\Database\Drivers\Pdo;

class Firebird extends \Nofuzz\Database\PdoConnection
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
    # Firebird options
    if (count($dbOpts)==0) {
      $dbOpts =
        array(
          \PDO::ATTR_PERSISTENT => TRUE,
          \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
          // \PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
          \PDO::ATTR_AUTOCOMMIT => FALSE
        );
    }
    # Utilize Parent Constructor - \Nofuzz\Database\Connection
    parent::__construct('firebird',$dbHost,$dbPort,$dbName,$dbUser,$dbPass,$dbCharset,$dbOpts);
  }

  /**
   * Get DSN - Firebird formatting
   *
   * @return string       [description]
   */
  public function getDsn(): string
  {
    # Build the DSN
    $_dsn = $this->getDriver().':'.
            'dbname='.$this->GetHost().':'.($this->GetPort()!=0 ? ':'.$this->GetPort().':' : '' ).$this->GetName().';'.
            'charset='.$this->GetCharset();
    # Set it
    $this->setDsn($_dsn);
    # Results
    return $this->pdo_dsn;
  }

}
