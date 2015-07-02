<?php

class RowTagTag extends Zend_Db_Table_Row_Abstract {

    public function init() {
        
    }

}

class Db_TagTag extends ZC_Db_Table_Abstract {

    protected $_name = 'tag_tag';
    protected $_nome_log = 'Tag';
    protected $_rowClass = 'RowTagTag';

    public function padrao(Zend_Db_Select $s) {
        $s->order("NOME");
        return $s;
    }

    public function filtro(Zend_Db_Select $s) {
        $vFiltro = ZC_Db_Filtro::get();
        if ($vFiltro['TAG']) {
            $s->where("NOME like '{$vFiltro['TAG']}%'");
        }
        return $s;
    }

    public function fetchBuscaTag($tag) {
        $o = $this->fetchAll(array("NOME = ?" => "$tag"));
        return $o;
    }

    function verificaTag($tag) {
        $s = $this->select()->from($this->_name)
                ->where("NOME = '{$tag}'")
                ->where("NOME = '{$tag}'")
        ;
        $o = $this->fetchAll($s)->current();
        if (count($o) <= 0) {
            return $this->insert(array('NOME' => $tag));
        } elseif (count($o)) {
            return $o->ID;
        }
    }

    function countTag($id) {
        return count($this->fetchAll(array('ID = ?' => $id))->toArray());
    }

    public function delete($where) {
        $vo = $this->fetchAll($where);
        $dbArquivo = new Db_ArqArquivo();
        foreach ($vo as $o) {
            $dbArquivo->delete(array('ID_PAGINA = ?' => $o->ID));
        }

        return parent::delete($where);
    }

}
