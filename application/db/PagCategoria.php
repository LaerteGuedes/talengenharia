<?php

class RowPagCategoria extends Zend_Db_Table_Row_Abstract {

    public function init() {
        
    }

    function convertUtf8($sColuna) {
//        if (mb_detect_encoding($this->$sColuna, "UTF-8, ISO-8859-1") == 'ISO-8859-1') {
//            $this->$sColuna = utf8_encode($this->$sColuna);
//        }
    }

    public function getFilho($ordem = 'ID DESC') {
        $db = new Db_PagCategoria();
        $vo = $db->disableFiltro()->fetchAll(array('ID_PAI = ?' => $this->ID), "$ordem");

        if (count($vo)):
            return $vo;
        else:
            return false;
        endif;
    }

    function getLink() {
        if ($this->ID == 27):
            $s = '/esporte/parazao';
        elseif ($this->ID == 22):
            $s = '/esporte/brasileirao';
        elseif (substr($this->ORDEM, 0, 3) == '077'):
            $s = '/amazonia/' . $this->ID;
        elseif (substr($this->ORDEM, 0, 3) == '093'):
            $s = '/oliberal/' . $this->ID;
        else:
            $s = '/noticias/' . $this->ID;
        endif;
        return $s;
    }

    function getNivel() {
        return strlen($this->ORDEM) / 3;
    }

    function checkAmazonia() {
        
    }

    function getNumPagina($where = null) {
        $dbPagina = new Db_PagPagina();
        $s = $dbPagina->select()->setIntegrityCheck(false)->from('pag_pagina', array('NUM' => 'COUNT(1)'));
        $s->where("ID_CATEGORIA = '{$this->ID}'");
        if ($where) {
            $s->where($where);
        }
        $o = $dbPagina->setPadrao(false)->fetchRow($s);
        return ($o) ? $o->NUM : 0;
    }

}

class Db_PagCategoria extends ZC_Db_Table_Abstract {

    protected $_name = 'pag_categoria';
    protected $_primary = 'ID';
    protected $_rowClass = 'RowPagCategoria';

//    public function padrao(Zend_Db_Select $s) {
//		$s->setIntegrityCheck(false);
//		$s->joinLeft('pag_pagina', "pag_pagina.ID_CATEGORIA = pag_categoria.ID", array('PAGINAS' => 'COUNT(*)'));
//		$s->group(array('pag_categoria.ID','pag_categoria.NOME'));
//        return $s;
//    }
    public function getVideoCat($where = null) {
        $s = $this->select()->from(array($this->_name), array($this->_name . ".ID", $this->_name . ".ORDEM", $this->_name . ".NOME", "CONTADOR" => "count(1)"));
        $s->setIntegrityCheck(false);
        $data = new ZC_Date();
        $data = $data->toString('yyyy-MM-dd HH:mm:ss');
        $s->joinInner(array("v" => "vid_video"), "v.ID_CATEGORIA = {$this->_name}.ID and v.DATA_INI <= '{$data}' and ((v.DATA_FIM >= v.DATA_INI AND v.DATA_FIM > '{$data}' or v.DATA_FIM is null))", array());
        $s->group(array($this->_name . ".ID", $this->_name . ".NOME", $this->_name . ".ORDEM"));
        if ($where) {
            $s->having("$where");
        }
        $s->order($this->_name . ".ORDEM");
        return $this->fetchAll($s);
    }

    public function save($data) {
        $oPai = $this->fetchRow("ID = '{$data['ID_PAI']}'");
        if ($oPai):
            $nOrdem = $oPai->ORDEM;
        else:
            $nOrdem = '';
        endif;
        $nId = parent::save($data);
        $nId = ($data['ID']) ? $data['ID'] : $nId;
        $this->update(array('ORDEM' => $nOrdem . str_pad($nId, 3, "0", STR_PAD_LEFT)), "ID = '{$nId}'");
        return $nId;
    }

}
