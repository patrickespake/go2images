<?php

/**
 * Controlador que gerência as ações padrões
 */
class DefaultController
{
  /**
   * Recupera uma variável flash
   * @param string $name nome da variável
   */
  public function getFlash($name)
  {
    $value = null;

    if (isset($_SESSION[$name])) {
      if (count($_SESSION[$name]['requests']) >= 1) {
        unset($_SESSION[$name]);
      } else {
        $key = $_REQUEST['controller'].$_REQUEST['action'];
        $_SESSION[$name]['requests'][$key] = date('Y-m-d h:i:s');

        $value = $_SESSION[$name]['value'];
      }
    }

    return $value;
  }

  /**
   * Cria uma variável flash
   * @param string $name nome da variável
   * @param mix $value
   */
  public function setFlash($name, $value)
  {
    $_SESSION[$name] = array('requests' => array(), 'value' => $value);
  }
  
  /**
   * Redireciona para um determinado controller e action
   * @param string $controller nome do controlador
   * @param string $action nome da action
   * @param array $params parâmetros
   */
  public function redirectTo($controller, $action, $params = array())
  {
    $params['controller'] = $controller;
    $params['action'] = $action;

    header(sprintf("Location: ?%s", http_build_query($params)));
    exit;
  }

  /**
   * Erro 404
   */
  public function error404Action()
  {
    header("HTTP/1.0 404 Not Found");
  }

  /**
   * Cria um novo parâmetro na requisição
   * @param string $name nome do parâmetro
   * @param mix $value valor para o parâmetro
   */
  public function __set($name, $value)
  {
    $_REQUEST[$name] = $value;
  }

  /**
   * Ação realizada quando algum item não é encontrado
   * @param string $controller Nome do controlador
   * @param string $action Nome da ação
   */
  public function notFound($controller, $action = 'list')
  {
    // Cria a variável flash
    $this->setFlash('notice_error', 'Not found');

    // Redireciona para a listagem
    $this->redirectTo($controller, $action);
  }
}
