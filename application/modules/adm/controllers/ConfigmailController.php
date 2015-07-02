<?php

class Adm_ConfigmailController extends ZC_Controller_Action{
      
    public function indexAction(){
        $bHead = new Business_Head();
        $this->view->bMail = new Business_Mail();
        $bHead->jsColor();
        $this->_data['ID'] = 1;
        $form = new Adm_Form_SiteConfMail;
        $this->view->form = $form;
        $this->inserealterapagina($form, new Db_ConfigMail(), '/adm/configmail/index', true, '', false, false);
    }
}
