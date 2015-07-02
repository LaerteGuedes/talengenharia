<?php

class Adm_AclController extends ZC_Controller_Action {

    public function init() {
        $this->_data = $this->_request->getParams();
        $this->_dbAclGrupo = new Db_AclGrupo();
    }

    public function savepermissaoAction() {
        $this->disableLayout();
        $this->disableView();
        $dbPermissao = new Db_AclPermissao();
        try {
            foreach ($this->_data['pagina'] as $pagina):
                $dbPermissao->save($pagina);
            endforeach;
            ZC_Alerta::add('sucesso', 'Permissões alteradas com sucesso!');
        } catch (Exception $e) {
            ZC_Alerta::add('erro', $e->getMessage());
        }
        $this->_redirect('/adm/acl/permissoes');
    }

    public function grupoAction() {
        $formAclGrupo = new Adm_Form_AclGrupo();
        if ($this->_request->isPost()):
            $this->savegrupo();
        endif;
        if ($this->_data['editar']):
            $oGrupo = $this->_dbAclGrupo->find($this->_data['editar'])->current();
            $formAclGrupo->populate($oGrupo->toArray());
        endif;

        $this->view->voGrupos = $this->_dbAclGrupo->fetchAll()->paginator();
        $this->view->formGrupo = $formAclGrupo;
    }

    protected function savegrupo() {
        try {
            $this->_dbAclGrupo->save($this->_data);
            ZC_Alerta::add("sucesso", "Grupo cadastrado com sucesso!");
        } catch (Exception $e) {
            ZC_Alerta::add("erro", "Ocorreu um erro ao cadastrar o grupo!");
        }
    }

    public function excluirgrupoAction() {
        try {
            $nQnt = $this->_dbAclGrupo->delete(array('ID IN(?)' => $this->_data['ID']));
            ZC_Alerta::add("sucesso", $nQnt . " registros(s) excluído(s) com sucesso!");
        } catch (Exception $e) {
            ZC_Alerta::add("erro", $e->getMessage());
        }
        $this->_redirect('/adm/acl/grupo/');
    }

    public function permissoesAction() {
        $this->view->grupos = $this->_dbAclGrupo->fetchAll();
    }

    public function breadcrumbAction() {
        $oHeader = new Business_Head();
        $oHeader->jqueryTree();
        $dbPagina = new Db_AclPagina();
        $this->view->voModules = $dbPagina->getModules();
    }

