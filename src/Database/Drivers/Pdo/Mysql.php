<?php
/**
 * MySQL Database Connection (extends PDO)
 *
 * @package     NOFUZZ
*/
################################################################################################################################
/*
MYSQL:
  $connection = new \PDO('mysql:host=localhost;port=3306;dbname=test', $user, $pass,
    array(
      \PDO::ATTR_PERSISTENT => true
      \PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      \PDO::ATTR_AUTOCOMMIT => FALSE,
      \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"           // Set UTF8 as charset
    )
  );
*/

namespace Nofuzz\Database\Drivers\Pdo;

class MySql extends \Nofuzz\Database\PdoConnection
{
  /**
   * Constructor
   *
   * @param string $connectionName [description]
   * @param array  $params         [description]
   */
  public function __construct(string $connectionName, array $params=[])
  {
    # MySQL default PDO options
    if (count($params['options'] ?? [])==0) {
      $params['options'] = [
          \PDO::ATTR_PERSISTENT => TRUE,
          \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
          // \PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
          \PDO::ATTR_AUTOCOMMIT => FALSE
        ];
    }

    # Parent Constructor (\Nofuzz\Database\PdoConnection)
    parent::__construct($connectionName,$params);
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
            ';dbname='.$this->getSchema().
            ';charset='.$this->getCharset();
    # Set it
    $this->setDsn($_dsn);
    # Results
    return $this->pdo_dsn;
  }

}
