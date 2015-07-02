<?php
class Adm_Form_FiltroLog extends ZC_Form{
    public function init(){
        $this->addAttribs(array('id'=>'formAclLog'));
		$this->eData('DATA_INICIO', 'Data Inicial:')->setBelongsTo('FILTRO');
		$this->eData('DATA_FIM', 'Data Final:')->setBelongsTo('FILTRO');
       	$this->eSelect('USUARIO', 'Usuário:', false, $this->getUsuario())->setBelongsTo('FILTRO');
       	$this->eSubmit('ENVIAR', 'Buscar');

		$this->addDisplayGroup(array('DATA_INICIO','DATA_FIM','USUARIO','ENVIAR'),'groupDados',array('legend' => 'Filtro'));
		$this->cSetDecoratorTable();
		
    }
    
	protected function getUsuario(){
		$dbUsuario = new Db_UsuUsuario();
		$v[''] = 'Selecione';
		foreach ($dbUsuario->fetchAll() as $o):
			$v[$o->NOME] = $o->NOME;
		endforeach;
		return $v;
	}
}
