<?php

class Adm_Form_PagPaginaMenu extends ZC_Form {

    public function populate(array $values) {
        $this->getElement('UPLOAD')->setParam('ID', $values['ID']);
        return parent::populate($values);
    }

    public function init() {
        $this->addAttribs(array('id' => 'formPagPaginaMenu'));

        $this->eHidden('ID');
        $this->eRadio('ID_CATEGORIA', 'Tipo de Menu:', true, array(1 => 'Institucional', 2 => 'LINK', 3 => 'Submenu'));
        $this->eText('TITULO', 'Título:', false, 100, 80);
        $this->eText('RESUMO', 'Resumo:', false, 300, 80);
        $this->eText('YOUTUBE', 'Vídeo Youtube:', false, 250, 80);
        $this->eText('LINK', 'Link:', false, 250, 80);
        $this->eText('AUTOR', 'Autor:', false, 100, 80);
        $this->eTextarea('TEXTO', 'Texto:', false);
        $this->addElement('Upload', 'UPLOAD')
                ->setPasta('/upload/arq_arquivo/')
                ->setAction('/adm/auxiliar/jupload/')
                ->setParam('TABELA', 'pag_pagina')
                ->setCallback('loadImagem();')
                ->setMulti(true)
                ->setLabel('Imagem:');

        $this->eSubmit('ENVIAR', 'Enviar');

        $this->addDisplayGroup(array('ID','ID_CATEGORIA','TITULO','LINK', 'RESUMO', 'YOUTUBE', 'AUTOR', 'TEXTO', 'UPLOAD', 'ENVIAR'), 'groupDados', array('legend' => 'Dados'));

        $this->cSetDecoratorTable();
    }

}
