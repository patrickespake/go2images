<?php

// Requere DatabaseConnection
require_once('db/DatabaseConnection.php');

// Requere Model
require_once('models/Model.php');

// Requre o model Image
require_once('models/Image.php');

/**
 * Model do tamanho de imagem
 */
class ImageSize implements Model
{
  // Atributos
  private $id;
  private $image;
  private $imageType;
  private $fileName;
  private $fileContentType;
  private $fileSize;
  private $errors;

  // Métodos gets e sets
  public function getId() { return $this->id; }
  public function getImage() { return $this->image; }
  public function getImageType() { return $this->imageType; }
  public function getFileName() { return $this->fileName; }
  public function getFileContentType() { return $this->fileContentType; }
  public function getFileSize() { return $this->fileSize; }

  private function setId($id) { $this->id = $id; }
  public function setImage($image) { $this->image = $image; }
  public function setImageType($imageType) { $this->imageType = $imageType; }
  public function setFileName($fileName) { $this->fileName = $fileName; }
  public function setFileContentType($fileContentType) { $this->fileContentType = $fileContentType; }
  public function setFileSize($fileSize) { $this->fileSize = $fileSize; }

  /**
   * Método construtor
   * @param array $info Atributos do tamanho da imagem
   */
  public function __construct($info = array())
  {
    if (count($info) > 0) {
      $this->setId($info['id']);

      $image = Image::find($info['image_id']);
      $this->setImage($image);

      $imageType = ImageType::find($info['image_type_id']);
      $this->setImageType($imageType);

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
    $values['image'] = $this->getImage();
    $values['image_type'] = $this->getImageType();
    $values['file_name'] = $this->getFileName();
    $values['file_content_type'] = $this->getFileContentType();
    $values['file_size'] = $this->getFileSize();

    if ($this->newRecord()) {
      $status = self::insert($values);
    } else {
      $status = self::update($values);
    }
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
   * @param array $items Resultado da busca pelo PDO
   * @return array de objetos
   */
  private function toObjectArray($items)
  {
    $results = array();
    foreach ($items as $item) {
      $results[] = new ImageSize($item);
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
    $sql = "SELECT id, image_id, image_type_id, file_name, file_content_type, file_size FROM image_sizes";

    if (!empty($conditions)) {
      $sql .= sprintf(" WHERE %s", $conditions);
    } else {
      $sql .= " ORDER BY image_id DESC, image_type_id DESC";
    }

    $pdo = DatabaseConnection::getConnection();
    $sth = $pdo->prepare($sql);
    $sth->execute();
    $results = $sth->fetchAll();

    return self::toObjectArray($results);
  }

  /**
   * Busca o menor tamanho de imagem
   * @param integer $imageId Id da imagem
   * @return ImageSize Informações do tamanho da imagem
   */
  public static function findMinimumSize($imageId)
  {
    $pdo = DatabaseConnection::getConnection();
    $sth = $pdo->prepare("SELECT 
      ims.* 
    FROM 
      image_sizes as ims
    INNER JOIN
      image_types as it
      ON (ims.image_type_id = it.id)
    WHERE
      ims.image_id = :image_id
    ORDER BY
      it.width ASC, it.height ASC
    LIMIT 1");
    $sth->bindParam(':image_id', $imageId, PDO::PARAM_INT);
    $sth->execute();
    $info = $sth->fetch();

    if ($info) {
      return new ImageSize($info);
    } else {
      return null;
    }
  }

  /**
   * Método implementado da interface
   * @see Model#find
   */
  public static function find($id)
  {
    $pdo = DatabaseConnection::getConnection();
    $sth = $pdo->prepare("SELECT id, image_id, image_type_id, file_name, file_content_type, file_size FROM image_sizes WHERE id = :id");
    $sth->bindParam(':id', $id, PDO::PARAM_INT);
    $sth->execute();

    $info = $sth->fetch();

    if ($info) {
      return new ImageSize($info);
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
    // Apaga as imagens em disco
    $imageSizes = self::findAll($conditions);

    foreach ($imageSizes as $imageSize) {
      if (file_exists($imageSize->getFileName())) {
        unlink($imageSize->getFileName());
      }
    }

    // Apaga do banco de dados
    $sql = sprintf("DELETE FROM image_sizes WHERE %s", $conditions);
    
    $pdo = DatabaseConnection::getConnection();
    $sth = $pdo->prepare($sql);
    $sth->execute();

    if ($sth->rowCount() > 0) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * Método implementado da interface
   * @see Model#delete
   * @return boolean
   */
  public static function delete($id)
  {
    $pdo = DatabaseConnection::getConnection();
    $sth = $pdo->prepare("DELETE FROM image_sizes WHERE id = :id");
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
    $sql = sprintf("UPDATE image_sizes SET image_id = %s, image_type_id = %s, file_name = '%s', file_content_type = '%s', file_size = '%s' WHERE id = %s", $values['image']->getId(), $values['image_type']->getId(), $values['file_name'], $values['file_content_type'], $values['file_size'], $values['id']);
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
    $sth = $pdo->prepare("INSERT INTO image_sizes (image_id, image_type_id, file_name, file_content_type, file_size) VALUES (:image_id, :image_type_id, :file_name, :file_content_type, :file_size)");
    $sth->bindParam(':image_id', $values['image']->getId(), PDO::PARAM_INT);
    $sth->bindParam(':image_type_id', $values['image_type']->getId(), PDO::PARAM_INT);
    $sth->bindParam(':file_name', $values['file_name'], PDO::PARAM_STR, 255);
    $sth->bindParam(':file_content_type', $values['file_content_type'], PDO::PARAM_STR, 255);
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
    return true;
  }
}
