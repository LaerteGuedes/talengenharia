<?php

class Adm_Form_SiteConf extends ZC_Form {

    public function populate(array $values) {
        $this->getElement('UPLOAD2')->setParam('ID', $values['ID']);
        $this->getElement('UPLOAD3')->setParam('ID', $values['ID']);
        $this->getElement('UPLOAD4')->setParam('ID', $values['ID']);
        return parent::populate($values);
    }

    public function init() {
        $this->addAttribs(array('id' => 'formConf'));
        $this->eHidden('ID');
        $this->eRadio("MANUTENCAO", "Estado de Manutenção", false, array(0 => "Inativo", 1 => "Ativo"));
        $this->eText("TITULO", 'Titulo1', true, 100, 80);
        $this->eText("TITULO2", 'Titulo2', false, 100, 80);
        $this->eText("TITULO3", 'Titulo3', false, 100, 80);
        $this->eText("EMAIL", 'E-mail p/ Contato', true, 100, 80);
        $this->addElement('Upload', 'UPLOAD2')
                ->setPasta('/upload/arq_arquivo/')
                ->setAction('/adm/auxiliar/jupload/')
                ->setParam('TABELA', 'config_manutencao')
                ->setCallback('loadImagem2();')
                ->setMulti(true)
                ->setLabel('Imagem Manutenção:');

        $this->addElement('Upload', 'UPLOAD3')
                ->setPasta('/upload/arq_arquivo/')
                ->setAction('/adm/auxiliar/jupload/')
                ->setParam('TABELA', 'config_favicon')
                ->setCallback('loadImagem3();')
                ->setMulti(false)
                ->setLabel('Imagem Favicon:');
        
        $this->addElement('Upload', 'UPLOAD4')
                ->setPasta('/upload/arq_arquivo/')
                ->setAction('/adm/auxiliar/jupload/')
                ->setParam('TABELA', 'config_mail_topo')
                ->setCallback('loadImagem4();')
                ->setMulti(false)
                ->setLabel('Imagem de Topo do E-mail');
                
        $this->eSubmit('ENVIAR', 'Enviar');
        $this->addDisplayGroup(array("MANUTENCAO", "UPLOAD2"), 'groupDados1', array('legend' => 'Manutenção'));
        $this->addDisplayGroup(array("TITULO","TITULO2","TITULO3"), 'groupDados2', array('legend' => 'Configuração do Site'));
        $this->addDisplayGroup(array("EMAIL"), 'groupDados3', array('legend' => 'Configurações de Email'));
        $this->addDisplayGroup(array("UPLOAD3"), 'groupDados4', array('legend' => 'Imagens'));
        $this->addDisplayGroup(array("UPLOAD4"), 'groupDados5', array('legend' => 'Imagem do Topo do Email'));
        $this->addDisplayGroup(array("ENVIAR"), 'groupDados7', array('legend' => 'Alterar'));
        $this->cSetDecoratorTable();
    }

}
