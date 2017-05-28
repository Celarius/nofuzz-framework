<?php
/**
 * CockroachDb Database Connection (extends PDO)
 *
 * https://www.cockroachlabs.com
 *
 * @package     NOFUZZ
*/
################################################################################################################################
/*
  $connection = new PDO('pgsql:host=<hostname>;port=26257;dbname=bank;sslmode=disable', $user, $pass, 
    array(
      PDO::ATTR_ERRMODE          => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_EMULATE_PREPARES => true,
  ));
*/

################################################################################################################################
/*
  TODO
  * 'sslmode' support from the 'connection' in config.json
  * Add support for SAVEPOINT's

 */
namespace Nofuzz\Database\Drivers\Pdo;

class CockroachDb extends \Nofuzz\Database\PdoConnection
{
  protected $sslmode = '';  // 'disable', 'require'

  /**
   * Constructor
   *
   * @param string $connectionName [description]
   * @param array  $params         [description]
   */
  public function __construct(string $connectionName, array $params=[])
  {
    # CockroachDb has it's own $sslmode property, extract it
    $this->setSSLMode($params['sslmode'] ?? 'disable');

    # CockroachDb default PDO options
    if (count($params['options'] ?? [])==0) {
      $params['options'] = [
          \PDO::ATTR_PERSISTENT => TRUE,
          \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
          \PDO::ATTR_EMULATE_PREPARES => TRUE
        ];
    }

    # Parent Constructor (\Nofuzz\Database\PdoConnection)
    parent::__construct($connectionName,$params);
  }

  /**
   * Get DSN - CockraochDb formatting
   *
   * @return string       [description]
   */
  public function getDsn(): string
  {
    // pgsql:host=<hostname>;port=26257;dbname=bank;sslmode=disable

    # Build the DSN
    $_dsn = 'pgsql:' . // Cockroach uses PostgreSql as driver. # $this->getDriver().':' . 
            'host=' . $this->GetHost().';' . 
            'port=' . ($this->GetPort()!=0 ? $this->GetPort() . ':' : '26257' ) .
            'dbname=' . $this->GetSchema().';' .
            'sslmode=' . $this->getSSLMode();
            // 'charset='.$this->GetCharset();
    # Set it
    $this->setDsn($_dsn);
    # Results
    return $this->pdo_dsn;
  }

  /**
   * Get the sslmode
   *
   * @return string
   */
  public function getSSLMode(): string
  {
    return $this->sslmode;
  }

  /**
   * Set sslmode
   *
   * @param   string $sslmode
   * @return  self
   */
  public function setSSLMode(string $sslmode)
  {
    $this->sslmode = $sslmode;

    return $this;
  }

}
