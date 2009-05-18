<?php

// Requere DefaultController
require_once('controllers/DefaultController.php');

/**
 * Classe responsável por gerenciar as requisições
 */
class FrontController extends DefaultController
{
  private $action;
  private $controller;
  
  /**
   * Método construtor
   */
  public function __construct()
  {
    // Inicia a sessão
    session_start();
    
    // Dados da requisição
    $request = $_REQUEST;
    
    // Controlador e view padrão
    $this->action = 'list';
    $this->controller = 'image';

    if (isset($request['controller']) && isset($request['action'])) {
      $controller = $request['controller'];
      $action = $request['action'];

      // Verifica se existe a view, senão define a view de erro 404
      if (file_exists($this->getViewPath($controller, $action))) {
        $this->controller = $controller;
        $this->action = $action;
      } else {
        $this->controller = 'default';
        $this->action = 'error404';
      }
    }

    $this->runControllerMethod();
  }

  /**
   * Renderiza a view
   */
  public function render()
  {
    // Transforma os dados da requisição em variáveis
    foreach ($_REQUEST as $key => $value) {
      $$key = $value;
    }

    include($this->getView());
  }

  /**
   * Executa o código do método presente no controlador
   */
  protected function runControllerMethod()
  {
    $class = sprintf("%sController", ucfirst($this->getController()));
    $method = sprintf("%sAction()", $this->getAction());

    require_once(sprintf("%s.php", $class));
    $instance = new $class;
    $invokeMethod = '$'."instance->$method;";
    eval($invokeMethod);
  }

  /**
   * View a ser apresentada
   * @return string nome da view
   */
  public function getView()
  {
    return $this->getViewPath($this->getController(), $this->getAction());
  }

  /**
   * Nome da action
   * @return string action
   */
  public function getAction()
  {
    return $this->action;
  }

  /**
   * Nome do controlador
   * @return string controlador
   */
  public function getController()
  {
    return $this->controller;
  }

  /**
   * Path da view
   * @param string $controller nome do controlador
   * @param string $action nome da action
   * @return string path
   */
  private function getViewPath($controller, $action)
  {
    return sprintf("views/%s/%s.php", $controller, $action);
  }
}
