<?php

class RowPubPublicidade extends Zend_Db_Table_Row_Abstract {
	protected $_vCategoria = array(0=>'Banner Central',1=>'Banner Topo');
	public function init() {}
	public function getCategoria(){
		return $this->_vCategoria[$this->CATEGORIA];
	}
	public function getDestaque(){
		$dbArqImagem = new Db_ArqArquivo();
		$o =  $dbArqImagem->fetchRow(array('ID_PAGINA = ?'=>$this->ID,'TABELA = ?'=>'pub_publicidade','DESTAQUE = ?'=>1));
		return ($o) ? $o->getImagem() : false;
	}

}

class Db_PubPublicidade extends ZC_Db_Table_Abstract {

	protected $_name = 'pub_publicidade';
	protected $_rowClass = 'RowPubPublicidade';
}
