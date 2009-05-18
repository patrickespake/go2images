<?php

// Requere o model ImageType
require_once('models/ImageType.php');

// Requere o model ImageSize
require_once('models/ImageSize.php');

// Requere o controlador Default
require_once('controllers/DefaultController.php');

/**
 * Controlador responsável pelos tipos de imagens
 */
class ImageTypeController extends DefaultController
{   
  /**
   * Listagem
   */
  public function listAction()
  {
    $results = ImageType::findAll();

    // Passa os valores para a view
    $this->results = $results;
  }

  /**
   * Apaga um tipo de imagem 
   */
  public function deleteAction()
  {
    $status = false;

    if (isset($_POST)) {
      if (isset($_REQUEST['id'])) {
        $id = (int) $_REQUEST['id'];
        
        // Apaga as imagens do disco
        $condition = sprintf("image_type_id = %s", $id);
        ImageSize::deleteAll($condition);

        $status = ImageType::delete($id);
      }
    }

    // Cria a variável flash
    if ($status)
      $this->setFlash('notice_success', 'Image type deleted successfully.');
    else
      $this->setFlash('notice_error', 'Delete image type failed.');

    // Redireciona para a listagem
    $this->redirectTo('imageType', 'list');
  }

  /**
   * Ação para obter os dados do tipo de imagem que deve ser atualizada
   */
  public function editAction()
  {
    // Busca o tipo de imagem
    $imageType = ImageType::find($_REQUEST['id']);

    // Caso não exista o tipo da imagem
    if ($imageType == null) {
      $this->notFound('imageType');
    }

    // Passa a variável para o template
    $this->imageType = $imageType;
  }

  /**
   * Ação para atualizar o tipo da imagem
   */
  public function updateAction()
  {  
    // Só atualiza se a requisição for POST
    if (isset($_POST)) {
      // Busca o tipo de imagem
      $imageType = ImageType::find($_REQUEST['imageType']['id']);

      // Caso não exista o tipo da imagem
      if ($imageType == null) {
        $this->notFound('imageType');
      }

      // Define os valores
      $imageType->setName($_REQUEST['imageType']['name']);
      $imageType->setWidth($_REQUEST['imageType']['width']);
      $imageType->setHeight($_REQUEST['imageType']['height']);

      // Verifica se tem erros de validação
      if ($imageType->isValid()) {
        // Salva
        $imageType->save();
        
        // Cria a variável flash
        $this->setFlash('notice_success', 'Image type updated successfully.');

        // Redireciona para a listagem
        $this->redirectTo('imageType', 'list');
      } else {
        // Obtém os erros de validação
        $this->errors = $imageType->getErrors();
      }

      // Passa as variáveis para a view
      $this->imageType = $imageType;
    } else {
      // Cria a variável flash
      $this->setFlash('notice_error', 'Update image type failed.');
      
      // Redireciona para a listagem
      $this->redirectTo('imageType', 'list');
    }
  }

  /**
   * Ação para criar um novo tipo de imagem
   */
  public function newAction()
  {
    // Cria um novo tipo de imagem
    $imageType = new ImageType();

    // Passa as variáveis para a view
    $this->imageType = $imageType;
  }

  /**
   * Ação para salvar um novo tipo de imagem
   */
  public function createAction()
  {  
    // Só cria se a requisição for POST
    if (isset($_POST)) { 
      // Novo tipo de imagem
      $imageType = new ImageType($_REQUEST['imageType']);

      // Verifica se tem erros de validação
      if ($imageType->isValid()) {
        // Salva
        $imageType->save();

        // Cria a variável flash
        $this->setFlash('notice_success', 'Image type created successfully.');

        // Redireciona para a listagem
        $this->redirectTo('imageType', 'list');
      } else {
        // Obtém os erros de validação
        $this->errors = $imageType->getErrors();
      }

      // Passa as variáveis para a view
      $this->imageType = $imageType;
    } else {
      // Cria a variável flash
      $this->setFlash('notice_error', 'Create image type failed.');

      // Redireciona para a listagem
      $this->redirectTo('imageType', 'list');
    }
  }
}
