<?php

$html .= trim('
class Adm_Form_{DB_FORM} extends ZC_Form {

    public function populate(array $values) {');

$html .= trim(
        'if ($this->getElement("UPLOAD")) {
            $this->getElement("UPLOAD")->setParam("ID", $values["ID"]);
        }');
$html .= trim(
        'return parent::populate($values);
    }

    public function init() {
        $this->addAttribs(array("id" => "form{DB_FORM}"));
    ');
$html .= trim('$this->eHidden("{COLUNA}");');
$html .= trim('$this->eText("{COLUNA}", "{LABEL}:", {VALIDACAO}, 100, 80);');
$html .= trim('$this->eData("{COLUNA}", "{LABEL}:", {VALIDACAO});');
$html .= trim('$this->ePassword("{COLUNA}", "{LABEL}:", {VALIDACAO});');
$html .= trim('$this->eTextarea("{COLUNA}", "{LABEL}:", {VALIDACAO},false,false,true);');
$html .= trim('$this->eSubmit("ENVIAR", "{LABEL}");');
$html .= trim('$this->eSelect("{COLUNA}", "{LABEL}:", {VALIDACAO},{OPCOES});');
$html .= trim('$this->eMultiCheckbox("{COLUNA}", "{LABEL}:", {VALIDACAO},{OPCOES});');
$html .= trim('$this->eRadio("{COLUNA}", "{LABEL}:", {VALIDACAO},{OPCOES});');

$html .= trim('
$this->addElement("Upload", "UPLOAD")
        ->setPasta("/upload/arq_arquivo/")
        ->setAction("/adm/auxiliar/jupload/")
        ->setParam("TABELA", "{COLUNA}")
        ->setCallback("loadImagem();")
        ->setMulti(true)
        ->debug(true)
        ->setLabel("Imagem:");
');
$html .= trim('
$this->addDisplayGroup(array({LISTA_COLUNA}), "groupDados", array("legend" => "Dados"));
$this->cSetDecoratorTable();
}

}');
