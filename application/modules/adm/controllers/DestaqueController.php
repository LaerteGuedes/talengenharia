<?php
class Adm_DestaqueController extends ZC_Controller_Action {

    protected $_data;

    public function init() {
        $this->_dbPagPagina = new Db_PagPagina();
        parent::init();
        $this->view->data = $this->_data;
    }

    ## GERÊNCIA DE DESTAQUE ##

    public function indexAction() {

        ZC_Db_Filtro::setParam("TIPO", "destaque");

        $this->view->vo = $this->_dbPagPagina->fetchAll()->paginator();

        $this->view->TITULO = "Gerência de Destaque";
    }

    public function inserealteraAction() {
        $bData = new ZC_Date();
        $form = new Adm_Form_Destaque();
        $this->view->TABELA = "pag_pagina";
        $this->inserealterapagina($form, $this->_dbPagPagina, "/adm/destaque", false, "destaque");
    }

    #### GERAL ###################

    public function excluirAction() {
        $redirect = $this->_data["move"] ? "/adm/destaque" . $this->_data["move"] : null;
        parent::excluir($this->_dbPagPagina, $redirect);
    }

}