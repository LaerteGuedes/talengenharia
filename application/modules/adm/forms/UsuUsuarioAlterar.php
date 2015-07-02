<?php
class Adm_Form_UsuUsuarioAlterar extends ZC_Form{
    public function init(){
        $this->addAttribs(array('id'=>'formUsuario'));
                       
        $this->addElement('hidden', 'ID')->setDecorators(array('ViewHelper'));
		$this->ePassword('SENHA_ATUAL', 'Senha Atual:', TRUE, 200, 20);
		$this->ePassword('SENHA', 'Nova Senha:', TRUE, 200, 20);
		$this->ePassword('SENHA2', 'Repita a senha:', TRUE, 200, 20);
		$this->eSubmit('ENVIAR', 'Enviar');

		$this->addDisplayGroup( array('SENHA_ATUAL', 'SENHA','SENHA2','ENVIAR'),'groupUsuario',array('legend' => 'Alterar Senha'));

		$this->cSetDecoratorTable();
    }
}
