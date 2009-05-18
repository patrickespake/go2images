<?php

// Requere o model Image
require_once('models/Image.php');

// Requere o controlador Default
require_once('controllers/DefaultController.php');

// Requere a lib ImageManager
require_once('lib/ImageManager.php');

/**
 * Controlador responsável pelas imagens 
 */
class ImageController extends DefaultController
{
  /**
   * Exibição
   */
  public function showAction()
  {
    // Busca a imagem
    $image = Image::find($_REQUEST['id']);

    // Caso não exista o tipo da  imagem
    if ($image == null) {
      $this->notFound('image');
    }

    // Passa a variável para o template
    $this->image = $image;
  }
  
  /**
   * Listagem
   */
  public function listAction()
  {
    $results = Image::findAll();

    // Passa os valores para a view
    $this->results = $results;
  }

  /**
   * Apaga
   */
  public function deleteAction()
  {
    $status = false;

    if (isset($_POST)) {
      if (isset($_REQUEST['id'])) {
        $id = (int) $_REQUEST['id'];

        $image = Image::find($id);
        if ($image) {
          $status = Image::delete($image->getId());

          // Se a imagem foi apagada com sucesso realiza as demais ações
          if ($status) {
            // Apaga os tamanhos de imagem
            $condition = sprintf("image_id = %s", $image->getId());
            ImageSize::deleteAll($condition);

            // Gerenciador de imagens
            $imageManager = new ImageManager($image);

            // Apaga o diretório das imagens
            $imageManager->removeDir();
          }
        }
      }
    }

    // Cria a variável flash
    if ($status)
      $this->setFlash('notice_success', 'Image deleted successfully.');
    else
      $this->setFlash('notice_error', 'Delete image failed.');

    // Redireciona para a listagem
    $this->redirectTo('image', 'list');
  }

  /**
   * Edita
   */
  public function editAction()
  {
    // Busca a imagem
    $image = Image::find($_REQUEST['id']);

    // Caso não exista o tipo da  imagem
    if ($image == null) {
      $this->notFound('image');
    }

    // Passa a variável para o template
    $this->image = $image;
  }

  /**
   * Atualiza
   */
  public function updateAction()
  {
    // Só atualiza se a requisição for POST
    if (isset($_POST)) {
      // Status
      $status = false;

      // Busca a imagem
      $image = Image::find($_REQUEST['image']['id']);

      // Caso não exista a imagem
      if ($image == null) {
        $this->notFound('image');
      }

      // Define os valores
      $image->setTitle($_REQUEST['image']['title']);
      $image->setDescription($_REQUEST['image']['description']);

      // Verifica se tem algum arquivo para fazer upload 
      $size = $_FILES['image']['size']['file'];
      if ($size > 0) {
        $name = $_FILES['image']['name']['file'];
        $type = $_FILES['image']['type']['file'];
        $tmp_name = $_FILES['image']['tmp_name']['file'];
        $error = $_FILES['image']['error']['file'];

        $_REQUEST['image']['file_name'] = $name;
        $_REQUEST['image']['file_content_type'] = $type;
        $_REQUEST['image']['file_size'] = $size;

        $image->setFileName($_REQUEST['image']['file_name']);
        $image->setFileContentType($_REQUEST['image']['file_content_type']);
        $image->setFileSize($_REQUEST['image']['file_size']);

        // Verifica se tem erros de validação
        if ($image->isValidWithTmpName($tmp_name)) {
          // Salva
          $image->save();

          // Gerenciador de imagens
          $imageManager = new ImageManager($image);

          // Apaga os arquivos antigos
          $imageManager->removeDirFiles();

          // Faz o upload da imagem
          $imageManager->setName($name);
          $imageManager->setType($type);
          $imageManager->setTmpName($tmp_name);
          $imageManager->setError($error);
          $imageManager->setSize($size);
          $imageManager->upload();

          $status = true;
        }
      } else {
        // Verifica se tem erros de validação
        if ($image->isValid()) {
          // Salva
          $image->save();

          $status = true;
        }
      }

      // Verifica se foi salvo com sucesso
      if ($status) {
        // Cria a variável flash
        $this->setFlash('notice_success', 'Image updated successfully.');

        // Redireciona para a listagem
        $this->redirectTo('image', 'list');
      } else {
        // Passa as variáveis para a view
        $this->image = $image;

        // Obtém os erros de validação
        $this->errors = $image->getErrors();
      }
    } else {
      // Cria a variável flash
      $this->setFlash('notice_error', 'Update image failed.');

      // Redireciona para a listagem
      $this->redirectTo('image', 'list');
    }
  }

  /**
   * Novo
   */
  public function newAction()
  {
    // Cria um novo tipo de imagem
    $image = new Image();

    // Passa as variáveis para a view
    $this->image = $image;
  }

  /**
   * Cria
   */
  public function createAction()
  {
    // Só cria se a requisição for POST
    if (isset($_POST)) {
      // Adiciona as informações do arquivo na requisição
      $name = $_FILES['image']['name']['file'];
      $type = $_FILES['image']['type']['file'];
      $tmp_name = $_FILES['image']['tmp_name']['file'];
      $error = $_FILES['image']['error']['file'];
      $size = $_FILES['image']['size']['file'];

      $_REQUEST['image']['file_name'] = $name;
      $_REQUEST['image']['file_content_type'] = $type;
      $_REQUEST['image']['file_size'] = $size;

      // Nova imagem
      $image = new Image($_REQUEST['image']);

      // Verifica se tem erros de validação
      if ($image->isValidWithTmpName($tmp_name)) {
        // Salva
        $image->save();

        // Faz o upload da imagem
        $imageManager = new ImageManager($image);
        $imageManager->setName($name);
        $imageManager->setType($type);
        $imageManager->setTmpName($tmp_name);
        $imageManager->setError($error);
        $imageManager->setSize($size);
        $imageManager->upload();

        // Cria a variável flash
        $this->setFlash('notice_success', 'Image created successfully.');

        // Redireciona para fazer o crop da imagem
        $this->redirectTo('image', 'list');
      } else {
        // Obteḿ os erros de validação
        $this->errors = $image->getErrors();
      }

      // Passa as variáveis para a view
      $this->image = $image;
    } else {
      // Cria a variável flash
      $this->setFlash('notice_error', 'Create image failed.');

      // Redireciona para a listagem
      $this->redirecTo('image', 'list');
    }
  }
}
