<?php
class RowPagComentario extends Zend_Db_Table_Row_Abstract {
	public function init() {
		
	}
}

class Db_PagComentario extends ZC_Db_Table_Abstract {
	protected $_name = 'pag_comentario';
	protected $_rowClass = 'RowPagComentario';
	
	function fetchByPagina($nId){
		if(!$nId){return null;}
		$s = $this->select();
		$s->where('ID_PAGINA = ?',$nId);
		return $this->fetchAll($s);
	}
}
