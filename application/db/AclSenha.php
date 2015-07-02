<?php

class Db_AclSenha extends ZC_Db_Table_Abstract {

    protected $_name = 'acl_senha';
    protected static $_instance = null;

    public function inserePass($oBj) {
        $pass = md5(date('Y-m-d h:i:s'));
        $insert = array(
            'ID_USUARIO' => $oBj->ID,
            'PASS' => $pass
        );
       
       return $this->insert($insert);
    }

}

?>