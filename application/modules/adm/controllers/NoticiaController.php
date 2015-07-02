<?php

class Adm_NoticiaController extends ZC_Controller_Action {

    protected $_data;

    public function init() {
        $this->_dbPagina = new Db_PagPagina();
        parent::init();
        $this->view->data = $this->_data;
        $this->view->destaque = true;
        $this->view->removerDestaque = false;
        $this->view->noticiaSemana = false;
        $this->view->categoria = true;
    }

    ## GERÊNCIA DE NOTÍCIAS ##

    public function indexAction() {
        // $this->uploadmunicipios('AP');
        ZC_Db_Filtro::setParam('TIPO', 'noticia');

        if (isset($this->_data['ID_CATEGORIA'])) {
            $_SESSION['admpaginanoticiafiltro']['ID_CATEGORIA'] = $this->_data['ID_CATEGORIA'];
        }
        if ($this->_data['page']) {
            $_SESSION['admpaginanoticiafiltro']['page'] = $this->_data['page'];
        }
        if ($_SESSION['admpaginanoticiafiltro']) {
            ZC_Db_Filtro::setParam('ID_CATEGORIA_FULL', $_SESSION['admpaginanoticiafiltro']['ID_CATEGORIA']);
        }
        $dbCategoria = new Db_PagCategoria();
        $this->view->voCategoria = $dbCategoria->fetchAll(array("ID_PAI = ''"), 'NOME');
        $this->view->vo = $this->_dbPagina->getAdmNoticia($_SESSION['admpaginanoticiafiltro']['page']);
        $this->view->categoria = false;
        $this->view->TITULO = "Gerência de Notícias";
    }

    public function inserealteraAction() {
        $oHeader = new Business_Head();
        $oHeader->jqueryUi();
        $oHeader->jDateTimePiker();
        $oHeader->jCharCount();
        $oHeader->jTag();
        $oHeader->jMeioMask();
        $this->managerTags();


        $form = new Adm_Form_PagPaginaNot();
        $form->removeElements(array("CHAPEU", "YOUTUBE", "DATA_FIM", "LINK"));
        $this->inserealterapagina($form, $this->_dbPagina, '/adm/noticia', true, 'noticia', false, false);
    }

    ## GERÊNCIA DE AGENDA ##

    public function agendaAction() {
        ZC_Db_Filtro::setParam('TIPO', 'agenda');

        if (isset($this->_data['ID_CATEGORIA'])) {
            $_SESSION['admpaginanoticiafiltro']['ID_CATEGORIA'] = $this->_data['ID_CATEGORIA'];
        }
        if ($this->_data['page']) {
            $_SESSION['admpaginanoticiafiltro']['page'] = $this->_data['page'];
        }
        if ($_SESSION['admpaginanoticiafiltro']) {
            ZC_Db_Filtro::setParam('ID_CATEGORIA_FULL', $_SESSION['admpaginanoticiafiltro']['ID_CATEGORIA']);
        }
        $dbCategoria = new Db_PagCategoria();
        $this->view->voCategoria = $dbCategoria->fetchAll(array("ID_PAI IS NULL OR ID_PAI = '' "), 'ORDEM ASC');
        $this->view->vo = $this->_dbPagina->getAdmAgenda($_SESSION['admpaginanoticiafiltro']['page']);

        $this->view->TITULO = "Gerência de Agenda";
    }

    public function inserealteraagendaAction() {
        $oHeader = new Business_Head();
        $oHeader->jqueryUi();
        $oHeader->jDateTimePiker();
        $oHeader->jCharCount();
        $oHeader->jTag();
        $oHeader->jMeioMask();
        $this->managerTags();
        $this->inserealterapagina(new Adm_Form_PagPaginaNot(), $this->_dbPagina, '/adm/noticia/agenda', true, 'agenda', false, false);
        $this->render("inserealtera");
    }

    ## GERÊNCIA DE PUBLICACOES ##

    public function publicacoesAction() {
        $this->view->destaque = false;
        ZC_Db_Filtro::setParam('TIPO', 'publicacoes');

        if (isset($this->_data['ID_CATEGORIA'])) {
            $_SESSION['admpaginanoticiafiltro']['ID_CATEGORIA'] = $this->_data['ID_CATEGORIA'];
        }
        if ($this->_data['page']) {
            $_SESSION['admpaginanoticiafiltro']['page'] = $this->_data['page'];
        }
        if ($_SESSION['admpaginanoticiafiltro']) {
            ZC_Db_Filtro::setParam('ID_CATEGORIA_FULL', $_SESSION['admpaginanoticiafiltro']['ID_CATEGORIA']);
        }
        $dbCategoria = new Db_PagCategoria();
        $this->view->voCategoria = $dbCategoria->fetchAll(null, 'ORDEM ASC');
        $this->view->vo = $this->_dbPagina->getAdmNoticia($_SESSION['admpaginanoticiafiltro']['page']);

        $this->view->TITULO = "Gerência de Publicações";

        $this->render("index");
    }

