<?php

class Adm_CategoriaController extends ZC_Controller_Action {

    public function init() {
        $this->_dbCategoria = new Db_PagCategoria();
        parent::init();
    }

    public function indexAction() {
        $this->view->title = 'Gerência de Categorias';
        $this->view->vo = $this->_dbCategoria->fetchAll(null, 'ORDEM ASC');
    }

    public function inserealteraAction() {
        $this->view->title = 'Editar Categoria';
        $form = new Adm_Form_PagCategoria();
        parent::inserealtera($form, $this->_dbCategoria);
    }

    public function excluirAction() {
        parent::excluir($this->_dbCategoria);
    }

}
