<?php

class Adm_AuthController extends ZC_Controller_Action {

    function loginAction() {
        $this->disableLayout();
        if ($this->_request->isPost()):
            $auth = new ZC_Auth();
            if ($auth->isValidNew($this->_data['LOGIN'], $this->_data['SENHA'], true)):
                $osRedirect = new Zend_Session_Namespace('redirect');
                if ($osRedirect->url)://VERIFICA SE EXISTE UMA PAGINA PARA REDIRECIONAR
                    $this->_redirect('/' . urldecode($osRedirect->url));
                    $osRedirect->unsetAll();
                else:
                    $this->_redirect('/' . $this->_data['module']);
                endif;
            else:
                ZC_Alerta::add("erro", "Login ou Senha inválido.");
                $this->_redirect("/{$this->_data['module']}/auth/login/");
            endif;
        endif;
    }

    function logoutAction() {
        ZC_Auth::getInstance()->clearIdentity();
        $this->_redirect("/{$this->_data['module']}/auth/login");
    }

}