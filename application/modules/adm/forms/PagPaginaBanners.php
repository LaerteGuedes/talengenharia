<?php

class Adm_Form_PagPaginaBanners extends ZC_Form{

    public function populate(array $values) {
        if ($this->getElement('UPLOAD')) {
            $this->getElement('UPLOAD')->setParam('ID', $values['ID']);
        }
        return parent::populate($values);
    }

    public function init() {
        $this->addAttribs(array('id' => 'formPagPaginaBanners'));
        $this->eHidden('ID');
        $this->eText('TITULO', 'Título:', true, 100, 80);
        $this->eText('RESUMO', 'Resumo:', false, 300, 80);
        $this->eText('LINK', 'Link:', false, 100, 80);
        $this->addElement('Upload', 'UPLOAD')
            ->setPasta('/upload/arq_arquivo/')
            ->setAction('/adm/auxiliar/jupload/')
            ->setParam('TABELA', 'pag_pagina')
            ->setCallback('loadImagem();')
            ->setMulti(true)
            ->debug(true)
            ->setLabel('Imagem:');

        $this->eSubmit('ENVIAR', 'Enviar')->setAttrib('class', 'submit');
        $this->addDisplayGroup(array('ID', 'TITULO', 'LINK', "RESUMO", 'UPLOAD', 'ENVIAR'), 'groupDados', array('legend' => 'Dados'));
        $this->cSetDecoratorTable();
    }

}