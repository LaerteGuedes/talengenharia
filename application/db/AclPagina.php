<?php
class Db_AclPagina extends ZC_Db_Table_Abstract {
    protected $_name = 'acl_pagina';
    protected static $_instance = null;

    public static function getInstance() {
        self::$_instance = new self();
        return self::$_instance;
    }

    public function fetchAllPermissao($nIdGrupo) {
        $select = $this->select()->from(array('pa'=>'acl_pagina'))->setIntegrityCheck(false);
        $select->joinLeft(array('pe'=>'acl_permissao'),"pa.ID = pe.ID_PAGINA and pe.ID_GRUPO = '{$nIdGrupo}'",array('LIBERADO'=>'pe.ATIVO'));
        $select->where('pa.ATIVO = ?', 1);
        return parent::fetchAll($select);
    }
    public function findPage($module,$controller,$action) {
        $select = $this->select();
        $select->where('MODULE = ?',$module);
        $select->where('CONTROLLER = ?',$controller);
        $select->where('ACTION = ?',$action);
        return $this->fetchRow($select);
    }

    public function getModules() {
        $select = $this->select()->distinct()->from($this,array('MODULE'));
        return $this->fetchAll($select);
    }

    public function getControllers($module) {
        $select = $this->select()->distinct()->from($this,array('CONTROLLER'));
        $select->where('MODULE = ?', $module);
        return $this->fetchAll($select);
    }

    public function getActions($module,$controller) {
        $select = $this->select();
        $select->where('MODULE = ?', $module);
        $select->where('CONTROLLER = ?', $controller);
        $select->where('PERMISSAO = ?', true);
        return $this->fetchAll($select);
    }

    public function getPages() {
        $pages = array();
        foreach($this->getModules() as $oM):
            foreach($this->getControllers($oM->MODULE) as $oC):
                foreach($this->getActions($oM->MODULE,$oC->CONTROLLER) as $oA):
                    $pages[$oM->MODULE][$oC->CONTROLLER][$oA->ACTION] = (object) $oA->toArray();
                endforeach;
            endforeach;
        endforeach;
        return (object) $pages;
    }


    public function save(array $data) {
        $data['ATIVO'] = 1;
        $data['PERMISSAO'] = ($data['PERMISSAO']) ? $data['PERMISSAO'] : 0 ;
        parent::save($data);

    }

    public function fetchCurrentPage() {
        $data = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        return $this->findPage($data['module'], $data['controller'], $data['action']);
    }

    public function fetchAll($where = null, $order = null, $count = null, $offset = null) {

        if(($where instanceof Zend_Db_Table_Select)) {
            $where->where('ATIVO = ?', 1);
        } else {
            $where = (is_array($where)) ? $where : array($where) ;
            $where = array_merge($where,array('ATIVO = ?'=>1));
        }
        return parent::fetchAll($where,$order,$count,$offset);
    }

}

?>