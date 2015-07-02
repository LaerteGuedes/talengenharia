<?php

class RowPagNewsletter extends Zend_Db_Table_Row_Abstract {

    public function init() {
        
    }

}

class Db_PagNewsletter extends ZC_Db_Table_Abstract {

    protected $_name = 'pag_newsletter';
    protected $_nome_log = 'newsletter';
    protected $_rowClass = 'RowPagNewsletter';
}
