<?php
class IndexController extends ZC_Controller_Action {

    public $_data; //variavel com todos os parametros request

    function indexAction() {
	    $this->_head->setTitle("Página Inicial");

    }
    
    function manutencaoAction() {
	    $this->disableLayout();
        
    }

    

}