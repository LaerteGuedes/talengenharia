<?php

class Adm_ArquivoController extends ZC_Controller_Action {

    protected $_data;

    public function init() {
        $this->_formArquivo = new Adm_Form_ArqArquivo();
        $this->_dbArqImagem = new Db_ArqArquivo();
        parent::init();
    }

    public function indexAction() {
        $vFiltro = $this->filtro();
        $this->view->vo = $this->_dbArquivo->fetchAll()->paginator();
    }

    public function inserealteraAction() {
        $this->inserealteraarquivo($this->_formArquivo, $this->_dbArquivo, '/adm/arquivo/', false, 'institucional');
    }

    public function arquivoAction() {
        $this->disableLayout();
        if ($this->_data['ID_PAGINA']):
            $this->view->voArquivo = $this->_dbArqImagem->fetchByArquivo($this->_data['ID_PAGINA'], 'pag_arquivo');
        endif;
    }

    public function excluirAction() {
        $redirect = $this->_data['move'] ? '/adm/arquivo/' . $this->_data['move'] : null;
        parent::excluir($this->_dbArquivo, $redirect);
    }

}