<?php

class RowConfigMail extends Zend_Db_Table_Row_Abstract{
    public function getDestaque($tabela) {
        $dbArqImagem = new Db_ArqArquivo();
        $o = $dbArqImagem->fetchRow(array('ID_PAGINA = ?' => $this->ID, 'TABELA = ?' => $tabela, 'DESTAQUE = ?' => 1));
               
        return ($o) ? $o->getImagem() : false;
    }
}

class Db_ConfigMail extends ZC_Db_Table_Abstract{
    protected $_name = 'config_mail';
    protected $_nome_log = 'Configuracao_Mail';
    protected $_sequence = true;
    protected $_rowClass = 'RowConfigMail';
    
}