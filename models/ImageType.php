<?php

// Requere DatabaseConnection
require_once('db/DatabaseConnection.php');

// Requere Model
require_once('models/Model.php');

/**
 * Model do tipo de imagem
 */
class ImageType implements Model
{ 
  // Atributos
  private $id;
  private $name;
  private $height;
  private $width;
  private $errors;

  // Métodos gets e sets
  public function getId() { return $this->id; }
  public function getName() { return $this->name; }
  public function getHeight() { return $this->height; }
  public function getWidth() { return $this->width; }

  private function setId($id) { $this->id = $id; }
  public function setName($name) { $this->name = $name; }
  public function setHeight($height) { 
    $height = (!empty($height) && is_numeric($height)) ? (float) $height : $height;
    $this->height = $height; 
  }
  public function setWidth($width) { 
    $width = (!empty($width) && is_numeric($width)) ? (float) $width : $width;
    $this->width = $width; 
  }

  /**
   * Método construtor
   * @param array $info atributos do tipo de imagem
   */
  public function __construct($info = array())
  {
    if (count($info) > 0) {
      $this->setId($info['id']);
      $this->setName($info['name']);
      $this->setHeight($info['height']);
      $this->setWidth($info['width']);
    }
  }

  /**
   * Método implementado da interface
   * @see Model#save
   * @return boolean
   */
  public function save()
  {
    $status = false;
    
    $values['id'] = $this->getId();
    $values['name'] = $this->getName();
    $values['width'] = $this->getWidth();
    $values['height'] = $this->getHeight();
    
    if ($this->newRecord()) {
      $status = self::insert($values);
    } else {
      $status = self::update($values);
    }

    return $status;
  }

  /**
   * Método implementado da interface
   * @see Model#newRecord
   */
  public function newRecord()
  {
    return empty($this->id) ? true : false;
  }

  /**
   * Transforma os itens em um array de objetos
   * @param array $items resultado da busca pelo PDO
   * @return array de objetos
   */
  private function toObjectArray($items)
  {
    $results = array();
    foreach ($items as $item) {
      $results[] = new ImageType($item);
    }

    return $results;
  }

  /**
   * Procura o maior tipo de conteúdo
   * @return ImageType Informações do tipo de imagem
   */
  public static function findMaximumSize()
  {
    $pdo = DatabaseConnection::getConnection();
    $sth = $pdo->prepare("SELECT id, name, height, width FROM image_types ORDER BY width DESC, height DESC LIMIT 1");
    $sth->execute();

    $info = $sth->fetch();

    if ($info) {
      return new ImageType($info);
    } else {
      return null;
    }
  }

  /**
   * Método implementado da interface
   * @see Model#findAll
   * @return array
   */
  public static function findAll($conditions = null)
  {
    $pdo = DatabaseConnection::getConnection();
    $sth = $pdo->prepare("SELECT id, name, height, width FROM image_types ORDER BY width DESC");
    $sth->execute();
    $results = $sth->fetchAll();

    return self::toObjectArray($results);
  }

  /**
   * Método implementado da interface
   * @see Model#find
   */
  public static function find($id)
  {
    $pdo = DatabaseConnection::getConnection();
    $sth = $pdo->prepare("SELECT id, name, height, width FROM image_types WHERE id = :id");
    $sth->bindParam(':id', $id, PDO::PARAM_INT);
    $sth->execute();

    $info = $sth->fetch();

    if ($info) {
      return new ImageType($info);
    } else {
      return null;
    }
  }

  /**
   * Método implementado da interface
   * @see Model#deleteAll
   */
  public static function deleteAll($conditions)
  {
  }

  /**
   * Método implementado da interface
   * @see Model#delete
   * @return boolean
   */
  public static function delete($id)
  {
    $pdo = DatabaseConnection::getConnection();
    $sth = $pdo->prepare("DELETE FROM image_types WHERE id = :id");
    $sth->bindParam(':id', $id, PDO::PARAM_INT);
    $sth->execute();

    if ($sth->rowCount() > 0) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * Método implementado da interface
   * @see Model#updateAll
   */
  public static function updateAll($conditions, $values)
  {
  }

  /**
   * Método implementado da interface
   * @see Model#update
   * @return boolean true em caso de sucesso, false caso contrário
   */
  public static function update($values)
  {    
    $pdo = DatabaseConnection::getConnection();
    $sql = sprintf("UPDATE image_types SET name = '%s', width = %s, height = %s WHERE id = %s", $values['name'], $values['width'], $values['height'], $values['id']);
    $count = $pdo->exec($sql);

    if ($count > 0) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * Método implementado da interface
   * @see Model#insert
   * @return boolean
   */
  public static function insert($values)
  {
    $pdo = DatabaseConnection::getConnection();
    $sth = $pdo->prepare("INSERT INTO image_types (name, width, height) VALUES (:name, :width, :height)");
    $sth->bindParam(':name', $values['name'], PDO::PARAM_STR, 150);
    $sth->bindParam(':width', $values['width']);
    $sth->bindParam(':height', $values['height']);
    return $sth->execute();
  }

  /**
   * Método implementado da interface
   * @see Model#isValid
   */
  public function isValid()
  {
    return $this->validates();
  }

  /**
   * Método implementado da interface
   * @see Model#getErrors
   */
  public function getErrors()
  {
    return $this->errors;
  }

  /**
   * Realiza todas as validações necessárias
   */
  private function validates()
  {    
    $this->errors = array();
    $status = true;
    
    $name = $this->getName();
    $height = $this->getHeight();
    $width = $this->getWidth();

    if (empty($name)) {
      $this->errors['name'] = 'the value is empty';
      $status = false;
    }
    
    if (strlen($name) > 150) {
      $this->errors['name'] = 'the number of characters is greater than 150';
      $status = false;
    }

    if (!ereg("^[a-z]+$", $name)) {
      $this->errors['name'] = 'only accepted names are compounds of letters a-z without space and special characters, example: large, small, big...';
      $status = false;
    }

    if (!is_float($height)) {
      $this->errors['height'] = 'the value is not float';
      $status = false;
    }

    if ($height == 0) {
      $this->errors['height'] = 'the value must be greater than 0';
      $status = false;
    }

    if (empty($height)) {
      $this->errors['height'] = 'the value is empty';
      $status = false;
    }

    if (!is_float($width)) {
      $this->errors['width'] = 'the value is not float';
      $status = false;
    }

    if ($width == 0) {
      $this->errors['width'] = 'the value must be greater than 0';
      $status = false;
    }

    if (empty($width)) {
      $this->errors['width'] = 'the value is empty';
      $status = false;
    }

    return $status;
  }
}
