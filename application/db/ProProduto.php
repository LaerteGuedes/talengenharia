<?php

class RowProProduto extends Zend_Db_Table_Row_Abstract{

    CONST PRODUTO = 1;
    CONST PRODUTO_DESTAQUE = 7;
    CONST COMBINACAO = 8;
    CONST COMBINACAO_DESTAQUE = 9;

    public function getArquivo() {
        $dbArqImagem = new Db_ArqArquivo();
        return $dbArqImagem->fetchByPagina($this->ID, 'pro_produto');
    }

    public function linkImagemDestaque($size = null) {
        $nome = ($size) ? $this->IMG_DESTAQUE . '-' . $size . '.' . $this->IMG_EXT : $this->IMG_DESTAQUE . '.' . $this->IMG_EXT;
        if ($this->IMG_DATA) {
            $data = new ZC_Date();
            $ano = $data->render($this->IMG_DATA, "yyyy");
            $mes = $data->render($this->IMG_DATA, "MM");
            return '/upload/arq_arquivo/' . $ano . '/' . $mes . "/" . $nome;
        } else {
            return '/upload/arq_arquivo/' . $nome;
        }
    }

    public function getImgDestaque($size = null, $bImagemSempre = false)
    {
        $dbArqImagem = new Db_ArqArquivo();
        $o = $dbArqImagem->fetchRow(array('ID_PAGINA = ?' => $this->ID, 'TABELA = ?' => 'pro_produto', 'DESTAQUE = ?' => 1));
        if ($o) {
            $oImg = $o->getImagem();
            if ($oImg) {
                return $oImg->getImagem($size);
            }
        }
        //caso nao exita a imagem;

        if ($bImagemSempre) {
            $buImg = new Business_Imagem(1);
            return $buImg->getImagem($size);
        } else {
            return false;
        }
    }
}

class Db_ProProduto extends ZC_Db_Table_Abstract{

    protected $_name = 'pro_produto';
    protected $_nome_log = 'Produto';
    protected $_rowClass = 'RowProProduto';

    public function padrao(Zend_Db_Select $s){
        $s->setIntegrityCheck(false);
        $s->joinLeft(array('img_destaque' => 'arq_arquivo'), "img_destaque.ID_PAGINA = pro_produto.ID and img_destaque.DESTAQUE = 1 and TABELA = 'pro_produto'", array('IMG_DESTAQUE' => 'img_destaque.ID', "IMG_EXT" => "img_destaque.EXT", "IMG_DATA" => "img_destaque.DATA"));
        $s->order(array('FL_DESTAQUE desc', '(case when FL_DESTAQUE_ORDEM = 0 then 9999 when FL_DESTAQUE_ORDEM = null then 9999 else FL_DESTAQUE_ORDEM end) ASC'));

        return $s;
    }

}

?>