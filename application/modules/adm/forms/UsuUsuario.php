<?php
class Adm_Form_UsuUsuario extends ZC_Form{
    public function init(){
        $this->addAttribs(array('id'=>'formUsuario'));
                       
        $this->addElement('hidden', 'ID')->setDecorators(array('ViewHelper'));

		$this->eText('NOME', 'Nome:', TRUE, 200, 50);
		$this->eText('LOGIN', 'Login:', TRUE, 200, 20);
		$this->eText('EMAIL', 'E-mail:', TRUE, 250, 20)->setAttrib('class', 'validate[required,custom[email]]');
		$this->ePassword('SENHA', 'Senha:', TRUE, 200, 20);
		$this->ePassword('SENHA2', 'Repita a senha:', TRUE, 200, 20)->setAttrib('class', 'validate[required,equals[SENHA]]');
		$this->eSelect('ID_GRUPO', 'Grupo de Usuário:',TRUE,$this->getGrupo());
		$this->eSubmit('ENVIAR', 'Enviar');

		$this->addDisplayGroup( array( 'NOME','LOGIN','EMAIL','SENHA','SENHA2','ID_GRUPO','ENVIAR'),'groupUsuario',array('legend' => 'Dados do Usuário'));

		$this->cSetDecoratorTable();
    }
   
	public function getGrupo(){
		$dbGrupo = new Db_AclGrupo();
		$v[''] = 'Selecione...';
		Zend_Registry::get('oUsuario');
		foreach($dbGrupo->fetchAll() as $o):
			$v[$o->ID] = $o->NOME;
		endforeach;
		return $v;
	}
}