    public function buscapaginasAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $dbPagina = new Db_AclPagina();
        echo $this->view->partial('/_includes/acl_permissao.phtml', array('pages' => $dbPagina->getPages(), 'ID_GRUPO' => $this->_data['ID_GRUPO']));
        $voPaginas = $dbPagina->fetchAll(array('PERMISSAO = ?' => true));
    }

    public function alteraordemAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $dbPagina = new Db_AclPagina();
        $oNode = $dbPagina->find($this->_data['NODE'])->current();
        $oNodeRef = $dbPagina->find($this->_data['REF_NODE'])->current();
        if ($this->_data['TYPE'] == 'inside'):
            $oNode->PAI = $oNodeRef->ID;
        else:
            $oNode->PAI = $oNodeRef->PAI;
        endif;
        $oNode->save();
    }

    public function logAction() {
        $oForm = new Adm_Form_FiltroLog();
        $oForm->populate($this->_data);
        $this->view->form = $oForm;

        $dbLog = new Db_AclLog();
        $this->view->voLog = $dbLog->fetchAll(null, 'DATA DESC')->paginator();
    }

    function paginaAction() {
        $dbAclPagina = new Db_AclPagina();
        $oUsuario = Zend_Registry::get('oUsuario');
        if ($oUsuario->ID_GRUPO <> 1):
            ZC_Alerta::add('erro', 'você nao tem permissão para acessar essa página.');
            $this->_redirect('/adm/auth/login/');
        endif;
        $this->view->dbPagina = $dbAclPagina;
        if ($this->_request->isPost()):
            $this->savepagina();
        endif;
        $front = $this->getFrontController();
        $acl = array();
        foreach ($front->getControllerDirectory() as $module => $path) {
            foreach (scandir($path) as $file) {
                if (strstr($file, "Controller.php") !== false) {
                    include_once $path . DIRECTORY_SEPARATOR . $file;
                    foreach (get_declared_classes() as $class) {
                        if (is_subclass_of($class, 'Zend_Controller_Action')) {
                            $controller = strtolower(substr($class, 0, strpos($class, "Controller")));
                            $controller = explode('_', $controller);
                            $controller = (count($controller) > 1) ? $controller[1] : $controller[0];
                            $actions = array();
                            foreach (get_class_methods($class) as $action) {
                                if (strstr($action, "Action") !== false) {
                                    $actions[] = str_replace('Action', '', $action);
                                }
                            }
                        }
                    }
                    $acl[$module][$controller] = $actions;
                }
            }
        }
        $this->view->vNavigation = $acl;
    }

    protected function savepagina() {
        $dbAclPagina = new Db_AclPagina();
        try {
            $dbAclPagina->update(array('ATIVO' => 0, 'BREADCRUMB' => 0), '');
            foreach ($this->_data['pagina'] as $vPagina):
                $dbAclPagina->save($vPagina);
                //echo $vPagina['MODULE'].'/'.$vPagina['CONTROLLER'].'/'.$vPagina['ACTION'].'/'.$vPagina['NOME'].'<br/>';
            endforeach;
            ZC_Alerta::add('sucesso', 'Paginas alteradas com sucesso!');
        } catch (Exception $e) {
            ZC_Alerta::add('erro', $e->getMessage());
        }
    }

    protected function recuperasenhaAction() {
        $form = new Adm_Form_Senha();
        if ($this->_request->isPost()):
            $dbUsuario = new Db_UsuUsuario();
            $oUsuario = $dbUsuario->setPadrao(false)->fetchRow(array("LOGIN = ?" => "{$this->_data['LOGIN']}"));
            if (count($oUsuario) > 0) {
                $bMail = new Business_Mail();

                try {
                    $bMail->sendSenha($oUsuario);
                    ZC_Alerta::add('sucesso', "Sua senha foi enviada para o email '{$oUsuario->EMAIL}'. Verifique sua caixa de entrada");
                } catch (Exception $exc) {
                    ZC_Alerta::add('erro', "Não foi possível enviar sua senha. Tente novamente mais tarde ou contate o administrador");
                }
            } else {
                $form->populate($this->_data);
                ZC_Alerta::add('erro', "Usuário não encontrado. Verifique corretamente o Login do usuário e tente novamente");
            }
        endif;

        $this->view->form = $form;
    }

    protected function alterasenhaAction() {
        $dados = $this->_data;
        $form = new Adm_Form_UsuUsuarioAlterar();
        $form->removeElement('SENHA_ATUAL');
        $dbSenha = new Db_AclSenha();
        if ($this->_request->isPost()) {
            if ($dados['SENHA'] == $dados['SENHA2']) {
                $dbUsuario = new Db_UsuUsuario();

                $oSenha = $dbSenha->fetchRow(array('PASS = ?' => $this->_data['PASS']));
                $oUsuario = $dbUsuario->fetchRow(array('usu_usuario.ID = ?' => $oSenha->ID_USUARIO));
                $update = array(
                    'SENHA' => ZC_Util::pwCrip($dados['SENHA'])
                );
                try {
                    $dbUsuario->update($update, array("ID = ?" => $oUsuario->ID));
                    $dbSenha->delete(array('ID_USUARIO = ?' => $dados['ID'], 'PASS' => $dados['PASS']));
                    ZC_Alerta::add('sucesso', 'Senha alterada com sucesso!');
                    $this->_redirect('/adm/index/');
                } catch (Exception $ex) {
                    ZC_Alerta::add('error', $ex->getMessage());
                }
            } else {
                $this->view->msg = 'As senhas não conferem!';
            }
        }

        $oSenha = $dbSenha->fetchRow(array('ID_USUARIO = ?' => $dados['ID'], 'PASS = ?' => $dados['PASS']));
        if (isset($oSenha->ID)) {

            $form->populate(array("ID" => $dados['ID']));
            $this->view->form = $form;
            $dbSenha->delete(array('ID_USUARIO = ?' => $dados['ID'], 'PASS' => $dados['PASS']));
        } else {
            $this->view->msg = "Você deve acessar o link que lhe foi enviado por email, caso já o tenha usado</br> 
                                alguma vez e queira trocar a senha novamente você deve fazer a solicitação novamente pois </br>
                                após clicar no link o mesmo é invalidado por motivos de serguraça da sua conta.";
        }
    }

}
