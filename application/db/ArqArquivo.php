<?php

class RowArqArquivo extends Zend_Db_Table_Row_Abstract {

    public function init() {
        
    }

    function getImagem() {
        $bImagem = new Business_Imagem($this->ID);
        return $bImagem;
    }

    function renderImagem($vSize) {
        $bImagem = new Business_Imagem($this->ID);
        return $bImagem->getImagem($vSize);
    }

    function getUrl($size = null, $crop = false) {
        $nome = ($size) ? $this->ID . '-' . $size . '.' . $this->EXT : $this->ID . '.' . $this->EXT;
        if ($this->DATA) {
            $data = new ZC_Date();
            $ano = $data->render($this->DATA, "yyyy");
            $mes = $data->render($this->DATA, "MM");
            return '/upload/arq_arquivo/' . $ano . '/' . $mes . "/" . $nome;
        } else {
            return '/upload/arq_arquivo/' . $nome;
        }
    }

}

class Db_ArqArquivo extends ZC_Db_Table_Abstract {

    protected $_name = 'arq_arquivo';
    protected $_primary = 'ID';
    //protected $_nome_log = '';
    protected $_rowClass = 'RowArqArquivo';

    public function fetchByPagina($nIdPagina, $tabela) {
        return $this->fetchAll(array('ID_PAGINA = ?' => $nIdPagina, 'TABELA = ?' => $tabela), "ORDEM");
    }

    public function removeDestaque($nIdPagina, $tabela) {
        return $this->update(array('DESTAQUE' => 0), array('ID_PAGINA = ?' => $nIdPagina, 'TABELA = ?' => $tabela));
    }

    public function save($data) {
        if (!$data['ID'] && !count($this->fetchByPagina($data['ID_PAGINA'], $data['TABELA']))):
            $data['DESTAQUE'] = 1;
        endif;
        return parent::save($data);
    }

    public function delete($where) {
        $vo = $this->fetchAll($where);
        if (count($vo)) {
            foreach ($vo as $o) {
                $arq = $o->ID . '.' . $o->EXT;
                if (is_file($_SERVER['DOCUMENT_ROOT'] . '/upload/arq_arquivo/' . $arq)) {
                    unlink($_SERVER['DOCUMENT_ROOT'] . '/upload/arq_arquivo/' . $arq);
                    array_map("unlink", glob($_SERVER['DOCUMENT_ROOT'] . '/upload/arq_arquivo/' . $o->ID . '-*' . $o->EXT));
                }
                if (is_file($_SERVER['DOCUMENT_ROOT'] . '/upload/arq_arquivo/' . date('Y') . '/' . date('m') . "/" . $arq)) {
                    unlink($_SERVER['DOCUMENT_ROOT'] . '/upload/arq_arquivo/' . date('Y') . '/' . date('m') . "/" . $arq);
                    array_map("unlink", glob($_SERVER['DOCUMENT_ROOT'] . '/upload/arq_arquivo/' . date('Y') . '/' . date('m') . "/" . $o->ID . '-*' . $o->EXT));
                }
            }
        }

        return parent::delete($where);
    }

}
