<?php

class Adm_Form_PagCategoria extends ZC_Form {

    public function populate(array $values) {

        return parent::populate($values);
    }

    public function init() {
        $this->addAttribs(array('id' => 'formPagPagina'));
        $this->eHidden('ID');
        $this->eHidden('ID_PAI');
        $this->eRadio('GRUPO', 'Tipo de Categoria:', true, array(1 => 'Categorias', 2 => 'Seção', 3 => 'Oculto'));
        $this->eText('NOME', 'Nome:', true, 150, 80);
        $this->eText('ID_ANTIGO', 'ID Antigo:', false, 100, 80);
        $this->eSubmit('ENVIAR', 'Enviar')->setAttrib('class', 'submit');
        $this->addDisplayGroup(array('ID', 'GRUPO', 'NOME', 'ID_ANTIGO', 'ENVIAR'), 'groupDados', array('legend' => 'Dados'));
        $this->cSetDecoratorTable();
    }

}
