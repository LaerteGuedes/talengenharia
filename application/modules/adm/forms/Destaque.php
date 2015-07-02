<?php
class Adm_Form_Destaque extends ZC_Form {

    public function populate(array $values) {if ($this->getElement("UPLOAD")) {
            $this->getElement("UPLOAD")->setParam("ID", $values["ID"]);
        }return parent::populate($values);
    }

    public function init() {
        $this->addAttribs(array("id" => "formDestaque"));$this->eHidden("ID");$this->eHidden("TIPO",'destaque');$this->eText("TITULO", "Nome do Destaque:", true, 100, 80);$this->eText("RESUMO", "Resumo:", true, 100, 80);$this->eTextarea("TEXTO", "Descrição:", true,false,false,true);$this->eData("DATA_CADASTRO", "Data:", true);$this->addElement("Upload", "UPLOAD")
        ->setPasta("/upload/arq_arquivo/")
        ->setAction("/adm/auxiliar/jupload/")
        ->setParam("TABELA", "pag_pagina")
        ->setCallback("loadImagem();")
        ->setMulti(true)
        ->debug(true)
        ->setLabel("Imagem:");$this->eSubmit("ENVIAR", "Enviar");$this->addDisplayGroup(array("ID","TIPO","TITULO","RESUMO","TEXTO","DATA_CADASTRO","UPLOAD","ENVIAR"), "groupDados", array("legend" => "Dados"));
$this->cSetDecoratorTable();
}

}