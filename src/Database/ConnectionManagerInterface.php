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

namespace Nofuzz\Database;

interface ConnectionManagerInterface
{
  /**
   * Find a connection based on name
   *
   * @param  string   $connectionName Name of the connection (from Config)
   * @return null | object
   */
  function findConnection(string $connectionName);

  /**
   * Get or Create a connection
   *
   * @param  string $connectionName Name of the connection (from Config)
   * @return null | object
   */
  function getConnection(string $connectionName);

  /**
   * Adds the Connection to the Pool
   *
   * @param [type] $connection [description]
   * @return  connection
   */
  function addConnection(\Nofuzz\Database\PdoConnectionInterface $connection);

  /**
   * Remove a connection from the pool
   *
   * @param  [type] $connection [description]
   * @return bool
   */
  function removeConnection(\Nofuzz\Database\PdoConnectionInterface $connection);

  /**
   * Get array of containers
   *
   * @return array
   */
  function getConnections(): array;
}