    public function inserealterapublicacoesAction() {
        $oHeader = new Business_Head();
        $oHeader->jqueryUi();
        $oHeader->jDateTimePiker();
        $oHeader->jCharCount();
        $oHeader->jTag();
        $oHeader->jMeioMask();
        $this->managerTags();
        $form = new Adm_Form_PagPaginaPublicacao();
        $form->removeElements(array("ATIVO"));
        $this->inserealterapagina($form, $this->_dbPagina, '/adm/noticia/publicacoes', true, 'publicacoes', false, false);
        $this->render("inserealtera");
    }

    public function managerTags() {
        $this->view->TAGS = true;
        $dbTag = new Db_TagTag();

        if ($this->_request->isPost()) {
            $dbNotTag = new Db_PagNoticiaTag();
            $dbNotTag->delete(array('ID_PAGINA = ?' => $this->_data['ID']));

            if ($this->_data['hiddenTagListA']) {
                $vTag = explode(",", $this->_data['hiddenTagListA']);
                foreach ($vTag as $tag) {
                    $tag = stripslashes($tag);
                    $idTag[] = $dbTag->verificaTag($tag);
                }
                foreach ($idTag as $id) {
                    $dbNotTag->insert(array('ID_PAGINA' => $this->_data['ID'], 'ID_TAG' => $id));
                }
            }
        } else {
            if ($this->_data['ID']) {
                $dbEstTag = new Db_PagNoticiaTag();
                $voTags = $dbEstTag->getTagsById($this->_data['ID']);
                foreach ($dbTag->fetchAll() as $tags) {
                    $nameTags[] = $tags->NOME;
                }
                foreach ($voTags as $oTags):
                    if ($oTags->NOME <> ""):
                        $nameTagsSelect[] = $oTags->NOME;
                    endif;
                endforeach;
            }
            $this->view->tagSelected = json_encode($nameTagsSelect);
            $this->view->tags = json_encode($nameTags);
        }
    }

    public function comentarioAction() {
        $this->view->o = $this->_dbPagina->fetchRow("ID = {$this->_data["ID"]}");
    }

    public function adddestaqueordemAction() {
        ZC_Db_Filtro::setParam('TIPO', 'noticia');
        $oPagina = $this->_dbPagina->fetchRow(array("pag_pagina.ID = ?" => $this->_data['ID']));

        $valor = ($this->_data['VALOR'] == 0) ? null : $this->_data['VALOR'];
        $oSegPagina = ($valor) ? $this->_dbPagina->fetchRow(array("pag_pagina.FL_DESTAQUE_ORDEM = ?" => $valor)) : NULL;
        $valorAntigo = $oPagina->FL_DESTAQUE_ORDEM;

        $where = array(
            'FL_DESTAQUE_ORDEM' => $valor,
        );
        $whereAux = array(
            'FL_DESTAQUE_ORDEM' => $valorAntigo,
        );

        $this->_dbPagina->update($where, array('ID = ?' => $this->_data["ID"]));
        if ($oSegPagina) {
            $this->_dbPagina->update($whereAux, array('ID = ?' => $oSegPagina->ID));
        }

        $arr['valorAntigo'] = $valorAntigo;
        $arr['valor'] = $valor;
        $arrJson = json_encode($arr);

        echo $arrJson;
        exit;
    }

    public function adddestaqueAction() {
        $where = array(
            'FL_DESTAQUE' => $this->_data['S'],
            'FL_DESTAQUE_ORDEM' => ''
        );

        if ($this->_data['S'] == 0) {
            $where = array(
                'FL_DESTAQUE' => $this->_data['S'],
                'FL_DESTAQUE_ORDEM' => NULL
            );
        }

        $this->_dbPagina->update($where, array('ID = ?' => $this->_data["ID"]));

        $this->disableView();
        exit;
    }

    public function addsemanaAction() {
        $dados = explode("_", $this->_data['destaque']);
        $where = array(
            'FL_SEMANA' => $this->_data['S']
        );
        $this->_dbPagina->update($where, array('ID = ?' => $this->_data["ID"]));
        $this->disableView();
        exit;
    }

    public function addexcluiAction() {
        $dados = explode("_", $this->_data['destaque']);
        $where = array(
            'FL_EXCLUI' => $this->_data['S']
        );
        $this->_dbPagina->update($where, array('ID = ?' => $this->_data["ID"]));
        $this->disableView();
        exit;
    }

    #### GERAL ###################

    public function excluirAction() {
        $redirect = $this->_data['move'] ? '/adm/pagina/' . $this->_data['move'] : null;
        parent::excluir($this->_dbPagina, $redirect);
    }

    public function gerapermalinkAction() {
        $db = new Db_PagPagina();
        $vo = $db->fetchAll(array("pag_pagina.TIPO = ?" => "noticia"));
        foreach ($vo as $o) {
            $permalink = ZC_Util::verificaPermalink($db, $o->TITULO, $o->ID);
            if ($permalink["isValid"]) {
                if ($db->disableFiltro()->disablePadrao()->update(array("PERMALINK" => $permalink["permalink"]), array("ID = ?" => $o->ID))) {
                    $var['ok'][] = $permalink["permalink"];
                } else {
                    $var['nao ok'][] = $permalink["permalink"];
                }
            }
        }
        echo "##################### SALVO #########################";
        echo "<pre/>";
        print_r($var['ok']);
        echo "##################### NAO SALVO #########################";
        echo "<pre/>";
        print_r($var['nao ok']);
        exit;
    }

}
