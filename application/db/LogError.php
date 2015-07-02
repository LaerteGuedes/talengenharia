<?php

class RowLogError extends Zend_Db_Table_Row_Abstract {

    public function init() {
        
    }

}

class Db_LogError extends ZC_Db_Table_Abstract {

    protected $_name = 'log_error';
    //protected $_nome_log = '';
    protected $_rowClass = 'RowLogError';

    public function save($data) {

        $select = $this->select();
        $select->where('TYPE = ?', $data['TYPE']);
        $select->where('MENSAGEM = ?', $data['MENSAGEM']);
        $select->where('RESPONSECODE = ?', $data['RESPONSECODE']);
        $select->where('URL = ?', $data['URL']);
        $select->where('TRACE = ?', $data['TRACE']);
        $oErro = $this->fetchRow($select);
        $bMail = new Business_Mail();
        if ($oErro):
            $data['QNT'] = $oErro->QNT + 1;
            $data['ID'] = $oErro->ID;
            if (($data['QNT'] % 10) == 0):
                $bMail->sendSiteError($this->getMensagem($data, true));
            endif;
        else:
            $bMail->sendSiteError($this->getMensagem($data));
        endif;
        parent::save($data);
    }

    protected function getMensagem($data, $persistente = false) {
        if ($persistente == true):
            $mensagem = '<h1>O erro abaixo persiste em ocorrer no site</h1>';
            $mensagem .= '<h2 style="color:red">Verifique o mais rápido possível</h2>';
        else:
            $mensagem = '<h1>Ocorreu um erro</h1>';
        endif;

        $mensagem .= '<h3>Informações sobre o erro</h3>';
        $mensagem .= '<strong>Histórico:</strong>' . $data['HISTORICO'] . '<br/>';
        $mensagem .= '<strong>Agent:</strong>' . $_SERVER['HTTP_USER_AGENT'] . '<br/>';
        $mensagem .= '<strong>Data:</strong>' . date("d/m/Y H:i") . '<br/>';
        $mensagem .= '<strong>Usuário:</strong>' . $data['USUARIO'] . '<br/>';
        $mensagem .= '<strong>Mensagem:</strong>' . $data['MENSAGEM'] . '<br/>';
        $mensagem .= '<strong>Endereço:</strong>' . $data['URL'] . '<br/>';
        $mensagem .= '<strong>Responsecode:</strong>' . $data['RESPONSECODE'] . '<br/>';
        $mensagem .= '<strong>Trace:</strong><pre>' . $data['TRACE'] . '</pre><br/>';
        return $mensagem;
    }

}
