<?php

class Db_PagNoticiaTag extends ZC_Db_Table_Abstract {

    protected $_name = 'pag_noticia_tag';
    protected $_nome_log = 'NoticiaTag';

    public function padrao(Zend_Db_Select $s) {
        return $s;
    }

    public function getTagsById($idPagina) {
        $select = $this->select()->setIntegrityCheck(false)
                ->from(array($this->_name))
                ->join(array('t' => 'tag_tag'), "t.ID = {$this->_name}.ID_TAG", 't.NOME')
                ->join(array('e' => 'pag_pagina'), "e.ID = {$this->_name}.ID_PAGINA", "")
                ->where("e.ID = {$idPagina}");
        return $this->fetchAll($select);
    }

    public function getNuvem() {
        $s = $this->select()->setIntegrityCheck(false)
                ->from(array($this->_name), array("CNT" => "count(1)", "t.ID", "t.NOME"))
                ->join(array('t' => 'tag_tag'), "t.ID = {$this->_name}.ID_TAG", '')
                ->group("t.ID")
                ->order("count(t.ID) desc");
        ;
        return $this->fetchAll($s);
    }

    public function delete($where) {
        return parent::delete($where);
    }

    public function save(array $data) {
        return parent::save($data, '');
    }

}
