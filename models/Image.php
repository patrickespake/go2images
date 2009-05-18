<?php

// Requere DatabaseConnection
require_once('db/DatabaseConnection.php');

// Requere Model
require_once('models/Model.php');

/**
 * Model da imagem
 */
class Image implements Model
{
  // Atributos
  private $id;
  private $title;
  private $description;
  private $fileName;
  private $fileContentType;
  private $fileSize;
  private $errors;

  // Métodos gets e sets
  public function getId() { return $this->id; }
  public function getTitle() { return $this->title; }
  public function getDescription() { return $this->description; }
  public function getFileName() { return $this->fileName; }
  public function getFileContentType() { return $this->fileContentType; }
  public function getFileSize() { return $this->fileSize; }

  private function setId($id) { $this->id = $id; }
  public function setTitle($title) { $this->title = $title; }
  public function setDescription($description) { $this->description = $description; }
  public function setFileName($fileName) { $this->fileName = $fileName; }
  public function setFileContentType($fileContentType) { $this->fileContentType = $fileContentType; }
  public function setFileSize($fileSize) { $this->fileSize = $fileSize; }

  /**
   * Método construtor
   * @param array $info atributos da imagem
   */
  public function __construct($info = array())
  {
    if (count($info) > 0) {
      $this->setId($info['id']);
      $this->setTitle($info['title']);
      $this->setDescription($info['description']);
      $this->setFileName($info['file_name']);
      $this->setFileContentType($info['file_content_type']);
      $this->setFileSize($info['file_size']);
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
    $values['title'] = $this->getTitle();
    $values['description'] = $this->getDescription();
    $values['file_name'] = $this->getFileName();
    $values['file_content_type'] = $this->getFileContentType();
    $values['file_size'] = $this->getFileSize();

    if ($this->newRecord()) {
      $status = self::insert($values);

      $pdo = DatabaseConnection::getConnection();
      $this->setId($pdo->lastInsertId());
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
      $results[] = new Image($item);
    }

    return $results;
  }

  /**
   * Método implementado da interface
   * @see Model#findAll
   * @return array
   */
  public static function findAll($conditions = null)
  {
    $pdo = DatabaseConnection::getConnection();
    $sth = $pdo->prepare("SELECT id, title, description, file_name, file_content_type, file_size FROM images");
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
    $sth = $pdo->prepare("SELECT id, title, description, file_name, file_content_type, file_size FROM images WHERE id = :id");
    $sth->bindParam(':id', $id, PDO::PARAM_INT);
    $sth->execute();

    $info = $sth->fetch();

    if ($info) {
      return new Image($info);
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
    $sth = $pdo->prepare("DELETE FROM images WHERE id = :id");
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
   * @see Model#update
   * @return boolean true em caso de sucesso, false caso contrário
   */
  public static function update($values)
  {
    $pdo = DatabaseConnection::getConnection();
    $sql = sprintf("UPDATE images SET title = '%s', description = '%s', file_name = '%s', file_content_type = '%s', file_size = %s WHERE id = %s", $values['title'], $values['description'], $values['file_name'], $values['file_content_type'], $values['file_size'], $values['id']);
    $count = $pdo->exec($sql);

    if ($count > 0) {
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
   * @see Model#insert
   * @return boolean
   */
  public static function insert($values)
  {
    $pdo = DatabaseConnection::getConnection();
    $sth = $pdo->prepare("INSERT INTO images (title, description, file_name, file_content_type, file_size) VALUES (:title, :description, :file_name, :file_content_type, :file_size)");
    $sth->bindParam(':title', $values['title'], PDO::PARAM_STR, 150);
    $sth->bindParam(':description', $values['description'], PDO::PARAM_STR);
    $sth->bindParam(':file_name', $values['file_name'], PDO::PARAM_STR);
    $sth->bindParam(':file_content_type', $values['file_content_type'], PDO::PARAM_STR);
    $sth->bindParam(':file_size', $values['file_size'], PDO::PARAM_INT);

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
   * Verifica se é possível salvar a imagem levando em consideração a imagem de upload
   * @param file $tmpName Arquivo para ser feito upload
   * @return boolean True caso seja válido, false caso contrário
   */
  public function isValidWithTmpName($tmpName)
  {
    $validate = $this->validates();
    
    if ($validate) {
      return $this->validateMinimumSizes($tmpName);
    }

    return $validate;
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

    // Dados para validar
    $title = $this->getTitle();
    $fileName = $this->getFileName();
    $fileContentType = $this->getFileContentType();
    $fileSize = $this->getFileSize();

    // Tipos de content types aceitos
    $validContentType = array('image/jpeg', 'image/jpg', 'image/png', 'image/gif');

    if (empty($title)) {
      $this->errors['title'] = 'the value is empty';
    }

    if (strlen($title) > 150) {
      $this->errors['title'] = 'the number of characters is greater than 150';
    }

    if (empty($fileName) || $fileSize == 0) {
      $this->errors['file'] = 'the value is empty';
    } else if (!in_array($fileContentType, $validContentType)) {
      $this->errors['file'] = 'is only accepted in the format jpg, gif or png';
    }

    if (count($this->errors) > 0) {
      return false;
    } else {
      return true;
    }
  }

  /**
   * Valida o tamanho mínimo da imagem
   * @param file $tmpName Imagem a ser cadastrada
   * @return boolean True caso seja válido, false caso contrário
   */
  private function validateMinimumSizes($tmpName)
  {
    // Tamanho da imagem a ser feito upload
    $sizes = getimagesize($tmpName);
    $width = $sizes['0'];
    $height = $sizes['1'];

    // Maior tipo de tamanho
    $imageType = ImageType::findMaximumSize();

    // Define a mensagem
    if (($width < $imageType->getWidth()) || ($height < $imageType->getHeight())) {
      $this->errors['file'] = sprintf("Must be greater than %sx%s", $imageType->getWidth(), $imageType->getHeight());
      return false;
    }

    return true;
  }

  /**
   * Obtém o menor tamanho de imagem
   * @return ImageSize Informações da menor imagem
   */
  public function getMinimumSize()
  {
    return ImageSize::findMinimumSize($this->getId());
  }

  /**
   * Obtém todos os tamanhos de imagens disponíveis
   * @return array/object Informações dos tamanhos de imagens
   */
  public function getSizes()
  {
    $condition = sprintf("image_id = %s ORDER BY image_type_id ASC", $this->getId());
    return ImageSize::findAll($condition);
  }
}
