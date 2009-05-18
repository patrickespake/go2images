<?php

// Requere o model ImageType
require_once('models/ImageType.php');

// Requere o model ImageSize
require_once('models/ImageSize.php');

/**
 * Classe responsável por gerenciar as imagens
 */
class ImageManager
{
  // Atributos
  private $name;
  private $type;
  private $tmpName;
  private $error;
  private $size;
  private $dirImagesPath = 'public/images/system';
  private $dirImagePath;
  private $originalImagePath;
  private $image;

  // Métodos gets
  public function getName() { return $this->name; }
  public function getType() { return $this->type; }
  public function getTmpName() { return $this->tmpName; }
  public function getError() { return $this->error; }
  public function getSize() { return $this->size; }
  public function getDirImagesPath() { return $this->dirImagesPath; }
  public function getImage() { return $this->image; }

  // Métodos sets
  public function setName($name) { $this->name = $name; }
  public function setType($type) { $this->type = $type; }
  public function setTmpName($tmpName) { $this->tmpName = $tmpName; }
  public function setError($error) { $this->error = $error; }
  public function setSize($size) { $this->size = $size; }

  /**
   * Método construtor
   * @param Image $image Instância do model de imagem
   */
  public function __construct($image)
  {
    $this->image = $image;

    // Cria o diretório da imagem, caso seja necessário
    $this->makeDir();
  }

  /**
   * Realiza o upload da imagem no servidor
   */
  public function upload()
  {
    // Grava a imagem original no servidor
    move_uploaded_file($this->getTmpName(), $this->getOriginalImagePath());

    // Atualiza o path da imagem
    $this->getImage()->setFileName($this->getOriginalImagePath());
    $this->getImage()->save();

    // Apaga todos os tamanhos de imagens
    $condition = sprintf("image_id = %s", $this->getImage()->getId());
    ImageSize::deleteAll($condition);  

    // Resize e crop dos tipos de imagens
    $this->resizeAndCropImageTypes();
  }

  /**
   * Faz o resize e crop dos tipos de imagens em cima da imagem original
   */
  public function resizeAndCropImageTypes()
  { 
    // Busca todos os tipos de imagens
    $imageTypes = ImageType::findAll();

    foreach ($imageTypes as $imageType) {
      $this->resize($imageType);
      $this->crop($imageType);
    }
  }

  /**
   * Realiza o crop da imagem para determinadas medidas
   * @param ImageType $imageType Informações do tipo de imagem
   */
  private function crop($imageType)
  {
    // Caminho da imagem a ser realizado crop
    $croppedImagePath = sprintf("%s/%s_%s", $this->getDirImagePath(), $imageType->getName(), $this->getName());

    // Dimensões da imagem que foi realizada resize
    $resizedImageSizes = $this->getResizedImageSizes();

    // Pontos x e y para crop
    $startHeight = ($resizedImageSizes['height'] - $imageType->getHeight()) / 2;
    $startWidth = ($resizedImageSizes['width'] - $imageType->getWidth()) / 2;

    // Cria a nova imagem
  	$newImage = imagecreatetruecolor($imageType->getWidth(), $imageType->getHeight());
    $source = $this->getSource($this->getResizedImagePath());
    imagecopy($newImage, $source, 0, 0, $startWidth, $startHeight, $resizedImageSizes['width'], $resizedImageSizes['height']);
    $this->createImage($newImage, $croppedImagePath, $this->getResizedImagePath());
    chmod($croppedImagePath, 0777);

    // Apaga a imagem de resize
    unlink($this->getResizedImagePath());

    // Grava no banco de dados as informações do novo tamanho de imagem
    $imageSize = new ImageSize();
    $imageSize->setImage($this->getImage());
    $imageSize->setImageType($imageType);
    $imageSize->setFileName($croppedImagePath);
    $imageSize->setFileContentType($this->getMimeType($croppedImagePath));
    $imageSize->setFileSize(filesize($croppedImagePath));
    $imageSize->save();
  }

  /**
   * Faz o resize da imagem de acordo com a escala
   * @param ImageType $imageType Informações do tipo de imagem
   */
  private function resize($imageType)
  { 
    // Obtém a escala para fazer o resize
    $scale = $this->getScaleToResize($imageType->getHeight(), $imageType->getWidth());
    
    // Dimensões da imagem original
    $originalImageSizes = $this->getOriginalImageSizes();
    $originalImageHeight = (float) $originalImageSizes['height'];
    $originalImageWidth = (float) $originalImageSizes['width'];
    
    // Cria uma nova imagem de acordo com a escala
    $newImageWidth = ceil($originalImageWidth / $scale);
    $newImageHeight = ceil($originalImageHeight / $scale); 

    // Cria a imagem resized
    $newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
    $source = $this->getSource($this->getOriginalImagePath());
	  imagecopyresampled($newImage, $source, 0, 0, 0, 0, $newImageWidth, $newImageHeight, $originalImageWidth, $originalImageHeight);
    $this->createImage($newImage, $this->getResizedImagePath(), $this->getOriginalImagePath());
    chmod($this->getResizedImagePath(), 0777); 
  }

