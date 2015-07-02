<?php

class Adm_PaginaController extends ZC_Controller_Action {

    protected $_data;

    public function init() {
        $this->_dbPagina = new Db_PagPagina();
        parent::init();
        $this->view->data = $this->_data;
    }

    public function indexAction() {
        ZC_Db_Filtro::setParam('TIPO', 'institucional');
        ZC_Db_Filtro::setParam('ID_PAI', '');
        $this->view->vo = $this->_dbPagina->fetchAll()->paginator();
        $this->view->subpagina = true;
        $this->view->title = 'Gerência de Páginas Institucionais';
        $this->render('lista');
    }

    public function inserealteraAction() {
        $bData = new ZC_Date();
        $form = new Adm_Form_PagPagina();
        $this->_data['DATA'] = $bData->toDb($this->_data['DATA']);
        $form->eHidden('ID_PAI', $this->_data['ID_PAI']);
        $this->inserealterapagina($form, $this->_dbPagina, '/adm/pagina/', false, 'institucional');
    }

         
    public function institucionalAction(){
        $this->view->vo = $this->_dbPagina->fetchAll(array("pag_pagina.TIPO = ?" => $this->_data['pagina'], "pag_pagina.ID_PAI IS NULL" => ''))->paginator();
        $this->view->subpagina = true;
        $this->view->title = 'Gerência de '.ucfirst($this->_data['pagina']);
        $this->view->destaque = true;
        $this->view->page = $this->_data['pagina'];
        $this->view->elementosColuna = $this->customColumnElements($this->_data['pagina']);
        $this->renderView($this->_data['pagina']);
    }
    
    public function inserealterainstitucionalAction(){
        $form = $this->instanceFormClasses($this->_data['pagina']); 
              
        $form->removeElements(array('YOUTUBE'));
        $this->inserealterapagina($form, $this->_dbPagina, '/adm/institucional/'.$this->_data['pagina'], false, ''.$this->_data['pagina']);
    }

    public function lockpaginaAction() {
        $this->disableLayout();
        try {
            $o = $this->_dbPagina->disableFiltro()->disablePadrao()->fetchRow(array("ID = ?" => $this->_data["ID"]));
            if ($o->FL_LOCK == 0) {
                $lock = 1;
            } else {
                $lock = 0;
            }
            $where = array(
                'FL_LOCK' => $lock,
            );
            $this->_dbPagina->update($where, array('ID = ?' => $this->_data["ID"]));
            $jData['success'] = true;
        } catch (Exception $exc) {
            $jData['success'] = false;
        }
        if ($this->_data['ajax']) {
            echo json_encode($jData);
        } else {
            $redirect = ($this->_data["redirect"]) ? $this->_data["redirect"] : '/adm/pagina/index';
            $this->_redirect($redirect);
        }
        exit;
    }

    public function videoAction() {
        ZC_Db_Filtro::setParam('TIPO', 'video');
        $this->view->vo = $this->_dbPagina->fetchAll(array(), 'ID DESC')->paginator();
        $this->view->title = 'Gerência de Vídeos';
        $this->render('lista');
    }

    public function inserealteravideoAction() {
        $form = new Adm_Form_PagPagina();
        $form->removeElements(array('RESUMO', 'CHAPEU', 'DATA', 'AUTOR', 'TEXTO'));
        $this->inserealterapagina($form, $this->_dbPagina, '/adm/pagina/video', false, 'video');
    }

    public function newsletterAction() {
        $db = new Db_PagNewsletter;
        $this->view->vo = $db->fetchAll(array(), 'ID DESC')->paginator();
        $this->view->title = 'Gerência de Newsletter';
        $this->render('lista');
    }

    public function inserealteranewsletterAction() {
        $db = new Db_PagNewsletter;
        $form = new Adm_Form_PagNewsletter();
        $this->inserealterapagina($form, $db, '/adm/pagina/newsletter', false, 'newsletter');
    }

    public function jogoAction() {
        ZC_Db_Filtro::setParam('TIPO', 'jogo');
        $this->view->vo = $this->_dbPagina->fetchAll(array(), 'ID DESC')->paginator();
        $this->view->title = 'Gerência de Jogos';
        $this->render('lista');
    }

    public function inserealterajogoAction() {
        $oHeader = new Business_Head();
        $oHeader->jqueryUi();
        $oHeader->jCharCount();
        $oHeader->jTag();
        $this->view->TAGSV = true;
        $dbTag = new Db_TagTag();
        $form = new Adm_Form_PagPaginaNotJogo();
        $form->removeElements(array("ID_CATEGORIA", 'CHAPEU', 'TITULO', "TAG", 'RESUMO', "FONTE_VIDEO", 'YOUTUBE', 'AUTOR', 'TEXTO', 'UPLOAD', 'DATA_CADASTRO', 'DATA_INI', 'DATA_FIM', "HORA", 'LINK'));
        $this->inserealterapagina($form, $this->_dbPagina, '/adm/pagina/jogo', false, 'jogo');
    }

    public function galeriaAction() {
        ZC_Db_Filtro::setParam('TIPO', 'galeria');
        $this->view->vo = $this->_dbPagina->fetchAll(array(), 'ID DESC')->paginator();
        $this->view->title = 'Gerência de Galeria de Imagens';
        $this->render('lista');
    }

    public function inserealteragaleriaAction() {
        $form = new Adm_Form_PagPagina();
        $form->removeElements(array('RESUMO', 'CHAPEU', 'DATA', 'AUTOR', 'TEXTO', 'YOUTUBE'));
        $this->inserealterapagina($form, $this->_dbPagina, '/adm/pagina/galeria', false, 'galeria');
    }

