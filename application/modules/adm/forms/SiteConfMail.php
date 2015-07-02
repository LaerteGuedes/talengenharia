<?php

class Adm_Form_SiteConfMail extends ZC_Form{
    
    public function populate(array $values){
        $this->getElement('UPLOAD')->setParam('ID', $values['ID']);
        $this->getElement('UPLOAD2')->setParam('ID', $values['ID']);
        return parent::populate($values);
    }
    
    public function init(){
        $this->addAttribs(array('ID' => 'formConfMail'));
        $this->eHidden('ID', 1);
        $this->eText('EMAIL', 'Email: ', true);
        $this->eTextarea('ENDERECO', 'Endereço: ', true);
        $this->eUpload('Upload', 'UPLOAD', 'config_mail_topo', 'loadImagem()', 'Imagem de topo do e-mail: ', false);
        $this->eUpload('Upload', 'UPLOAD2', 'config_mail_rodape', 'loadImagemRodape()', 'Marca do rodapé: ', false);
        $this->eText('TELEFONE', 'Telefone: ', true);
        $this->eText('COR_EMAIL_1', 'Cor do Corpo do texto: ', true)->setAttribs(array('ID' => 'COR_EMAIL_1', 'class' => 'color'));
                                                
        $this->eText('COR_EMAIL_2', 'Cor do rodapé:', true)->setAttribs(array('ID' => 'COR_EMAIL_1', 'class' => 'color'));
        $this->eSubmit('ENVIAR', 'Enviar');
               
        $this->addDisplayGroup(array('ID', 'EMAIL', 'ENDERECO', 'UPLOAD', "UPLOAD2", "TELEFONE", 'COR_EMAIL_1', "COR_EMAIL_2",'PRE-VISUALIZAR','ENVIAR'), 'groupDados', array('legend' => 'Dados'));
        $this->cSetDecoratorTable();
    }
}
