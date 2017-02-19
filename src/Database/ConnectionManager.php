<?php
/**
 * ConnectionManager
 *
 * Manages all Database Connections
 *
 * Each Connection has a {Driver} and a {Schema}
 *
 * @package  [Application]
 */
#################################################################################################################################

/*
Example:

  # Getting the Connection
  $dbCon = db('name1'); // Gives you back a PDO Object connected to the Database

*/
namespace Nofuzz\Database;

class ConnectionManager implements \Nofuzz\Database\ConnectionManagerInterface
{
  protected $connections = array();

  /**
   * Constructor
   */
  public function __construct()
  {
  }

  /**
   * Destructor
   */
  public function __destruct()
  {
    // Close all Database connections
  }

  /**
   * Find a connection based on name
   *
   * @param  string   $connectionName Name of the connection (from Config)
   * @return null | object
   */
  public function findConnection(string $connectionName)
  {
    if ( empty($connectionName) ) {
      # Take first available connection
      $connection = reset($this->connections);
      if ($connection === false)
        return null;
    } else {
      # Attempt to find the connection from the pool
      $connection = ( $this->connections[strtolower($connectionName)] ?? null);
    }

    return $connection;
  }

  /**
   * Get or Create a connection
   *
   * @param  string $connectionName Name of the connection (from Config)
   * @return null | object
   */
  public function getConnection(string $connectionName)
  {
    $connection = $this->findConnection($connectionName);

    if (is_null($connection)) {
      $connection = $this->createConnection($connectionName);

      if (!is_null($connection)) {
        $this->addConnection($connection);
      }
    }

    return $connection;
  }

  /**
   * Adds the Connection to the Pool
   *
   * @param [type] $connection [description]
   * @return  connection
   */
  public function addConnection(\Nofuzz\Database\PdoConnectionInterface $connection)
  {
    $this->connections[strtolower($connection->getName())] = $connection;

    return $connection;
  }

  /**
   * Remove a connection from the pool
   *
   * @param  [type] $connection [description]
   * @return bool
   */
  public function removeConnection(\Nofuzz\Database\PdoConnectionInterface $connection)
  {
    $connection = $this->findConnection($connectionName);

    if ($connection) {
      $connection->disconnect();
      unset( $this->connections[strtolower($connection->getName())] );
      unset($connection);
      $connection = null;
    }

    return is_null($connection);
  }

  /**
   * Creates a Connection based on the $connectionName
   *
   * Finds the corresponding connection in the config and uses it
   * to instanciate a connection
   *
   * @param  string $connectionName [description]
   * @return [type]                 [description]
   */
  protected function createConnection(string $connectionName)
  {
    $connection = $this->connections[strtolower($connectionName)] ?? null;

    if (is_null($connection)) {

      # Get configuration
      $connConf = config()->get('connections.'.$connectionName);

      # Type="PDO"
      if ( strcasecmp($connConf['type'] ?? '','PDO')==0 ) {
        $className = '\\Nofuzz\\Database\\Drivers\\'.ucfirst($connConf['type']).'\\'.ucfirst($connConf['driver']) ;

        # Convert the PDO constants in the Opts to real values
        $opts = array();
        if ( count($connConf)>0 ) {


          foreach ($connConf['options'] as $pdoOptions)
          {
            $pdoValue = trim(reset($pdoOptions));
            $pdoOption = strtoupper(key($pdoOptions));

            # Convert data to PDO constants
            $k = constant('\PDO::'.$pdoOption); // PDO Option
            if ( !is_numeric($pdoValue) && !empty($pdoValue) ) {
              $v = @constant('\PDO::'.$pdoValue);  // PDO constant
            } else if (!empty($pdoValue)) {
              $v = $pdoValue; // Its a string
            } else {
              $v = 0; // false
            }
            # Set the Option
            $opts[ $k ] = $v;
          }

          # Create the Connection
          $connection = new $className($connConf['host'],$connConf['port'],$connConf['schema'],$connConf['username'],$connConf['password'],$connConf['charset'],$opts);
          if ($connection) {
            $connection->setName($connectionName);
            $connection->setType($connConf['type']);
          }
        }
      }
    }

    return $connection;
  }


  /**
   * Get array of containers
   *
   * @return array
   */
  public function getConnections(): array
  {
    return $this->connections;
  }

}
