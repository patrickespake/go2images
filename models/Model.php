<?php

/**
 * Específica quais itens as classes models devem implementar
 */
interface Model
{ 
  /**
   * Método que obtém os erros de validações
   * @return array com os erros
   */
  public function getErrors();
  
  /**
   * Método que verifica se um item é válido
   * Faz todas as navegações necessárias
   * @return boolean true caso seja válido, false caso contrário
   */
  public function isValid();
  
  /**
   * Método que salva o item
   */
  public function save();

  /**
   * Método que especifica se o item é novo registro
   */
  public function newRecord();

  /**
   * Método para buscar todos os itens que correspondem com a condição
   * @param mix $conditions condição
   */
  public static function findAll($conditions = null);

  /**
   * Método para buscar um item pelo id
   * @param integer $id chave primária
   */
  public static function find($id);

  /**
   * Método para apagar todos os itens que correspondem com a condição
   * @param mix $conditions condições
   */
  public static function deleteAll($conditions);

  /**
   * Método para apagar um item pelo id
   * @param integer $id chave primária
   */
  public static function delete($id);

  /**
   * Método para atualizar vários itens que correspondem com a condição
   */
  public static function updateAll($conditions, $values);

  /**
   * Método para atualizar um item pelo id com os valores
   * @param mix $values valores para serem atualizados
   */
  public static function update($values);

  /**
   * Método para inserir um novo item
   * @param mix $values valores a serem inseridos
   */
  public static function insert($values);
}
