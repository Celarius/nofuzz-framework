<?php
/**
 * Firebird Database Connection (extends PDO)
 *
 * @package     NOFUZZ
*/
################################################################################################################################
/*
FIREBIRD:
  $connection = new PDO("firebird:dbname=<hostname>/<port>:C:\\path\\filename.fdb", $user, $pass,
    array(
      PDO::ATTR_PERSISTENT => true
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_AUTOCOMMIT => false
    )
  );
  setAttribute(PDO::FB_ATTR_TIMESTAMP_FORMAT, '%s')
  setAttribute(PDO::FB_ATTR_DATE_FORMAT, '%s' )
  setAttribute(PDO::FB_ATTR_TIME_FORMAT, '%S' )
  setAttribute(PDO::FB_ATTR_TIMESTAMP_FORMAT, '%s' )
*/

namespace Nofuzz\Database\Drivers\Pdo;

class Firebird extends \Nofuzz\Database\PdoConnection
{
  /**
   * Constructor
   *
   * @param string $connectionName [description]
   * @param array  $params         [description]
   */
  public function __construct(string $connectionName, array $params=[])
  {
    # Firebird default PDO options
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
   * Get DSN - Firebird formatting
   *
   * @return string       [description]
   */
  public function getDsn(): string
  {
    # Build the DSN
    $_dsn = $this->getDriver().':'.
            'host='.$this->GetHost().($this->GetPort()!=0 ? ':'.$this->GetPort() : '' ).';'.
            'dbname='.$this->GetSchema().';'.
            'charset='.$this->GetCharset();
    # Set it
    $this->setDsn($_dsn);
    # Results
    return $this->pdo_dsn;
  }

}
