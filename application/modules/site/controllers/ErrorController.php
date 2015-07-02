<?php

class ErrorController extends Zend_Controller_Action {//não colocar o ZC pois dá erro

    public $_data; //variavel com todos os parametros request

    public function init() {
        $this->_oUsuario = ZC_Auth::getInstance()->getIdentity();
        parent::init();
    }

    public function errorAction() {
        header("Content-Type: text/html; charset=utf-8");
        header("HTTP/1.0 404 Not Found");
        $this->_helper->layout()->disableLayout();
        $dbError = new Db_LogError();
        $errors = $this->_getParam('error_handler');

        $this->_data['TYPE'] = $errors->type;
        $this->_data['MENSAGEM'] = $errors->exception->getMessage();
        $this->_data['RESPONSECODE'] = $this->getResponse()->getHttpResponseCode();
        $this->_data['USUARIO'] = $this->_request->getParam('usuario') ? $this->_request->getParam('usuario') : $this->_oUsuario->LOGIN;
        $this->_data['USUARIO'] .= " ip({$_SERVER['REMOTE_ADDR']})";
        $this->_data['URL'] = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        $this->_data['TRACE'] = $errors->exception->getTraceAsString();
        $s = new Zend_Session_Namespace('ultima_pagina');
        $this->_data['HISTORICO'] = implode(' >> ', $s->uri);
        $isLocal = ZC_Util::isLocal();
        $isImagem = ZC_Util::isImage();
		$isBoot = ZC_Util::isBot();
		
        if(!$isImagem && !$isLocal && !$isBoot){
            $dbError->save($this->_data);
        }

        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:

                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'Página não encotrada';
                $this->view->error = 404;
                break;
            default:
                // application error 
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message = 'Erro de aplicação';
                $this->view->error = 500;
                break;
        }
        if ($isLocal) {

            if ($this->_data['msg']) {
                $data["exception"] = $this->view->exception = $this->_data['msg'];
                $data["request"] = $this->view->request = $this->_data["request"];
                $data["tipoErro"] = $this->view->tipoErro = 2;
            } elseif ($errors) {
                $data["exception"] = $this->view->exception = $errors->exception;
                $data["request"] = $this->view->request = $errors->request;
                $data["tipoErro"] = $this->view->tipoErro = 1;
            }
        }
    }

}

