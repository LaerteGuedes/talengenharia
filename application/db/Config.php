<?php

class RowConfig extends Zend_Db_Table_Row_Abstract {

    public function getDestaque($tabela) {
        $dbArqImagem = new Db_ArqArquivo();
        $o = $dbArqImagem->fetchRow(array('ID_PAGINA = ?' => $this->ID, 'TABELA = ?' => $tabela, 'DESTAQUE = ?' => 1));
               
        return ($o) ? $o->getImagem() : false;
    }

    public function init() {
        
    }

}

class Db_Config extends ZC_Db_Table_Abstract {

    protected $_name = 'config';
    protected $_nome_log = 'Configuracao';
    protected $_sequence = true;
    protected $_rowClass = 'RowConfig';

    public function padrao() {
        $s = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(false);

        return $s;
    }

    public function save(array $data) {
        return parent::save($data);
    }

}
