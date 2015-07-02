<?php

class Adm_IndexController extends ZC_Controller_Action{

    public function indexAction(){
		$bAuth = new ZC_Auth();
		if(!$bAuth->getIdentity()):
			$this->_redirect('/adm/auth/login/');
		endif;
    }
}