<?php

class PaginaController extends ZC_Controller_Action {

    public $_data;

    function buscaAction() {
        $dbPagina = new Db_PagPagina();
        ZC_Db_Filtro::setParam('BUSCA', $this->_data['BUSCA']);
        $this->view->vo = $dbPagina->fetchAll(array('pag_pagina.TIPO = ?' => 'noticia'))->paginator();
    }

    function institucionalAction() {
        $dbPagina = new Db_PagPagina();

        try {
            if ($this->_data['permalink']) {
                $o = $this->view->oPagina = $dbPagina->fetchRow(array('PERMALINK = ?' => $this->_data['permalink']));
            } else {
                $o = $this->view->oPagina = $dbPagina->fetchRow(array('pag_pagina.ID = ?' => $this->_data['ID']));
            }
            if (!$this->view->oPagina) {
                throw new Exception;
            }
        } catch (Exception $exc) {
            throw new Zend_Controller_Action_Exception('A página que você deseja acessar não existe.', 404);
        }
        $this->_head->setTitle($this->view->oPagina->TITULO);

        $this->openGraph(array("image" => $o->linkImagemDestaque(), "title" => $o->TITULO, "description" => $o->RESUMO));
    }

    function obrasAction(){
        $dbPagina = new Db_PagPagina();
        $this->_head->setTitle('Obras');

        $this->view->oPagina = $dbPagina->fetchRow(array('pag_pagina.PERMALINK = ?' => 'obras'));
        $this->view->voObra = $dbPagina->fetchAll(array('pag_pagina.TIPO = ?' => 'obras'));
        $this->view->voParceiro = $dbPagina->fetchAll(array('pag_pagina.TIPO = ?' => 'parceiros'));
    }

    function contatoAction() {
        $dbPagina = new Db_PagPagina();
        $this->_head->jqueryMask();

        $this->view->voObra = $dbPagina->fetchAll(array('pag_pagina.TIPO = ?' => 'obras'));
        $this->view->voParceiro = $dbPagina->fetchAll(array('pag_pagina.TIPO = ?' => 'parceiros'));

        $this->view->oPagina = $dbPagina->fetchRow(array('pag_pagina.PERMALINK = ?' => 'contato'));
        if ($this->_request->isPost()) {
            $bMail = new Business_Mail();
            $bMail->sendContato($this->_data);
            ZC_Alerta::add('sucesso', 'Mensagem Enviada com sucesso!');
        }
        $this->_head->setTitle($this->view->oPagina->TITULO);
    }

    function contatoajaxAction(){
        $this->disableLayout();
        $this->disableView();

        if ($this->_request->isPost()) {
            $bMail = new Business_Mail();
            $bMail->sendContato($this->_data);
            $response = array('encode' => true);
            echo json_encode($response);
        }
   }


    function empresaAction() {
        $this->_head->setTitle('Empresa');

        $dbPagina = new Db_PagPagina();

        $this->view->oPagina = $dbPagina->fetchRow(array('pag_pagina.PERMALINK = ?' => 'empresa'));
        $this->view->voObra = $dbPagina->fetchAll(array('pag_pagina.TIPO = ?' => 'obras'));
        $this->view->voParceiro = $dbPagina->fetchAll(array('pag_pagina.TIPO = ?' => 'parceiros'));
    }

    function servicosAction() {
        $this->_head->setTitle('Serviços');

        $dbPagina = new Db_PagPagina();

        $this->view->oPagina = $dbPagina->fetchRow(array('pag_pagina.PERMALINK = ?' => 'servicos'));
        $this->view->voObra = $dbPagina->fetchAll(array('pag_pagina.TIPO = ?' => 'obras'));
        $this->view->voParceiro = $dbPagina->fetchAll(array('pag_pagina.TIPO = ?' => 'parceiros'));
    }

    function clientesAction() {
        $this->_head->setTitle('Clientes');

        $dbPagina = new Db_PagPagina();

        $this->view->oPagina = $dbPagina->fetchRow(array('pag_pagina.PERMALINK = ?' => 'clientes'));
        $this->view->voObra = $dbPagina->fetchAll(array('pag_pagina.TIPO = ?' => 'obras'));
        $this->view->voParceiro = $dbPagina->fetchAll(array('pag_pagina.TIPO = ?' => 'parceiros'));
    }

    function produtoAction() {
        $dbProduto = new Db_ProProduto();


        try{
            $oProduto = $dbProduto->fetchRow(array('pro_produto.PERMALINK = ?' => $this->_data['permalink']));
            if (!(isset($oProduto->ID))){
                throw new Exception('Página não encontrada!');
            }
            $this->view->voProduto = $dbProduto->fetchAll(array('ID_CATEGORIA = ?' => 8));
            $this->view->oProduto = $oProduto;

            $this->_head->setTitle($oProduto->NOME);
        }catch (Exception $ex){
            echo $ex->getMessage();
        }
    }

    function noticiaAction() {
        $dbPagina = new Db_PagPagina();
        $this->view->oPagina = $dbPagina->fetchRow(array('pag_pagina.PERMALINK = ?' => 'novidades'));
        $this->view->vo = $dbPagina->fetchAll(array('pag_pagina.TIPO = ?' => 'noticia'))->paginator();
        $this->_head->setTitle($this->view->oPagina->TITULO);
    }

    function noticiadetalheAction(){
        $dbPagina = new Db_PagPagina();
        try {
            if ($this->_data['permalink']) {
                $o = $this->view->oPagina = $dbPagina->fetchRow(array('PERMALINK = ?' => $this->_data['permalink']));
            } else {
                $o = $this->view->oPagina = $dbPagina->fetchRow(array('pag_pagina.ID = ?' => $this->_data['ID']));
            }
            if (!$this->view->oPagina) {
                throw new Exception;
            }
        } catch (Exception $exc) {
            throw new Zend_Controller_Action_Exception('A página que você deseja acessar não existe.', 404);
        }
        $this->_head->setTitle($this->view->oPagina->TITULO);
        $this->openGraph(array("image" => $o->linkImagemDestaque(), "title" => $o->TITULO, "description" => $o->RESUMO));
    }


}
