<?php
class Db_AclPermissao extends ZC_Db_Table_Abstract
{
	protected $_name = 'acl_permissao';

	public static function getInstance(){
        self::$_instance = new self();
        return self::$_instance;
    }
    
    
    public function findPermissao($grupo,$pagina){
    	return $this->fetchRow(array('ID_GRUPO = ?'=>$grupo,'ID_PAGINA = ?'=>$pagina));
    }
    
    public function save(array $data) {
    	$data['ATIVO'] = ($data['ATIVO']) ? $data['ATIVO'] : 0 ;
    	parent::save($data);
    }
    
}

?>