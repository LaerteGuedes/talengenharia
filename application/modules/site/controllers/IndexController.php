<?php
class IndexController extends ZC_Controller_Action {

    public $_data; //variavel com todos os parametros request

    function indexAction() {
        $dbPagina = new Db_PagPagina();

	    $this->_head->setTitle("Página Inicial");
        $this->view->voDestaque = $dbPagina->fetchAll(array('pag_pagina.TIPO = ?' => 'destaquehome'));
        $this->view->voObra = $dbPagina->fetchAll(array('pag_pagina.TIPO = ?' => 'obras'));
        $this->view->voDica = $dbPagina->fetchAll(array('pag_pagina.TIPO = ?' => 'dicas'));
    }
    
    function manutencaoAction() {
	    $this->disableLayout();
        
    }

    

}