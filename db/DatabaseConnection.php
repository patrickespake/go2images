<?php

/**
 * Realiza a conexão com o MySQL usando o PDO
 */
class DatabaseConnection
{
  // Atributos
  private static $host = 'localhost';
  private static $user = 'root';
  private static $password = '123';
  private static $db = 'go2images';

  /**
   * Retorna a conexão com o MySQL
   */
  public static function getConnection()
  {
    // Estabelece a conexão usando o PDO
    return new PDO(sprintf("mysql:host=%s;dbname=%s", self::$host, self::$db), self::$user, self::$password, array(PDO::ATTR_PERSISTENT => true));
  }
}