    public function eventoAction() {
        ZC_Db_Filtro::setParam('TIPO', 'evento');
        $this->view->vo = $this->_dbPagina->fetchAll(array(), 'ID DESC')->paginator();
        $this->view->title = 'Gerência de Eventos';
    }

    public function inserealteraeventoAction() {
        $form = new Adm_Form_PagPagina();
        $form->removeElements(array('CHAPEU', 'AUTOR', 'UPLOAD', 'YOUTUBE'));
        $this->inserealterapagina($form, $this->_dbPagina, '/adm/pagina/evento', true, 'evento');
    }

    public function informativoAction() {
        $this->view->title = 'Gerência de Informativos';
        ZC_Db_Filtro::setParam('TIPO', 'informativo');
        $this->view->vo = $this->_dbPagina->fetchAll(array(), 'ID DESC')->paginator();
        $this->render('lista');
    }

    public function inserealterainformativoAction() {
        $form = new Adm_Form_PagPagina();
        $form->removeElements(array('RESUMO', 'CHAPEU', 'DATA', 'AUTOR', 'TEXTO', 'YOUTUBE'));
        $this->inserealterapagina($form, $this->_dbPagina, '/adm/pagina/informativo', false, 'informativo');
    }

    public function menuAction() {
        ZC_Db_Filtro::setParam('TIPO', 'menu');
        $this->view->vo = $this->_dbPagina->fetchAll(array("ID_PAI IS NULL"), 'ID DESC')->paginator();
    }

    public function inserealteramenuAction() {
        if ($this->_data['ID']) {
            ZC_Db_Filtro::setParam('TIPO', 'menu');
            $this->view->o = $this->_dbPagina->fetchRow(array("ID = ?" => $this->_data['ID']));
        }
        $this->inserealterapagina(new Adm_Form_PagPaginaMenu(), $this->_dbPagina, '/adm/pagina/menu', true, 'menu', false, false);
    }

    public function importacaocsvAction() {
        $row = 0;
        $i = 0;
        if (($handle = fopen($_SERVER['DOCUMENT_ROOT'] . '/upload/importacao/arquivo.csv', "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                if ($row > 0) {
                    unset($data2);
                    foreach ($data as $oD) {
                        $data2[$index[$i]] = rtrim(rtrim($oD, '.'), "-");
                        $i++;
                        $i = ($i > 12) ? 0 : $i;
                    }
                    $os[] = $data2;
                } elseif ($row == 0) {
                    foreach ($data as $o) {
                        $nInd[] = rtrim(join("_", explode(".", $o)), "_");
                    }
                    $index = $nInd;
                }
                $row++;
            }
            fclose($handle);
            Business_Util::debug((object) $os);
        }
        die;
    }

    public function excluirAction() {
        $redirect = $this->_data['move'] ? '/adm/pagina/' . $this->_data['move'] : null;
        if ($this->_data['pagina'] && $this->_data['move']) {
            $redirect = '/adm/' . $this->_data['move'] . '/' . $this->_data['pagina'];
        }
        parent::excluir($this->_dbPagina, $redirect);
    }

    public function downloadAction() {
        try {
            $o = $this->_dbPagina->disableFiltro()->disablePadrao()->fetchRow(array("ID = ?" => $this->_data['id']));

            $path = APPLICATION_PATH . "/../public_html";
            $archive_file_name = $o->PERMALINK . '.zip';
            $zip = new ZipArchive();
            if ($zip->open($archive_file_name, ZIPARCHIVE::CREATE) == TRUE) {
                foreach ($o->getArquivo() as $oA) {
                    if (file_exists($path . $oA->getUrl())) {
                        $zip->addFile($path . $oA->getUrl(), $oA->NOME_ARQUIVO);
                    } else {
                        throw new Exception("O Arquivo " . $oA->NOME_ARQUIVO . " não foi encontrado!");
                    }
                }
                $zip->close();
                header("Content-type: application/zip");
                header("Content-Disposition: attachment; filename=$archive_file_name");
                header("Content-length: " . filesize($archive_file_name));
                header("Pragma: no-cache");
                header("Expires: 0");
                readfile("$archive_file_name");
                exit;
            }
        } catch (Exception $exc) {
            ZC_Alerta::add("erro", $exc->getMessage());
        }
        $this->_redirect(current($_SESSION["ultima_pagina"]["uri"]));
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
    
    private function renderView($page, $padrao = 'lista'){
        $controller = $this->_data['controller'];
        $pathFile = "/adm/{$controller}/{$page}.phtml";
        (is_file($pathFile)) ? $this->render($page) : $this->render($padrao); 
    }
    
    private function customColumnElements($page){
        if ($page == 'portifolio'){
            return $elementosColuna = array('Titulo da Atividade' => 'TITULO', 'Resumo da Atividade' => 'RESUMO');
        }
        if ($page == 'produtos'){
            return $elementosColuna = array('Nome do Produto' => 'TITULO', 'Resumo' => 'RESUMO');
        }
        if ($page == 'equipe'){
            return $elementosColuna = array('Nome do Membro' => 'TITULO','Cargo' => 'RESUMO');
        }
        if ($page == 'historico'){
            return $elementosColuna = array('Acontecimento' => 'TITULO', 'Data' => 'DATA_INI');
        }
        return $elementosColuna = array('Título' => 'TITULO', 'Resumo' => 'RESUMO');
    }

}