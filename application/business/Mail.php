<?php

class Business_Mail extends Zend_Mail {

    function __construct() {
        parent::__construct('UTF-8');
        $this->view = Zend_Registry::get('view');
    }

    protected function getMail() {
        return $this;
    }

    public function sendContato($data, $custom = true) {
        $data['MENSAGEM'] = nl2br($data['MENSAGEM']);
        $this->setFrom($data['EMAIL'], $data['NOME']);
        $this->addTo(Zend_Registry::get('oConfiguracao')->EMAIL);
        $this->setSubject('Cachaça de Jambu - Fale Conosco');
        ($custom) ? $this->setBodyHtml($this->view->partial('/_includes/mail_faleconosco_custom.phtml', $data)) : 
            $this->setBodyHtml($this->view->partial('/_includes/mail_faleconosco.phtml', $data));
        return $this->send();
    }

    public function sendSiteError($mensagem) {
        $oMail = new Zend_Mail('UTF-8');
        $oMail->addTo('laerte@libradesign.com.br', 'Laerte');
        $oMail->setSubject("Erro - {$_SERVER['SERVER_NAME']}");
        $oMail->setBodyHtml($mensagem);
        $oMail->send();
    }

    public function sendSenha($oUsuario) {
        $this->setFrom($oUsuario->LOGIN, Zend_Registry::get('oConfiguracao')->TITULO);
        $this->addTo($oUsuario->EMAIL, $oUsuario->NOME);
        $this->setSubject(Zend_Registry::get('oConfiguracao')->TITULO . ' - Link Para Recuperar a Senha');
        $dbSenha = new Db_AclSenha();
        $dbSenha->inserePass($oUsuario);
        $oSenha = $dbSenha->fetchAll(array(), 'ID DESC')->current();
        $this->setBodyHtml($this->view->partial('/_includes/mail_recupera.phtml', array('oUsuario' => $oUsuario, "oSenha" => $oSenha)));
        return $this->send();
    }
    
    public function preVisualizar($data){
        $this->setBodyHtml($this->view->partial('/_includes/mail_faleconosco_custom.phtml', $data));
        self::showMailContent($this, false);
    }
    
    private static function showMailContent(Business_Mail $mail, $exit = true){
        $body = $mail->getBodyHtml();
        $content = $body->getRawContent();
        echo $content;
        if ($exit){
            die;
        }
    }

}