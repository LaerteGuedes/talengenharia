<?php

class Plugin_Inicializacao extends Zend_Controller_Plugin_Abstract {

    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {

        $this->_data = $this->_request->getParams();

        $this->onInitProject();

        $this->setPageHistory();

        if ($this->_data['module'] == "adm") {
            $this->verificaPermissao();
        }

        $this->iniConfig();
        $this->iniConfigMail();

        ZC_Db_Filtro::init();
    }

    protected function onInitProject() {
        $conf = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', 'libra');
        if ($conf->resources->db->params->dbname == 'modelo2.1' && !preg_match('/^(modelo2\-1\.)[a-zA-Z0-9-\.]+$/', $_SERVER['SERVER_NAME'])) {
            echo 'Alterar database no config antes de iniciar o projeto.';
            exit;
        }
    }

    protected function setPageHistory() {
        //##### REGISTRA HISTORICO PARA PAGINA ERROS#####
        $s = new Zend_Session_Namespace('ultima_pagina');
        $url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        $s->uri = (count($s->uri)) ? $s->uri : array();
        if (!preg_match("/^.*\.(php|js|ico|gif|jpeg|jpg|JPG|png|css|html)$/i", $url)) {
            $s->uri[3] = $s->uri[2];
            $s->uri[2] = $s->uri[1];
            $s->uri[1] = $s->uri[0];
            $s->uri[0] = $url;
        }
    }

    protected function verificaPermissao() {


        $bPermissao = new ZC_Acl();
        $bPermissao->verifica(true);
        $bAuth = new ZC_Auth();
        Zend_Registry::set('oUsuario', $bAuth->getIdentity());
    }

    protected function iniConfig() {
        $bCache = new Business_Cache();
        $urlImg = $oConf = (object) $bCache->get('config');
                    
        if ($oConf->ID) {
            if ($oConf->MANUTENCAO == 1) {
                if ($this->_data['action'] <> "manutencao" && $this->_data['module'] <> "adm") {
                    $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
                    $redirector->gotoUrl('/index/manutencao');
                }
            }
           
        } else {
            $oConf = array();
            $urlImg = array();
        }
        Zend_Registry::set('oConfiguracao', $oConf);
        Zend_Registry::set('urlImg', $urlImg);
        Zend_Registry::set('SITE_NAME', 'Site');
    }
    
    protected function iniConfigMail(){
        $dbConfMail = new Db_ConfigMail();
        $dbArquivo = new Db_ArqArquivo();
        
        $oConfMail = $dbConfMail->fetchRow(array('ID = ?' => 1));
        $oArquivoTopo = $oConfMail->getDestaque(array('config_mail_topo'));
        $oArquivoRodape = $oConfMail->getDestaque(array('config_mail_rodape'));
        $vConfMail = $oConfMail->toArray();
        
        if ($oArquivoTopo){
            $urlTopo = $oArquivoTopo->getUrl();
            $vConfMail['MAIL_TOPO'] = $urlTopo ? $urlTopo : ''; 
        }
        
        if ($oArquivoRodape){
            $urlRodape = $oArquivoRodape->getUrl();
            $vConfMail['MAIL_RODAPE'] = $urlRodape ? $urlRodape : ''; 
        }
        $vConfMail['COR_EMAIL_1'] = $oConfMail->COR_EMAIL_1;
        $vConfMail['COR_EMAIL_2'] = $oConfMail->COR_EMAIL_2;
        $vConfMail['EMAIL'] = $oConfMail->EMAIL;
        $vConfMail['TELEFONE'] = $oConfMail->TELEFONE;
               
        Zend_Registry::set('oConfiguracaoMail', $vConfMail);
    }

}
