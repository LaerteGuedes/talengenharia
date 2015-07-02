<?php
class Db_AclLog extends ZC_Db_Table_Abstract {

	protected $_name = 'acl_log';
	protected static $_instance = null;
    public static function getInstance(){self::$_instance = new self();return self::$_instance;}
	
    public function save($data){
    	$data['DATA'] = date("Y-m-d H:i:s");
    	$data['USUARIO'] = ZC_Auth::getInstance()->getIdentity()->NOME;
		parent::save($data);
    }
    
    protected function filtro(Zend_Db_Select $s){
		$vFiltro = ZC_Db_Filtro::get();
    	$bData = new ZC_Date();
    	if($vFiltro['USUARIO']):
    		$s->where("USUARIO = '{$vFiltro['USUARIO']}'");
    	endif;
    	if($vFiltro['DATA_INICIO']):
    		$s->where("DATA >= '{$bData->toDb($vFiltro['DATA_INICIO'])}'");
    	endif;
    	if($vFiltro['DATA_FIM']):
    		$s->where("DATA <= '{$bData->toDb($vFiltro['DATA_FIM'])}'");
    	endif;
    	return $s;
    }
}