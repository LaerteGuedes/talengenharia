<?php

class RowPagPagina extends Zend_Db_Table_Row_Abstract {

    public function init() {
        
    }

    public function getTipo($tipo = 'menu') {
        if ($tipo == 'destaque') {

            switch ($this->ID_CATEGORIA) {
                case 1: return "Superior";
                    break;
                case 2: return "Direito";
                    break;
            }
        } elseif ($tipo == 'menu') {
            switch ($this->ID_CATEGORIA) {
                case 1: return "Institucional";
                    break;
                case 2: return "Link";
                    break;
                case 3: return "Submenu";
                    break;
            }
        }
    }

    public function getFilho($ordem = 'ID DESC') {
        $db = new Db_PagPagina();
        $vo = $db->disableFiltro()->fetchAll(array('pag_pagina.ID_PAI = ?' => $this->ID), "$ordem");
        if (count($vo)):
            return $vo;
        else:
            return false;
        endif;
    }

    public function getVideo($vSize = null) {
        if ($this->YOUTUBE) {
            return ZC_Youtube::getEmbed($this->YOUTUBE, $vSize);
        }
    }

    public function getLink($controller = 'pagina') {
        $vTipoLink = array(
            'noticia' => '/noticia/{PERMALINK}',
            'institucional' => '/pagina/institucional/{PERMALINK}',
            'agenda' => '/agenda/{PERMALINK}'
        );

        if ($this->LINK) {
            $link = $this->LINK;
        } elseif (array_key_exists($this->TIPO, $vTipoLink)) {
            $link = $vTipoLink[$this->TIPO];
        } else {
            $link = "/{$controller}/{$this->TIPO}detalhe/ID/{ID}";
        }
        $link = str_replace('{ID}', $this->ID, $link);
        $link = str_replace('{PERMALINK}', $this->PERMALINK, $link);

        return $link;
    }

    public function getArquivo() {
        $dbArqImagem = new Db_ArqArquivo();
        return $dbArqImagem->fetchByPagina($this->ID, 'pag_pagina');
    }

    public function getArquivoDestaque() {
        $dbArqArquivo = new Db_ArqArquivo();
        return $dbArqArquivo->fetchRow(array('DESTAQUE = ?' => 1, 'arq_arquivo.ID_PAGINA = ?' => $this->ID));
    }

