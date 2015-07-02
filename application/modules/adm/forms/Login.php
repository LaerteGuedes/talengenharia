<?php
//http://framework.zend.com/manual/en/zend.form.html
class Adm_Form_Login extends ZC_Form{
    public function init(){
        $this->addAttribs(array('id'=>'formLogin'));
		$this->setAction("/adm/auth/login/");
		
        $this->addElement('text', 'LOGIN')
        	->setLabel('Login:')
        	->setRequired(true)
			->setAttrib('maxlength',50)
			->setAttrib('size',20)
			->setFilters(array('StringTrim'))
			->setAttrib('class','validate[required]');

        $this->addElement('password', 'SENHA')
        	->setLabel('Senha:')
			->setDescription('<br /><a href="/adm/acl/recuperasenha/">Esqueceu a sua senha?</a>')
        	->setRequired(true)
			->setAttrib('maxlength',50)
			->setAttrib('size',20)
			->setFilters(array('StringTrim'))
			->setAttrib('class','validate[required]');

		$this->addElement('button', 'ENVIAR')
			->setLabel('Enviar')
			->setAttrib('type','submit')
			->setAttrib('ignore','true');

		$this->addDisplayGroup( array('LOGIN','SENHA','ENVIAR'),'groupDados',array('legend' => 'Dados de acesso'));

		$this->cSetDecoratorTable();
		$this->getElement('ENVIAR')->setDecorators($this->dcSubmit);
    }
}
