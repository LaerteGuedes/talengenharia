<?php

class Adm_AuxiliarController extends ZC_Controller_Action {

    protected $_data;
    protected $_dbAuxiliar;

    public function init() {
        $this->_dbArqArquivo = new Db_ArqArquivo();
        parent::init();
    }

    public function juploadAction() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        if ($_FILES['Filedata']) {
            try {
                $oFile = new Business_File($_FILES['Filedata'], $this->_data['folder']);
                $data = array(
                    'ID_PAGINA' => $this->_data['ID'],
                    'TABELA' => $this->_data['TABELA'],
                    'NOME_ARQUIVO' => $_FILES['Filedata']['name'],
                    'TIPO' => $oFile->file_type,
                    'MIME' => $_FILES['Filedata']['type'],
                    'EXT' => $oFile->file_ext
                );
                
                $id = $this->_dbArqArquivo->save($data);
                $oFile->setId($id);
                $oFile->upload();
                if ($oFile->file_type == 'image' && $this->_data['img_resize']) {
                    $oFile->resize(1200, false);
                    $oFile->multiResize(array(71, 150, 300, 400, 696, 955));
                }
                $db->commit();
                echo '1';
            } catch (Exception $exc) {
                $db->rollBack();
                echo $exc->getMessage();
            }
            exit;
        }else{
            exit();
        }
    }

    public function juploadnewAction() {

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        try {
            $oFile = new Business_File($_FILES['Filedata'], $this->_data['folder']);
            $data = array(
                'ID_PAGINA' => $this->_data['ID'],
                'TABELA' => $this->_data['TABELA'],
                'NOME_ARQUIVO' => $_FILES['Filedata']['name'],
                'TIPO' => $oFile->file_type,
                'MIME' => $_FILES['Filedata']['type'],
                'EXT' => $oFile->file_ext
            );

            $id = $this->_dbArqArquivo->save($data);
            $oFile->setId($id);

            $oFile->upload();
            if ($oFile->file_type == 'image' && $this->_data['img_resize']) {
                $oFile->resize(1200, false);
                $oFile->multiResize(array(71, 150, 300, 400, 696, 955));
            }
            $db->commit();
            echo '1';
        } catch (Exception $exc) {
            $db->rollBack();
            echo $exc->getMessage();
        }
        exit;
    }

    public function arquivoAction() {
        $this->disableLayout();
        if ($this->_data['ID_PAGINA']):
            $tabela = $this->_data['TABELA'] ? $this->_data['TABELA'] : "pag_pagina";
            $this->view->voArquivo = $this->_dbArqArquivo->fetchByPagina($this->_data['ID_PAGINA'], $tabela);
            $this->view->idPagina = $this->_data['ID_PAGINA'];
        endif;
    }

    public function atualizaposicaoAction() {
        $this->disableLayout();
        $this->disableView();
        $i = 1;
        foreach ($this->_data["components"] as $o) {
            $id = explode("ARQ_", $o);
            $id = current(array_filter($id));
            $this->_dbArqArquivo->update(array("ORDEM" => $i), array("ID = ?" => $id));
            $i++;
        }
    }

    public function jarquivodestaqueAction() {
        if ($this->_data['ID']):
            $oArquivo = $this->_dbArqArquivo->fetchRow("ID = '{$this->_data['ID']}'");
            if ($oArquivo):
                if ($this->_data['DESTAQUE']) {
                    $destaque = $this->_data['DESTAQUE'];
                } else {
                    $destaque = 1;
                }
                $this->_dbArqArquivo->update(array('DESTAQUE' => 0), array('ID_PAGINA = ?' => $oArquivo->ID_PAGINA, 'TABELA = ?' => $oArquivo->TABELA, "DESTAQUE = ?" => $destaque));
                $oArquivo->DESTAQUE = $destaque;
                $oArquivo->save();
            else:
                $erro = 1;
            endif;
        else:
            $erro = 1;
        endif;
        echo ($erro) ? json_encode(array('erro' => '1')) : json_encode(array("destaque" => $destaque));
        exit;
    }

    public function cropAction() {
        $this->disableLayout();
        if ($this->_data['ID']) {
            $this->view->oArquivo = $this->_dbArqArquivo->fetchRow("ID = '{$this->_data['ID']}'");
        } else {
            exit;
        }
    }

    public function arquivocropsaveAction() {

        $oArquivo = $this->_dbArqArquivo->disableFiltro()->disablePadrao()->fetchRow("ID = '{$this->_data['ID']}'");

        $data = array(
            'ID_PAGINA' => $oArquivo->ID_PAGINA,
            'TABELA' => $oArquivo->TABELA,
            'NOME_ARQUIVO' => $oArquivo->NOME_ARQUIVO,
            'TIPO' => $oArquivo->TIPO,
            'MIME' => $oArquivo->MIME,
            'EXT' => $oArquivo->EXT
        );


        $datas = new ZC_Date();
        $ano = $datas->render($oArquivo->DATA, "yyyy");
        $mes = $datas->render($oArquivo->DATA, "MM");


        if ($oArquivo->DATA) {
            $arq = $_SERVER['DOCUMENT_ROOT'] . "/upload/arq_arquivo/" . $ano . '/' . $mes . "/" . $oArquivo->ID . "." . $oArquivo->EXT;
        } else {
            $arq = $_SERVER['DOCUMENT_ROOT'] . "/upload/arq_arquivo/" . $oArquivo->ID . "." . $oArquivo->EXT;
        }


        $db = new Db_ArqArquivo();
        unset($this->_data["ID"]);

        $this->_data["EXT"] = $oArquivo->EXT;
        $this->_data["ID"] = $db->insert($data);



        if ($oArquivo->TIPO == 'image') {

            $file = array(
                "tmp_name" => $arq,
                "name" => $this->_data["ID"] . "." . $oArquivo->EXT,
            );


//            

            $path = "/upload/arq_arquivo/";
            $zFile = new Business_File($file, $path);
            $zFile->setId($this->_data["ID"]);

            $this->_data["img_original"] = $file;
            $this->_data["path_original"] = $path;

            $zFile->resizeCrop($this->_data);

            $zFile->multiResize(array(71, 150, 300, 400, 696, 955), $data);
        }

        echo ($erro) ? json_encode(array('erro' => $exc->getMessage())) : json_encode(array("destaque" => $destaque, 'msg' => 'Imagem cortada com sucesso!'));
        exit;
    }

    public function excluirarquivoAction() {
        if ($this->_data["CROP"]) {
            
        }
        parent::excluir($this->_dbArqArquivo);
    }

    function modelochamadasAction() {
        $this->disableLayout();
        $dbPagina = new Db_PagPagina();

        $s = $dbPagina->select()->from(array("pag_pagina"));

        $s->setIntegrityCheck(false);

        $s->joinLeft(array('img_destaque' => 'arq_arquivo'), "img_destaque.ID_PAGINA = pag_pagina.ID and img_destaque.DESTAQUE = 1 and TABELA = 'pag_pagina'", array('IMG_DESTAQUE' => 'img_destaque.ID'));

        $s->joinLeft('pag_categoria', "pag_categoria.ID = {$this->_data["ID_CATEGORIA"]}", array('CATEGORIA_NOME' => 'pag_categoria.NOME', 'CATEGORIA_ORDEM' => 'pag_categoria.ORDEM'));

        $s->where('pag_pagina.ID = ?', $this->_data["ID"]);

        $oNoticia = $dbPagina->disablePadrao()->fetchRow($s);

        $oNoticia->TITULO = $this->_data["TITULO"];
        $oNoticia->RESUMO = $this->_data["RESUMO"];
        $oNoticia->CHAPEU = $this->_data["CHAPEU"];
        $oNoticia->ID_CATEGORIA = $this->_data["ID_CATEGORIA"];
        $this->view->oNoticia = $oNoticia;
    }

    function listarAction() {
        #FUNÇAO CRIADA PARA VERIFICAR IMAGENS FORA DO FORMATO DE "DIRETORIO/ANO/MES/IMAGEM"


        $this->disableLayout();

        if ($this->_request->isPost()) {
            set_time_limit(60);

            $db = new Db_ArqArquivo();

            #VARIAVEIS 
            if ($this->_data["TABELA"] && $this->_data["COLUNA"] && $this->_data["ANO"] && $this->_data["ANO2"] && $this->_data["DIA"] && $this->_data["DIA2"] && $this->_data["HORA"] && $this->_data["HORA2"]) {

                $this->view->TABELA = $tabela = $this->_data["TABELA"];
                $this->view->COLUNA = $coluna = $this->_data["COLUNA"];
                $this->view->ANO = $ano = $this->_data["ANO"];
                $this->view->ANO2 = $ano2 = $this->_data["ANO2"];
                $this->view->MES = $mes = $this->_data["MES"];
                $this->view->MES2 = $mes2 = $this->_data["MES2"];
                $this->view->DIA = $dia = $this->_data["DIA"];
                $this->view->DIAS2 = $dia2 = $this->_data["DIA2"];
                $this->view->HORA = $hora = $this->_data["HORA"];
                $this->view->HORA2 = $hora2 = $this->_data["HORA2"];

                ## SELECT QUE BUSCA AS IMAGENS RELACIONADAS A UMA TABELA ##

                $s = $db->select()->from(array("a" => "arq_arquivo"));
                $s->setIntegrityCheck(false);
                $s->join(array("t" => "$tabela"), "a.ID_PAGINA = t.ID", array($coluna));
                $s->where("TABELA = ?", $tabela);
                $s->where("a.DATA IS NULL");
                $s->where("DATEPART(YEAR, $coluna) BETWEEN $ano and $ano2");
                $s->where("DATEPART(MONTH, $coluna) BETWEEN $mes and $mes2");
                $s->where("DATEPART(DAY, $coluna) BETWEEN $dia and $dia2");
                $s->where("DATEPART(HOUR, $coluna) BETWEEN $hora and $hora2");
                ;
                $s->order("$coluna");

                $data = new ZC_Date();

                $arImamge = $db->disableFiltro()->disablePadrao()->fetchAll($s);
                $nFor1 = count($arImamge->toArray()) * 7;

                $msg[] = "Total de Imagens - $nFor1 ";

                if ($nFor1) {

                    ## LAÇO QUE AGRUPA TODOS OS RESULTADOS POR ANO/MES ##
                    ## APENAS O ID/EXT SÃO ARMAZENADOS ##

                    foreach ($arImamge as $oImg) {
                        $ano = $data->render($oImg->DATA_CADASTRO, "yyyy");
                        $mes = $data->render($oImg->DATA_CADASTRO, "MM");
                        $vImg[$ano][$mes][] = $oImg->ID . "#" . $oImg->EXT . "#$oImg->DATA_CADASTRO";
                    }

                    ## PASTA DESTINO E PASTA DO ARQUIVO ORIGINAL ##

                    $pastaAtual = $pasta = "upload/arq_arquivo";

                    ## INICIO DOS LAÇOS QUE FARÃO O PROCESSO DE COPIA DOS ARQUIVOS ##

                    foreach ($vImg as $oAno => $vAno) {
                        foreach ($vAno as $oMes => $vMes) {

                            ## CAMINHO DAS PASTAS DE ARMAZENAMENTO DOS ARQUIVOS ##

                            $dirAno = $_SERVER['DOCUMENT_ROOT'] . "/" . $pasta . '/' . $oAno;
                            $dirMes = $_SERVER['DOCUMENT_ROOT'] . "/" . $pasta . '/' . $oAno . '/' . $oMes;
                            $dirAtual = $_SERVER['DOCUMENT_ROOT'] . "/" . $pastaAtual . "/";

                            ## VERIFICAÇÃO DE EXISTENCIA DO DIRETÓRIO A SER ARMAZENADO OS ARQUIVOS ##

                            if (!is_dir($dirAno)) {
                                mkdir($dirAno, 0775);
                            }
                            if (!is_dir($dirMes)) {
                                mkdir($dirMes, 0775);
                            }

                            $varOK = 0;

                            foreach ($vMes as $oImagem) {

                                ## INICIO DO PROCESSO DE VERIFICAÇÃO, TRANSFERENCIA E PERSISTENCIA DE ARQUIVOS ##

                                $vTamanho = array(71, 150, 300, 400, 696, 955); ## DIMENCIONAMENTO DAS IMAGENS ##

                                $vIdExt = explode("#", $oImagem); ## SEPARAÇÃO DO ID#EXT#DATA_CADASTRO ##

                                $nomeArquivo = "{$vIdExt[0]}.{$vIdExt[1]}";

                                $fileOriginal = $dirAtual . $nomeArquivo; ## CRIAÇÃO CAMINHO DA IMAGEM ORIGINAL ##

                                $idUpdate[] = $vIdExt[0];
                                $dataUpdate[] = $vIdExt[2];

                                if (file_exists($fileOriginal)) { ## VERIFICA SE EXISTE O ARQUIVO ORIGINAL ##
                                    ## VERIFICA SE EXISTE O ARQUIVO ORIGINAL FOI COPIADO ##
                                    if (!copy($fileOriginal, $dirMes . "/$nomeArquivo")) {
                                        $msg[] = "<span style='color: red'> Arquivo " . $fileOriginal . " não foi copiado </span>";
                                    } else {
                                        if (!unlink($fileOriginal)) {
                                            $msg[] = "<span style='color: red'>Arquivo original nao foi deletado -> $fileOriginal </span>";
                                        } else {
                                            $msg[] = "<span style='color: blue'> $fileOriginal OK </span>";
                                            $varOK++;
                                        }
                                    }
                                } else {
                                    $msg[] = "<span style='color: red'> Arquivo Original " . $fileOriginal . " não existe no servidor</span> ";
                                }

                                ## VERIFICA SE EXISTE O ARQUIVO ORIGINAL FOI COPIADO ##

                                foreach ($vTamanho as $size) {

                                    ## INICIA A COPIA DOS ARQUIVOS REDIMENCIONADOS ##

                                    $nomeArquivoSizes = "{$vIdExt[0]}-{$size}.{$vIdExt[1]}";
                                    $fileOriginalSizes = $dirAtual . $nomeArquivoSizes;

                                    if (file_exists($fileOriginalSizes)) { ## VERIFICA SE EXISTE O ARQUIVO ORIGINAL REDIMENCIONADO ##
                                        ## VERIFICA SE EXISTE O ARQUIVO ORIGINAL REDIMENCIONADO ##
                                        if (!copy($fileOriginalSizes, $dirMes . "/$nomeArquivoSizes")) {

                                            $msg[] = "<span style='color: red'> Arquivo " . $fileOriginalSizes . " não foi copiado </span>";
                                        } else {
                                            if (!unlink($fileOriginalSizes)) {
                                                $msg[] = "<span style='color: red'> Arquivo original size nao foi deletado -> $fileOriginalSizes</span>";
                                            } else {
                                                $varOK++;
                                                $msg[] = "<span style='color: blue'> $fileOriginalSizes OK </span> ";
                                            }
                                        }
                                    } else {
                                        $msg[] = "<span style='color: red'> Arquivo Redimencionado " . $fileOriginalSizes . " não existe no servidor</span> ";
                                    }
                                }
                            }
                        }
                    }
                    ## PERSISTE DAS IMAGENS NO BANCO PARA QUE AS MESMAS PASSEM PARA O NOVO PROCESSO DE IMAGENS ##

                    try {
                        for ($i = 0; $i < count($idUpdate); $i++) {
                            $update = array("DATA" => $dataUpdate[$i]);
                            $where = array("ID = ?" => $idUpdate[$i]);
                            $db->update($update, $where);
                        }
                    } catch (Exception $exc) {
                        $msg[] = $exc->getMessage();
                    }
                } else {
                    $msg[] = "Sem imagem para processar.";
                }
                $msg[] = "DONE XDD";
            } else {
                $msg[] = "Falta algum parametro.";
            }
        }
        $this->view->msg = $msg;
    }

}
