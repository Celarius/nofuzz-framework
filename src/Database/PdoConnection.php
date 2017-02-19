<?php
/**
 * Generic PDO Database Connection
 *
 * @package     Nofuzz
*/

################################################################################################################################

namespace Nofuzz\Database;
/*
MYSQL:
  // host=localhost
  // port=3306
  // dbname=<path>\\<filename.fdb>
  $connection = new \PDO('mysql:host=localhost;port=3306;dbname=test', $user, $pass,
    array(
      \PDO::ATTR_PERSISTENT => true
      \PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      // \PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
      \PDO::ATTR_AUTOCOMMIT => FALSE,
      \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"           // Set UTF8 as charset
    )
  );


FIREBIRD:
  // host=localhost
  // port=3050
  // dbname=<path>\\<filename.fdb>
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


POSTGRESQL:
  // host=localhost
  // port=5432
  // dbname=C:\db\banco.gdb
  $connection = new PDO("pgsql:host=192.168.137.1;port=5432;dbname=anydb", $user, $pass,
    array(
      PDO::ATTR_PERSISTENT => true
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_AUTOCOMMIT => false
    )
  );


*/
################################################################################################################################

abstract class PdoConnection extends \PDO implements \Nofuzz\Database\PdoConnectionInterface
{
  protected $name = ''; // Connection name
  protected $type = ''; // Connection Type
  protected $driver = ''; // Connection Driver ('MySql','Firebird','Sqlite'...)
  protected $schema = ''; // Connection Schema
  protected $host = '';
  protected $port = 0;
  protected $username = '';
  protected $password = '';
  protected $charset = '';
  protected $pdo_options = array(); // PDO options array

  protected $pdo_dsn = '';
  protected $version = ''; // Database Version we connect to

  /** @var boolean True=Connected, False=Not connected */
  protected $connected = false;


  /**
   * Constructor
   *
   * @param string $dbDriver    Driver name. Ex. 'firebird', 'mysql', 'pgsql', 'odbc', 'sqlite'
   * @param string $dbHost      Hostname/IP address
   * @param int    $dbPort      Port number. 0=Auto
   * @param string $dbSchema    Database file name / schema
   * @param string $dbUser      Username
   * @param string $dbPass      Password
   * @param array  $dbCharset   Charset. Optional. Defaults to UTF-8
   * @param array  $dbOpts      PDO Options. Optional
   */
  public function __construct(string $dbDriver, string $dbHost, int $dbPort, string $dbSchema, string $dbUser='', string $dbPass='', string $dbCharset='UTF8',array $dbOpts=array())
  {
    # Default PDO options for all drivers if none given
    if (count($dbOpts)==0) {
      $dbOpts =
        array(
          \PDO::ATTR_PERSISTENT => TRUE,
          \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
          \PDO::ATTR_AUTOCOMMIT => FALSE
        );
    }

    # Set properties
    $this->driver = $dbDriver;
    $this->host = $dbHost;
    $this->port = $dbPort;
    $this->setSchema($dbSchema);
    $this->setUsername($dbUser);
    $this->setPassword($dbPass);
    $this->setCharset($dbCharset);
    $this->setOptions($dbOpts);

    # Parent Constructor
    parent::__construct($this->getDsn(),$this->getUsername(),$this->getPassword(),$this->getOptions());

    # Retreive the DB Engine Version (if supported)
    $this->version = $this->getAttribute(\PDO::ATTR_SERVER_VERSION);

    # Set connected
    $this->connected = true;
  }

  /**
   * Destrctor
   */
  public function __destruct()
  {
    # Disconnect
    $this->disconnect();
  }

  /**
   * Connect to Database
   *
   * This does not work in PDO as the connection is always open as long as the object (this) exists.
   *
   * @return [type] [description]
   */
  public function connect(): bool
  {
    return $this->connected();
  }

  /**
   * Disconnect from database
   *
   * This does not work in PDO since there is no disconnect() feature in PDO
   *
   * @return [type] [description]
   */
  public function disconnect(): bool
  {
    # Rollback any open transactions
    if ($this->inTransaction()) $this->rollback();

    return true;
  }

  /**
   * Checks if Connected to a Database
   *
   * @return bool         True for connected, false if not
   */
  public function connected(): bool
  {
    return $this->connected;
  }

  /**
   * Get DSN - Return the default formatted DSN
   *
   * This method needs to be overridden in DB Driver specific files
   *
   * @return string       [description]
   */
  public function getDsn(): string
  {
    # Build the DSN
    $_dsn = $this->driver.':host='.$this->host.';port='.$this->port.';dbname='.$this->schema.';charset='.$this->charset;
    # Set it
    $this->setDsn($_dsn);

    return $this->pdo_dsn;
  }

  /**
   * Get the Name
   *
   * @return string
   */
  public function getName(): string
  {
    return $this->name;
  }

  /**
   * Get the Driver
   *
   * @return string
   */
  public function getDriver(): string
  {
    return $this->driver;
  }

  /**
   * Get the Type
   *
   * @return string
   */
  public function getType(): string
  {
    return $this->type;
  }

  /**
   * Get the Schema
   *
   * @return string
   */
  public function getSchema(): string
  {
    return $this->schema;
  }

  /**
   * Get the Host
   *
   * @return string
   */
  public function getHost(): string
  {
    return $this->host;
  }

  /**
   * Get the Port
   *
   * @return string
   */
  public function getPort(): int
  {
    return $this->port;
  }


  /**
   * Get Username
   *
   * @return string
   */
  public function getUsername(): string
  {
    return $this->username;
  }

  /**
   * Get Password
   *
   * @return string
   */
  public function getPassword(): string
  {
    return $this->password;
  }

  /**
   * Get Charset
   *
   * @return string
   */
  public function getCharset(): string
  {
    return $this->charset;
  }

  /**
   * Get Options
   *
   * @return array
   */
  public function getOptions(): array
  {
    return $this->pdo_options;
  }

  /**
   * Get Version
   *
   * @return array
   */
  public function getVersion(): string
  {
    return $this->version;
  }

  /**
   * Set Type
   *
   * @param   string $type
   * @return  delf
   */
  public function setType(string $type)
  {
    $this->type = $type;

    return $this;
  }

  /**
   * Set Connection Name
   *
   * @param   string $name
   * @return  delf
   */
  public function setName(string $name)
  {
    $this->name = $name;

    return $this;
  }

  /**
   * Set Schema
   *
   * @param   string $schema
   * @return  delf
   */
  public function setSchema(string $schema)
  {
    $this->schema = $schema;

    return $this;
  }

  /**
   * Set DSN connection string
   *
   * @param [type] $dsn           [description]
   */
  public function setDsn(string $dsn)
  {
    $this->pdo_dsn = $dsn;

    return $this;
  }

  /**
   * Set Username
   *
   * @param string $username      [description]
   */
  public function setUsername(string $username)
  {
    $this->username = $username;

    return $this;
  }

  /**
   * Set Password
   *
   * @param string $password      [description]
   */
  public function setPassword(string $password)
  {
    $this->password = $password;

    return $this;
  }

  /**
   * Set Charset
   *
   * @param string $charset       Charset to use
   */
  public function setCharset(string $charset)
  {
    $this->charset = $charset;

    return $this;
  }

  /**
   * Set Connection Options
   *
   * @param array $options        [description]
   */
  public function setOptions(array $options)
  {
    $this->pdo_options = $options;

    return $this;
  }
}