    public function getDestaque() {
        $dbArqImagem = new Db_ArqArquivo();
        $o = $dbArqImagem->fetchRow(array('ID_PAGINA = ?' => $this->ID, 'TABELA = ?' => 'pag_pagina', 'DESTAQUE = ?' => 1));
        return ($o) ? $o->getImagem() : false;
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

    public function getImgDestaque($size = null, $bImagemSempre = false) {
        $dbArqImagem = new Db_ArqArquivo();
        $o = $dbArqImagem->fetchRow(array('ID_PAGINA = ?' => $this->ID, 'TABELA = ?' => 'pag_pagina', 'DESTAQUE = ?' => 1));
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

    public function getStatus() {
        $zData = new ZC_Date();
        $data1 = new Zend_Date($this->DATA_INI);
        $data2 = new Zend_Date(date("Y-m-d H:i:s"));

        if ($data1->getTimestamp() <= $data2->getTimestamp()) {
            return "<a style='color:green'>Publicado no dia " . $this->getDataF() . "</a>";
        } else {
            return "<a style='color:red'>Agendado para o dia " . $this->getDataF() . "</a>";
        }
    }

    public function getCategoria() {
        return $this->ID_CATEGORIA == 1 ? "Artigo" : "Notícia";
    }

    public function getDataF($formato = "dd 'de' MMMM, YYYY - HH'h'mm") {
        $data = new ZC_Date();
        return $data->getFormatado($this->DATA_INI, $formato);
    }

    public function getComentario($limit = null, $st = array(1)) {
        $dbComentario = new Db_PagComentario();
        return $dbComentario->fetchByPagina($this->ID, $limit, $st);
    }

    public function getComentarioCount(array $st = array(1)) {
        $dbComentario = new Db_PagComentario();
        return count($dbComentario->fetchByPagina($this->ID, false, $st)->toArray());
    }

    public function getComentarioCountAprovar() {
        $dbComentario = new Db_PagComentario();
        return count($dbComentario->fetchByPagina($this->ID, null, 0)->toArray());
    }

}

class Db_PagPagina extends ZC_Db_Table_Abstract {

    protected $_name = 'pag_pagina';
    protected $_nome_log = 'Pagina';
    protected $_rowClass = 'RowPagPagina';

    public function padrao(Zend_Db_Select $s) {
        $s->setIntegrityCheck(false);
        $s->joinLeft(array('img_destaque' => 'arq_arquivo'), "img_destaque.ID_PAGINA = pag_pagina.ID and img_destaque.DESTAQUE = 1 and TABELA = 'pag_pagina'", array('IMG_DESTAQUE' => 'img_destaque.ID', "IMG_EXT" => "img_destaque.EXT", "IMG_DATA" => "img_destaque.DATA"));
        $s->joinLeft('pag_categoria', "pag_categoria.ID = pag_pagina.ID_CATEGORIA", array('CATEGORIA_NOME' => 'pag_categoria.NOME', 'CATEGORIA_ORDEM' => 'pag_categoria.ORDEM', 'CATEGORIA_ID_PAI' => 'pag_categoria.ID_PAI'));
        $s->where('pag_pagina.ATIVO = ?', 1);
        $s->order(array('FL_DESTAQUE desc', '(case when FL_DESTAQUE_ORDEM = 0 then 9999 when FL_DESTAQUE_ORDEM = null then 9999 else FL_DESTAQUE_ORDEM end) ASC', 'DATA_INI desc'));
        $data = new ZC_Date();
        $data = $data->toString('yyyy-MM-dd HH:mm:ss');
        $s->where("pag_pagina.DATA_INI <= '{$data}' or pag_pagina.DATA_INI is null");
        if (!count($s->getPart(Zend_Db_Select::ORDER))) {
            $s->order('pag_pagina.DATA_INI DESC');
        }
        
        return $s;
    }
    
       
    public function getPublicacoesByIdPai($idPai, $tipo){
        $this->disablePadrao();
        $s = $this->select()->setIntegrityCheck(false)->from('pag_pagina', array('ID', 'TITULO', 'CHAPEU', 'LINK', 'TIPO', 'PERMALINK'));
        $s->setIntegrityCheck(false);
        $s->joinLeft(array('img_destaque' => 'arq_arquivo'), "img_destaque.ID_PAGINA = pag_pagina.ID and img_destaque.DESTAQUE = 1 and TABELA = 'pag_pagina'", array('IMG_DESTAQUE' => 'img_destaque.ID', "IMG_EXT" => "img_destaque.EXT", "IMG_DATA" => "img_destaque.DATA"));
        $s->joinLeft('pag_categoria', "pag_categoria.ID_PAI = {$idPai}", array('CATEGORIA_NOME' => 'pag_categoria.NOME', 'CATEGORIA_ORDEM' => 'pag_categoria.ORDEM'));
        $s->where('pag_pagina.ATIVO = ?', 1);
        $s->where('pag_pagina.TIPO = ?', 'publicacoes');
        $s->order(array('FL_DESTAQUE desc', '(case when FL_DESTAQUE_ORDEM = 0 then 9999 when FL_DESTAQUE_ORDEM = null then 9999 else FL_DESTAQUE_ORDEM end) ASC', 'DATA_INI desc'));
        $data = new ZC_Date();
        $data = $data->toString('yyyy-MM-dd HH:mm:ss');
        $s->where("pag_pagina.DATA_INI <= '{$data}' or pag_pagina.DATA_INI is null");
        if (!count($s->getPart(Zend_Db_Select::ORDER))) {
            $s->order('pag_pagina.DATA_INI DESC');
        }
        
        return parent::fetchAll($s);
    }

    public function fetchMaisAcessadas($qnt = 5, $nIdCategoria = null) {
        $select = $this->select()->setIntegrityCheck(false)->from('pag_pagina', array('ID', 'TITULO', 'CHAPEU', 'LINK', 'TIPO', 'PERMALINK'));
        $select->joinInner(array('l' => 'log_acesso'), 'l.ID_PAGINA = pag_pagina.ID', null);
        $select->group(array('pag_pagina.ID', 'pag_pagina.TITULO', 'pag_pagina.CHAPEU', 'pag_pagina.LINK', 'pag_pagina.TIPO', 'pag_pagina.PERMALINK', 'img_destaque.ID', 'pag_categoria.ORDEM', 'pag_categoria.NOME'));
        $select->limit($qnt);
        if (!in_array($nIdCategoria, array('093', '077'))) {
            $select->where('pag_pagina.TIPO = ?', 'noticia');
        }
        $select->where('l.DATA >= ?', date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d') - 2, date('Y'))));
        if ($nIdCategoria) {
            $select->where("pag_categoria.ORDEM LIKE '{$nIdCategoria}%'");
        }
        $select->order('SUM(l.QNT) DESC');
        $vo = $this->fetchAll($select);
        return $vo;
    }

    public function filtro(Zend_Db_Select $s) {
        $vFiltro = ZC_Db_Filtro::get();


        if (Zend_Registry::isRegistered('PagPaginaDistinct')) {
            $this->_vDistinctId = Zend_Registry::get('PagPaginaDistinct');
        }
        if ($this->_isDistinct && count($this->_vDistinctId)) {
            $s->where('pag_pagina.ID NOT IN(?)', array($this->_vDistinctId));
        }

        if ($vFiltro['ID_CATEGORIA_NOT']) {
            $s->where('pag_pagina.ID_CATEGORIA NOT IN(?)', $vFiltro['ID_CATEGORIA_NOT']);
        }
        if ($vFiltro['ID_CATEGORIA']) {
            $s->where('pag_pagina.ID_CATEGORIA IN(?)', $vFiltro['ID_CATEGORIA']);
        }
        if ($vFiltro['ID_CATEGORIA_FULL']) {
            $s->where("pag_categoria.ORDEM LIKE('{$vFiltro['ID_CATEGORIA_FULL']}%')");
        }
        
        if ($vFiltro['TIPO']) {
            $s->where('pag_pagina.TIPO = ?', $vFiltro['TIPO']);
            $s->order('id DESC');
        }
        if (isset($vFiltro['ID_PAI'])) {
            $s->where('pag_pagina.ID_PAI = ?', $vFiltro['ID_PAI']);
        }
        if (isset($vFiltro['ANO'])) {
            $s->where('YEAR(pag_pagina.DATA_INI) = ?', $vFiltro['ANO']);
        }
       
        if ($vFiltro['BUSCA']) {
            $vBusca = array(
                "TITULO LIKE '%{$vFiltro['BUSCA']}%'",
                "CHAPEU LIKE '%{$vFiltro['BUSCA']}%'",
                "RESUMO LIKE '%{$vFiltro['BUSCA']}%'",
                "TEXTO LIKE '%{$vFiltro['BUSCA']}%'",
            );
            $s->where(implode(' OR ', $vBusca));
        }
        return $s;
    }

    public function getPaginasByTag($tag) {
        $select = $this->select()->setIntegrityCheck(false)
                ->from(array("pag_pagina"))
                ->join(array('not_tag' => 'pag_noticia_tag'), "pag_pagina.ID = not_tag.ID_PAGINA", "pag_pagina.ID")
                ->join(array('tag' => 'tag_tag'), "tag.ID = not_tag.ID_TAG", 'tag.NOME')
                ->where("tag.NOME = ?", $tag);
        return $this->fetchAll($select);
    }

    public function save($data) {

        if (isset($data['FL_DESTAQUE_ORDEM']) && !$data['FL_DESTAQUE_ORDEM']):
            $data['FL_DESTAQUE_ORDEM'] = 999;
        endif;
        if (isset($data['ID_CATEGORIA']) && !$data['ID_CATEGORIA']):
            $data['ID_CATEGORIA'] = NUll;
        endif;
        if (isset($data['LINK']) && !$data['LINK']):
            $data['LINK'] = NUll;
        endif;
        if (isset($data['TEXTO'])) {
            $data['TEXTO'] = stripslashes($data['TEXTO']);
        }
        if (isset($data['TITULO'])) {
            $data['TITULO'] = stripslashes($data['TITULO']);
        }
        if (isset($data['RESUMO'])) {
            $data['RESUMO'] = stripslashes($data['RESUMO']);
        }
        if (!$data['AUTOR']) {
            $data['AUTOR'] = NULL;
        }

        $zDate = new ZC_Date();

        if ($data['DATA_INI'] == "") {
            $data['DATA_INI'] = date("Y-m-d H:i:s");
        } else {
            $data['DATA_INI'] = $zDate->toDb($zDate->render($data['DATA_INI']));
        }
        if (!$data['DATA_FIM']) {
            $data['DATA_FIM'] = null;
        } else {
            $data['DATA_FIM'] = $zDate->toDb($data['DATA_FIM']);
        }
        $permalink = ZC_Util::verificaPermalink($this, $data["TITULO"], $data['ID']);
        if ($permalink["isValid"]) {
            $data["PERMALINK"] = $permalink["permalink"];
        }

        return parent::save($data);
    }

    public function getAdmNoticia($page = 1) {
        $s = $this->select()->from(array($this->_name));
        $s->setIntegrityCheck(false);
        $s->joinLeft(array('img_destaque' => 'arq_arquivo'), "img_destaque.ID_PAGINA = pag_pagina.ID and img_destaque.DESTAQUE = 1 and TABELA = 'pag_pagina'", array('IMG_DESTAQUE' => 'img_destaque.ID'));
        $s->joinLeft('pag_categoria', "pag_categoria.ID = ID_CATEGORIA", array('CATEGORIA_NOME' => 'pag_categoria.NOME', 'CATEGORIA_ORDEM' => 'pag_categoria.ORDEM'));
        $s->where('pag_pagina.ATIVO in (?)', array(1, 2));
        $s->order(array('FL_DESTAQUE desc', '(case when FL_DESTAQUE_ORDEM = 0 then 9999 when FL_DESTAQUE_ORDEM = null then 9999 else FL_DESTAQUE_ORDEM end) ASC'));
        $vo = $this->disablePadrao()->fetchAll($s)->paginator();
        return $vo;
    }
    public function getAdmAgenda($page = 1) {
        $s = $this->select()->from(array($this->_name));
        $s->setIntegrityCheck(false);
        $s->joinLeft(array('img_destaque' => 'arq_arquivo'), "img_destaque.ID_PAGINA = pag_pagina.ID and img_destaque.DESTAQUE = 1 and TABELA = 'pag_pagina'", array('IMG_DESTAQUE' => 'img_destaque.ID'));
        $s->joinLeft('pag_categoria', "pag_categoria.ID = ID_CATEGORIA", array('CATEGORIA_NOME' => 'pag_categoria.NOME', 'CATEGORIA_ORDEM' => 'pag_categoria.ORDEM'));
        $s->where('pag_pagina.ATIVO in (?)', array(1, 2));
        $s->order(array('FL_DESTAQUE DESC', 'FL_DESTAQUE_ORDEM ASC', 'DATA_INI desc'));
        $vo = $this->disablePadrao()->fetchAll($s)->paginator();
        return $vo;
    }

    public function deletaFilhos($idPai) {
        $where = array("ID_PAI = ?" => $idPai);
        $vo = $this->fetchAll($where);
        $dbArquivo = new Db_ArqArquivo();
        foreach ($vo as $o) {
            $this->deletaFilhos($o->ID);
            $dbArquivo->delete(array('ID_PAGINA = ?' => $o->ID));
        }
        return parent::delete($where);
    }

    public function delete($where) {
        $vo = $this->fetchAll($where);
        $dbArquivo = new Db_ArqArquivo();
        foreach ($vo as $o) {
            $this->deletaFilhos($o->ID);
            $dbArquivo->delete(array('ID_PAGINA = ?' => $o->ID));
        }
        return parent::delete($where);
    }

}