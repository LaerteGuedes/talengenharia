<?php

class RowUsuUsuario extends Zend_Db_Table_Row_Abstract {

    public function init() {
        
    }

    public function getTarefas() {
        $dbTarefa = new Db_ProTarefa();
        $select = $dbTarefa->padrao();
        $select->where('pro_tarefa.ID_RESPONSAVEL = ?', $this->ID);
        $select->where("pro_tarefa.STATUS NOT IN(?)", 2);
        return $dbTarefa->fetchAll($select);
    }

}

class Db_UsuUsuario extends ZC_Db_Table_Abstract {

    protected $_name = 'usu_usuario';
    protected $_nome_log = 'Usuario';
    protected $_sequence = true;
    protected $_rowClass = 'RowUsuUsuario';

    public function padrao() {
        $select = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(false);
        $select->joinLeft('acl_grupo', "acl_grupo.ID = ID_GRUPO", array('GRUPO_NOME' => 'acl_grupo.NOME'));
        return $select;
    }

    function fetchAllColaboradores($aColoborador = array(1, 2, 3, 4)) {
        return $this->fetchAll(array('ID_GRUPO IN (?)' => $aColoborador), 'NOME ASC');
    }

    public function save(array $data) {
        if (array_key_exists('SENHA', $data)):
            $data['SENHA'] = ZC_Util::pwCrip($data['SENHA']);
        endif;
        if (array_key_exists('DATA_NASCIMENTO', $data)):
            $zDate = new ZC_Date();
            $data["DATA_NASCIMENTO"] = $zDate->toDb($data["DATA_NASCIMENTO"]);
        endif;

        return parent::save($data);
    }

}
