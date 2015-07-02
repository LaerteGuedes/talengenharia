<?php

class Adm_Form_PagPaginaNot extends ZC_Form {

    public function populate(array $values) {
        $this->getElement('UPLOAD')->setParam('ID', $values['ID']);
        
        $dbEstTag = new Db_PagNoticiaTag();
        $voTags = $dbEstTag->getTagsById($values['ID']);
        foreach ($voTags as $oTags):
            if ($oTags->NOME <> ""):
                $tags .= $oTags->NOME . ",";
            endif;
        endforeach;
        $values['TAG'] = $tags;

        $zDate = new ZC_Date();
        $values["DATA_INI"] = "{$zDate->render($values["DATA_INI"])}";

        return parent::populate($values);
    }

    public function init() {
        $this->addAttribs(array('id' => 'formPagPaginaNot'));
        
        $this->eHidden('ID');

        $this->eSelect('ID_CATEGORIA', 'Categorias:', false, $this->getCategoria());
       
        $this->eText('TITULO', 'Título:', true, 150, 80);

        $this->eText('LINK', 'Link:', false, 150, 80)->setDescription("* redirecionará para uma notícia externa.")->setAttrib('class', "validate[custom[url]]");

        $this->eText('YOUTUBE', 'YouTube:', false, 150, 80)->setDescription("ex: https://www.youtube.com/watch?v=zPHeuKJNQSE");

        $this->eText('CHAPEU', 'Chapeu:', false, 20, 80);

        $this->eText('RESUMO', 'Subtitulo:', false, 150, 80);

        $this->eText('TAG', 'Tags:', false, 60, 30)->setAttrib('class', "tm-input")->setAttrib('placeholder', "Tags");
        $this->eTextarea('TEXTO', 'Texto:', true);

        $this->addElement('Upload', 'UPLOAD')
                ->setPasta('/upload/arq_arquivo/')
                ->setAction('/adm/auxiliar/jupload/')
                ->setParam('TABELA', 'pag_pagina')
                ->setNameButton('Postar imagens')
                ->setCallback('loadImagem();')
                ->debug(false)
                ->setMulti(true)
                ->setLabel('Imagem/Arquivo:');

        $this->eText("DATA_INI", "Agendamento:")->setAttrib('class', "jDataHora")->setDescription("Caso não informe a data, a mesma adicionará a data cadastro.");
        $this->eText("DATA_FIM", "Desativação da Publicação")->setAttrib('class', "jDataHora");
        $this->eSubmit('ENVIAR', 'Enviar');
        $this->eHidden("FL_DESTAQUE", 0);
        $this->eText('AUTOR', 'Fonte/Autor:', false, 100, 80);
        $this->addDisplayGroup(array('ID', 'ATIVO', "ID_CATEGORIA", 'CHAPEU', 'TITULO', "TAG", 'RESUMO', "FONTE_VIDEO", 'YOUTUBE', 'AUTOR', 'TEXTO', 'UPLOAD', 'DATA_CADASTRO', 'DATA_INI', 'DATA_FIM', "HORA", 'LINK', 'ENVIAR'), 'groupDados', array('legend' => 'Dados'));

        $this->cSetDecoratorTable();
    }

    function getCategoria() {
        $db = new Db_PagCategoria();
        $vv[''] = 'Selecione...';
        $voOb = $db->fetchAll(array("ID_PAI IS NULL OR ID_PAI = '' "), "NOME");
        foreach ($voOb as $oC) {
            $vv[$oC->ID] = '#' . $oC->NOME;;
            foreach ($oC->getFilho("NOME") as $oF) {
                $vv[$oF->ID] =  ' ↳' . $oF->NOME;
            }
        }
        return ($vv) ? $vv : array();
    }

}
