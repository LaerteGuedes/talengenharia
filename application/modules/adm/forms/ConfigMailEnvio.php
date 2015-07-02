<?

class Adm_Form_ConfigMailEnvio extends ZC_Form{
    
    public function init(){
        $this->eHidden('ID');
        $this->eText('EMAIL_ENVIO', 'Email de envio', true);
        $this->ePassword('EMAIL_ENVIO_SENHA', 'Senha do email', true);
        $this->eText('SSL', 'SSL', true);
        $this->eText('SSL_PORTA', 'Porta (SSL)', true);
        $this->eSubmit('ENVIAR');
        
        $this->addDisplayGroup(array('ID','EMAIL_ENVIO', 'EMAIL_ENVIO_SENHA', 'SSL', 'SSL_PORTA', 'ENVIAR'), 'groupdados', array('legend' => 'Dados'));
        $this->cSetDecoratorTable();
    }
    
}