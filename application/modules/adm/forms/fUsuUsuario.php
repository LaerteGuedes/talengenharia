<?php
class Adm_Form_fUsuUsuario extends ZC_Form{
    public function init(){
        $this->addAttribs(array('id'=>'fUsuario'));
        $this->addAttribs(array('class'=>'FormPadrao Filtro'));

		$this->eHidden('ID');

		$this->eText('NOME', 'Nome:', false, 200, 50)->setBelongsTo('FILTRO');
		$this->eText('LOGIN', 'Login:', false, 200, 20)->setBelongsTo('FILTRO');
		$this->eSelect('ID_GRUPO', 'Grupo de Usuário:',false,$this->getGrupo())->setBelongsTo('FILTRO');
		$this->eSubmit('ENVIAR', 'Enviar');


		$this->addDisplayGroup( array( 'NOME','CPF','LOGIN','SENHA','ID_GRUPO','ENVIAR'),'groupUsuario',array('legend' => 'Filtro'));
		$this->cSetDecoratorTable();

		
    }
   
	public function getGrupo(){
		$dbGrupo = new Db_AclGrupo();
		$v[''] = 'Selecione...';
		foreach($dbGrupo->fetchAll() as $o):
			$v[$o->ID] = $o->NOME;
		endforeach;
		return $v;
	}
}