  /**
   * Cria a imagem em disco de acordo com o mime type
   * @param image $newImage Nova imagem
   * @param string $newImagePath Caminho da nova imagem
   * @param string $originalImagePath Caminho da imagem original
   */
  private function createImage($newImage, $newImagePath, $originalImagePath)
  {
    switch ($this->getMimeType($originalImagePath))
    {
      case 'image/jpeg':
        imagejpeg($newImage, $newImagePath, 90); 
        break;
      case 'image/jpg':
        imagejpeg($newImage, $newImagePath, 90);
        break;
      case 'image/png':
        imagepng($newImage, $newImagePath);
        break;
      case 'image/gif':
        imagegif($newImage, $newImagePath);
        break;
    }
  }

  /**
   * Obtém o código da imagem de acordo com mime type
   * @param string $imagePath Caminho da imagem
   * @return source
   */
  private function getSource($imagePath)
  {
    switch ($this->getMimeType($imagePath))
    {
      case 'image/jpeg':
        return imagecreatefromjpeg($imagePath);
        break;
      case 'image/jpg':
        return imagecreatefromjpeg($imagePath);
        break;
      case 'image/png':
        return imagecreatefrompng($imagePath);
        break;
      case 'image/gif':
        return imagecreatefromgif($imagePath);
        break;
    }
  }

  /**
   * Obtém o mime type da imagem
   * @param string $imagePath Caminho da imagem
   * @return string Tipo do mime type
   */
  public function getMimeType($imagePath)
  {
    return mime_content_type($imagePath);
  }

  /**
   * Caminho da imagem que foi feito resize
   * @param string Caminho da imagem
   */
  public function getResizedImagePath()
  {
    return sprintf("%s/resized.jpg", $this->getDirImagePath());
  }

  /**
   * Obtém a escala que deve ser usada para fazer o resize
   * @param float $imageHeight Altura da imagem
   * @param float $imageWidth Largura da imagem
   * @return float Escala para fazer o resize
   */
  private function getScaleToResize($imageHeight, $imageWidth)
  { 
    // Dimensões da imagem original
    $originalImageSizes = $this->getOriginalImageSizes();
    $originalImageHeight = (float) $originalImageSizes['height'];
    $originalImageWidth = (float) $originalImageSizes['width'];

    // Dimensões do tipo de imagem
    $imageHeight = (float) $imageHeight;
    $imageWidth = (float) $imageWidth;

    // Descobre a escala 
    $scaleWidth = $originalImageWidth / $imageWidth;
    $scaleHeight = $originalImageHeight / $imageHeight;

    if ($scaleWidth < $scaleHeight)
      $scale = $scaleWidth;
    else
      $scale = $scaleHeight;
    
    return $scale;
  }

  /**
   * Obtém a altura e largura da imagem que foi feita resize
   * @return array Com dois índices height e width
   */
  private function getResizedImageSizes()
  {
    $resizedImage = $this->getResizedImagePath();
    $info = getimagesize($resizedImage);
    $sizes['height'] = $info[1];
    $sizes['width'] = $info[0];

    return $sizes;
  }

  /**
   * Obtém a altura e largura da imagem original
   * @return array Com dois índices height e width
   */
  public function getOriginalImageSizes()
  {
    $originalImage = $this->getOriginalImagePath();
    $info = getimagesize($originalImage);
    $sizes['height'] = $info[1];
    $sizes['width'] = $info[0];

    return $sizes;
  }

  /**
   * Caminho da imagem original
   * @param string Caminho da imagem original
   */
  public function getOriginalImagePath()
  {
    return sprintf("%s/original_%s", $this->getDirImagePath(), $this->getName());
  }

  /**
   * Caminho do diretório da imagem
   * @return string Caminho
   */
  public function getDirImagePath()
  {
    return sprintf("%s/%s", $this->getDirImagesPath(), $this->getImage()->getId());
  }

  /**
   * Cria o diretório da imagem, caso seja necessário
   */
  private function makeDir()
  {
    // Diretório não existe, então cria
    if (!file_exists($this->getDirImagePath())) {
      mkdir($this->getDirImagePath(), 0777);
      chmod($this->getDirImagePath(), 0777);
    }
  }

  /**
   * Apaga o diretório da imagem e o seu conteúdo
   */
  public function removeDir()
  {
    // Só apaga se o diretório existir
    if (is_dir($this->getDirImagePath())) {
      // Apaga os arquivos do diretório
      $this->removeDirFiles();

      // Apaga o diretório
      rmdir($this->getDirImagePath());
    }
  }

  /**
   * Apaga os arquivos do diretório da imagem
   */
  public function removeDirFiles()
  {
    // Verifica se é um diretório
    if (is_dir($this->getDirImagePath())) {
      // Arquivos presentes no diretório
      $files = scandir($this->getDirImagePath());

      // Apaga arquivo por arquivo
      foreach ($files as $file) {
        // Não apaga se for . ou ..
        if ($file == "." || $file == "..") {
          continue;
        }

        // Caminho do arquivo
        $filePath = sprintf("%s/%s", $this->getDirImagePath(), $file);

        // Apaga o arquivo
        if (is_file($filePath)) {
          unlink($filePath);
        }
      }
    }
  }
}
