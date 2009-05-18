<?php

/**
 * Gera o link para editar um item
 * @param string $controller Nome do controlador
 * @param object $object Informações do objeto
 * @return string
 */
function link_to_edit($controller, $object)
{
  $href = sprintf("?controller=%s&action=edit&id=%s", $controller, $object->getId());
  return '<a href="'.$href.'"><img src="public/images/application_edit.png" alt="Edit" /></a>';
}

/**
 * Gera o link para remover um item
 * @param string $controller Nome do controlador
 * @param object $object Informações do objeto
 * @return string
 */
function link_to_delete($controller, $object)
{
  $href = sprintf("?controller=%s&action=delete&id=%s", $controller, $object->getId());
  return '<a onclick="if (confirm(\'Are you sure?\')) { var f = document.createElement(\'form\'); f.style.display = \'none\'; this.parentNode.appendChild(f); f.method = \'POST\'; f.action = this.href; f.submit(); };return false;" href="'.$href.'"><img src="public/images/delete.png" alt="Delete"/></a>';
}

/**
 * Gera o link para exibir um item
 * @param string $controller Nome do controlador
 * @param object $object Informações do objeto
 * @return string
 */
function link_to_show($controller, $object)
{
  $href = sprintf("?controller=%s&action=show&id=%s", $controller, $object->getId());
  return '<a href="'.$href.'"><img src="public/images/magnifier.png" alt="Show" /></a>';
}
?>
