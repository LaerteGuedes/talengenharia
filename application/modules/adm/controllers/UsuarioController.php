<?php

class Adm_UsuarioController extends ZC_Controller_Action {

    protected $_data;
    protected $_dbUsuario;

    public function init() {
        $this->_dbUsuario = new Db_UsuUsuario();
        $this->_formUsuario = new Adm_Form_UsuUsuario();
        parent::init();
    }

    public function indexAction() {
        $form = new Adm_Form_fUsuUsuario();
        $form->populate(ZC_Db_Filtro::get());
        $this->view->formFiltro = $form;
        $this->view->vo = $this->_dbUsuario->fetchAll()->paginator();
    }

    public function inserealteraAction() {
        parent::inserealtera($this->_formUsuario, $this->_dbUsuario);
    }

    public function alterasenhaAction() {
        $form = new Adm_Form_UsuUsuarioAlterar();
        $db = new Db_UsuUsuario();
        echo '<h1>Alterar Senha</h1>';
        $this->_helper->viewRenderer->setNoRender();
        if ($this->_request->isPost()):
            $oUser = $db->fetchRow(array("ID = ?", ZC_auth::getInstance()->getIdentity()->ID));
            if ($oUser && ZC_Util::pwCrip($this->_data['SENHA_ATUAL']) == $oUser->SENHA):
                if ($this->_data['SENHA'] == $this->_data['SENHA2']):
                    $this->_data['ID'] = $oUser->ID;
                    $db->save($this->_data);
                    ZC_Alerta::add('sucesso', 'Senha alterada com sucesso.');
                else:
                    ZC_Alerta::add('erro', 'As senhas não conferem, veirique e tente novamente.');
                endif;
            else:
                ZC_Alerta::add('erro', 'Não foi possível alterar, motivo: senha atual incorreta.');
            endif;
        endif;

        echo $form;
        //$this->_dbUsuario
    }

    protected function save() {
        parent::save($this->_dbUsuario);
    }

    public function excluirAction() {
        parent::excluir($this->_dbUsuario);
    }

}