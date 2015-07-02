<?php

class Adm_Form_ProProduto extends ZC_Form {

    public function populate(array $values) {
        if ($this->getElement('UPLOAD')) {
            $this->getElement('UPLOAD')->setParam('ID', $values['ID']);
        }
        return parent::populate($values);
    }

    public function init() {
        $optionsCategoria = $this->getOptionsCategoria();
        $optionsCapacidade = $this->getOptionsCapacidade();


        $this->addAttribs(array('id' => 'formProProduto'));
        $this->eHidden('ID');
        $this->eSelect('ID_CATEGORIA', 'Categoria: ', true, $optionsCategoria);
        $this->eText('NOME', 'Nome:', true, 250, 80);
        $this->eText('SUBTITULO', 'Subtítulo:', false, 250  , 80);
        $this->eText('LINK_MERCADO_PAGO', 'Link do Mercado Pago:', true);
        $this->eSelect('CAPACIDADE', 'Capacidade da garrafa (ml):', false, $optionsCapacidade);
        $this->eText('ETIQUETA', 'Etiqueta:', true, 100, 80);
        $this->eTextarea('TEXTO', 'Texto:', true);
        $this->eTextarea('DESCRICAO', 'Descricao:', false);
        $this->eText('PRECO', 'Preço: ', true);

        $this->addElement('Upload', 'UPLOAD')
            ->setPasta('/upload/arq_arquivo/')
            ->setAction('/adm/auxiliar/jupload/')
            ->setParam('TABELA', 'pro_produto')
            ->setCallback('loadImagem();')
            ->setMulti(true)
            ->debug(true)
            ->setLabel('Imagem:');

        $this->eSubmit('ENVIAR', 'Enviar')->setAttrib('class', 'submit');
        $this->addDisplayGroup(array('ID', 'NOME', 'SUBTITULO', 'LINK_MERCADO_PAGO', 'CAPACIDADE', 'ETIQUETA', 'ID_CATEGORIA', 'TEXTO', "DESCRICAO", "PRECO", 'FRETE', 'UPLOAD', 'ENVIAR'), 'groupDados', array('legend' => 'Dados'));
        $this->cSetDecoratorTable();
    }

    private function getOptionsCapacidade(){
        $options = array(0 => 'Selecione...', '275ml' => '275ml', '700ml' => '700ml');
        return $options;
    }

    private function getOptionsCategoria(){
        $dbCategoria = new Db_PagCategoria;
        $vCategoria = array(0 => 'Selecione...');
        $voCategoria = $dbCategoria->fetchAll();

        foreach ($voCategoria as $key => $oCategoria){
            $vCategoria[$oCategoria->ID] = $oCategoria->NOME;
        }

        return $vCategoria;
    }

}