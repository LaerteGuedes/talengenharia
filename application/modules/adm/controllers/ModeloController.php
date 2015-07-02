<?php

'<?php
    class Adm_{GERENCIA}Controller extends ZC_Controller_Action {

    protected $_data;

    public function init() {
        $this->_dbPagina = new Db_PagPagina();
        parent::init();
        $this->view->data = $this->_data;
    }

    ## GERÊNCIA DE strtoupper{GERENCIA} ##

    public function indexAction() {

        ZC_Db_Filtro::setParam("TIPO", "{TIPO}");

        $dbCategoria = new Db_PagCategoria();

        $this->view->vo = $this->_dbPagina->fetchAll()->paginator();
      
        $this->view->TITULO = "Gerência de {GERENCIA}";
    }

    public function inserealteraAction() {
        $bData = new ZC_Date();
        $form = new Adm_Form_PagPagina();
        $this->inserealterapagina($form, $this->_dbPagina, "/adm/{GERENCIA_CONTROLLER}", false, "{TIPO}");
    }

    

    #### GERAL ###################

    public function excluirAction() {
        $redirect = $this->_data["move"] ? "/adm/{GERENCIA_CONTROLLER}" . $this->_data["move"] : null;
        parent::excluir($this->_dbPagina, $redirect);
    }

  

}'
;