<?php
class Adm_Form_AclGrupo extends ZC_Form{

	public function populate(array $values) {
		if($values['ID']):
			$this->getElement('ENVIAR')->setLabel('Alterar');
		endif;
		parent::populate($values);
	}
	
    public function init(){
        $this->addAttribs(array('id'=>'formAclGrupo'));
        $this->eHidden('ID');
		$this->eText('NOME', 'Nome:', true, 20, 20);
        $this->eSubmit('ENVIAR', 'Cadastrar:');

		$this->addDisplayGroup( array( 'NOME','ENVIAR'),'groupDados',array('legend' => 'Dados'));

		$this->cSetDecoratorTable();
    }
}
