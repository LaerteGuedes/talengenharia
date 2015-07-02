<?php

class Adm_ConfigController extends ZC_Controller_Action {

    public function init() {
        parent::init();
    }

    public function indexAction() {
        $this->_data['ID'] = 1;
        $this->view->TABELA = 'conf_site';
        $this->inserealterapagina(new Adm_Form_SiteConf(), new Db_Config(), '/adm/config/', true, '', false, false);
    }

    public function criadordegerenciasAction() {
        if ($this->_request->isPost()) {
            for ($i = 0; $i < count($this->_data["COLUNA"]); $i++) {
                if ($this->_data["TIPO"][$i] && $this->_data["COLUNA"][$i] <> "" || $this->_data["TIPO"][$i] == "submit") {

                    $validacao = (is_array($this->_data["VALIDACAO_" . ($i + 1)])) ? current($this->_data["VALIDACAO_" . ($i + 1)]) : $this->_data["VALIDACAO_" . ($i + 1)];
                    $listagem = (is_array($this->_data["LISTAGEM_" . ($i + 1)])) ? current($this->_data["LISTAGEM_" . ($i + 1)]) : $this->_data["LISTAGEM_" . ($i + 1)];

                    $vRow[] = array(
                        "COLUNA" => $this->_data["COLUNA"][$i],
                        "TIPO" => $this->_data["TIPO"][$i],
                        "LABEL" => $this->_data["LABEL"][$i],
                        "CLASS" => $this->_data["CLASS"][$i],
                        "VALOR" => $this->_data["VALOR"][$i],
                        "VALIDACAO" => $validacao,
                        "LISTAGEM" => $listagem,
                    );

                    $data = array(
                        "GERENCIA" => $this->_data["GERENCIA"],
                        "TABELA" => $this->_data["TABELA"],
                        "ROW" => $vRow,
                    );
                }
            }
            $this->geradorGerencias($data);
            ZC_Alerta::add("sucesso", "Gerencia criada com sucesso!<br/> Vá até o menu e adione o nome da gerencia.");
        }

        $dbAdapter = Zend_Db_Table::getDefaultAdapter();
        $vTables = $dbAdapter->listTables();
        foreach ($vTables as $o) {
            $nameTable = "Db_" . $this->getNomeClass($o);
            if (class_exists($nameTable)) {
                $db = new $nameTable;
                $vInfo = $db->info(Zend_Db_Table_Abstract::METADATA);
                $Tabela[$o] = $vInfo;
            } else {
                $TabelaNot[] = $nameTable;
            }
        }
        $this->view->infos = $Tabela;
    }

    public function ajaxtabelaAction() {
        $this->disableLayout();
        $nameTable = "Db_" . $this->getNomeClass($this->_data["TABELA"]);
        if (class_exists($nameTable)) {
            $db = new $nameTable;
            $this->view->infos = $db->info(Zend_Db_Table_Abstract::METADATA);
            $this->view->tabela = $this->_data["TABELA"];
        } else {
            $this->view->error = "Tabela não encontrada";
        }
    }

    function getNomeClass($tabela) {
        $v = explode("_", $tabela);
        $cnt = count($v);
        if ($cnt > 1) {
            foreach ($v as $oV) {
                $classNome .=ucfirst($oV);
            }
            return $classNome;
        } else {

            return ucfirst(current($v));
        }
    }

    function geradorGerencias($dados) {
        $this->geraController($dados);
        $this->geraForm($dados);
        $this->geraView($dados);
        return true;
    }

    function geraView($dados) {
        $this->geraPastas($dados);
        $this->geraListagem($dados);
        $this->geraInserealtera($dados);
        return true;
    }

    function verificaUploadPupulate($dados) {
        $cnt = 0;
        foreach ($dados["ROW"] as $o) {
            if ($o["TIPO"] == "upload") {
                $cnt++;
            }
        }

        for ($i = 0; $i < $cnt; $i++) {
            if ($i > 0) {
                $html2 .= trim(
                        'if ($this->getElement("UPLOAD_{N}")) {
            $this->getElement("UPLOAD")->setParam("ID", $values["ID"]);
        }');
                $html2 = str_replace('{N}', $i, $html2);
            } else {
                $html2 .= trim(
                        'if ($this->getElement("UPLOAD")) {
            $this->getElement("UPLOAD")->setParam("ID", $values["ID"]);
        }');
            }
        }

        return $html2;
    }

    function geraController($dados) {
        $html = trim('
<?php
class Adm_{GERENCIA}Controller extends ZC_Controller_Action {

    protected $_data;

    public function init() {
        $this->_db{DB_CLASS} = new Db_{DB_CLASS}();
        parent::init();
        $this->view->data = $this->_data;
    }

    ## GERÊNCIA DE {GERENCIA_UPPER} ##

    public function indexAction() {

        ZC_Db_Filtro::setParam("TIPO", "{TIPO}");

        $this->view->vo = $this->_db{DB_CLASS}->fetchAll()->paginator();

        $this->view->TITULO = "Gerência de {GERENCIA}";
    }

    public function inserealteraAction() {
        $bData = new ZC_Date();
        $form = new Adm_Form_{FRM}();
        $this->view->TABELA = "{TABELA}";
        $this->inserealterapagina($form, $this->_db{DB_CLASS}, "/adm/{GERENCIA_LOWER}", false, "{TIPO}");
    }

    #### GERAL ###################

    public function excluirAction() {
        $redirect = $this->_data["move"] ? "/adm/{GERENCIA_LOWER}" . $this->_data["move"] : null;
        parent::excluir($this->_db{DB_CLASS}, $redirect);
    }

}
');
        $path = APPLICATION_PATH . "/modules/adm/controllers/";
        $nomeClass = $this->getNomeClass($dados["GERENCIA"]);
        $nomeClassDb = $this->getNomeClass($dados["TABELA"]);

        $html = str_replace('{FRM}', $nomeClass, $html);
        $html = str_replace('{DB_CLASS}', $nomeClassDb, $html);
        $html = str_replace('{TABELA}', $dados["TABELA"], $html);
        $html = str_replace('{GERENCIA}', $dados["GERENCIA"], $html);
        $html = str_replace('{GERENCIA_UPPER}', strtoupper($dados["GERENCIA"]), $html);
        $html = str_replace('{GERENCIA_LOWER}', strtolower($dados["GERENCIA"]), $html);

        foreach ($dados["ROW"] as $o) {
            if ($o["COLUNA"] == "TIPO") {
                $tipo = $o["VALOR"];
            }
        }

        $html = str_replace('{TIPO}', ($tipo) ? $tipo : "", $html);

        $nomeController = "{$dados["GERENCIA"]}Controller.php";

        file_put_contents($path . $nomeController, $html);
        return true;
    }

    function geraForm($dados) {
        $html .= trim('
            <?php
class Adm_Form_{DB_FORM} extends ZC_Form {

    public function populate(array $values) {');

        $html .= $this->verificaUploadPupulate($dados);

        $html .= trim(
                'return parent::populate($values);
    }

    public function init() {
        $this->addAttribs(array("id" => "form{DB_FORM}"));
    ');
        $html .= $this->verificaCamposForm($dados);


        $html .= trim('
$this->addDisplayGroup(array({LISTA_COLUNA}), "groupDados", array("legend" => "Dados"));
$this->cSetDecoratorTable();
}

}');
        $nomeClass = $this->getNomeClass($dados["GERENCIA"]);
        $path = APPLICATION_PATH . "/modules/adm/forms/";
        $html = str_replace('{DB_FORM}', $nomeClass, $html);


        foreach ($dados["ROW"] as $o) {
            if (!in_array($o["TIPO"], array("submit", "upload"))) {
                $vList[] = '"' . $o["COLUNA"] . '"';
            } else {
                if (in_array($o["TIPO"], array("submit"))) {
                    $vList[] = '"ENVIAR"';
                }
                if (in_array($o["TIPO"], array("upload"))) {
                    $vList[] = '"UPLOAD"';
                }
            }
        }

        $html = str_replace('{LISTA_COLUNA}', join(",", $vList), $html);

        $nomeClassArquivo = "{$nomeClass}.php";

        file_put_contents($path . $nomeClassArquivo, $html);

        return true;
    }

    function geraListagem($dados) {
        $html .= trim('
<?
$bAcl = new ZC_Acl();
$controller = "{CONTROLLER_LOWER}";
$pagina = $this->data["action"];
($pagina == "index") ? $pagina = "" : $pagina;
?>

<h1><?= $this->title; ?></h1>
<form action="/adm/<?=$controller?>/excluir" method="post">
    <? if ($bAcl->verifica(false, "adm", $controller, "inserealtera" . $pagina)): ?>
        <a class="button jInfo" href="<?= $this->baseUrl("/adm/{$controller}/inserealtera{$pagina}"); ?>" title="Cadastrar">Cadastrar</a>
    <? endif; ?>
    <? if ($bAcl->verifica(false, "adm", $controller, "inserealtera" . $pagina)): ?>
        <a class="button jInfo jSubmit" href="<?= $this->baseUrl("/adm/{$controller}/excluir"); ?>" title="Clique para exluir">Excluir selecionadas</a>
    <? endif; ?>

    <? if (count($this->vo)): ?>
        <p><?= $this->vo->getTotalItemCount() ?> registro(s) encontrados.</p>
        <table class="padrao">
            <thead>
                <tr>
                    <th width="26"><input type="checkbox" class="jCheckAll" /></th>
');
        $html .= $this->verificaTituloListagem($dados);
        $html .= trim('
                    <th width="160">AÇÕES</th>
                </tr>
            </thead>
            <tbody>
                <? foreach ($this->vo as $o): ?>
                    <tr>
                        <td><input name="ID[<?= $o->ID ?>]" type="checkbox" value="<?= $o->ID ?>" /><span></span></td>');
        $html .= $this->verificaValorListagem($dados);
        $html .= trim('
                        <td class="action">
                            <a class="bt bt_editar" title="editar página" href="<?= $this->baseUrl("/adm/{$controller}/inserealtera{$pagina}/ID/" . $o->ID) ?>"></a>
                            <a class="bt bt_excluir" title="excluir página" href="<?= $this->baseUrl("/adm/{$controller}/excluir/move/{$pagina}/ID/" . $o->ID) ?>"></a>
                        </td>
                    </tr>
                <? endforeach; ?>
            </tbody>
        </table>
        <?= $this->paginationControl($this->vo, "Sliding", "_includes/paginacao.phtml") ?>
    <? else: ?>
        <p>Nenhum registro encontrado</p>
    <? endif ?>
</form>');

        $path = APPLICATION_PATH . "/modules/adm/views/scripts/" . strtolower($dados["GERENCIA"]);
        $nomelista = "/index.phtml";

        $html = str_replace('{CONTROLLER_LOWER}', strtolower($dados["GERENCIA"]), $html);

        file_put_contents($path . $nomelista, $html);
        return true;
    }

    function geraPastas($dados) {
        $path = APPLICATION_PATH . "/modules/adm/views/scripts/" . strtolower($dados["GERENCIA"]);
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        return true;
    }

    function geraInserealtera($dados) {
        $html = trim(' 
<script type="text/javascript">
    function loadImagem(){
        $("#UPLOAD_CONTEUDO").load("/adm/auxiliar/arquivo/ID_PAGINA/<?= $this->ID ?>/TABELA/<?= $this->TABELA ?>");
    }
    $(function(){
        loadImagem();
    });
</script>
<?= $this->form ?>
');
        $path = APPLICATION_PATH . "/modules/adm/views/scripts/" . strtolower($dados["GERENCIA"]);
        file_put_contents($path . "/inserealtera.phtml", $html);
        return true;
    }

    function verificaTituloListagem($dados) {
        foreach ($dados["ROW"] as $o) {
            if ($o["LISTAGEM"] == 1) {
                if ($o["TIPO"] == "upload") {
                    $valor = "###";
                } else {
                    $vListagem[] = strtoupper($o["LABEL"]);
                }
            }
        }
        if ($valor) {
            array_unshift($vListagem, $valor);
        }
        if ($vListagem) {
            foreach ($vListagem as $o) {
                $html .= trim('<th>' . $o . '</th>');
            }
        } else {
            foreach ($dados["ROW"] as $o) {
                if (!in_array($o["TIPO"], array("submit", "upload"))) {
                    $html .= trim('<th>' . $o["LABEL"] . '</th>');
                }
            }
        }
        return $html;
    }

    function verificaValorListagem($dados) {
        foreach ($dados["ROW"] as $o) {
            if ($o["LISTAGEM"] == 1) {
                if ($o["TIPO"] == "upload") {
                    $valor = $this->verificaFuncoes($o);
                } else {
                    $vListagem[] = $this->verificaFuncoes($o);
                }
            }
        }
        if ($valor) {
            array_unshift($vListagem, $valor);
        }

        if ($vListagem) {
            foreach ($vListagem as $o) {
                $html .= trim('<td><?= $o->' . $o . ' ?></td>');
            }
        } else {
            foreach ($dados["ROW"] as $o) {
                if (!in_array($o["TIPO"], array("submit", "upload"))) {
                    $html .= trim('<td><?= $o->' . $o["COLUNA"] . ' ?></td>');
                }
            }
        }
        return $html;
    }

    function verificaFuncoes($dados) {
        switch ($dados["TIPO"]) {
            case "upload": $html = "getImgDestaque(array(60,40))";
                break;
            case "date": $html = "getDataF()";
                break;
            default :$html = $dados["COLUNA"];
        }

        return $html;
    }

    function verificaCamposForm($dados) {

        foreach ($dados["ROW"] as $o) {
            switch ($o["TIPO"]) {
                case 'hidden':
                    $html .= trim('$this->eHidden("{COLUNA}"{VALOR});');
                    break;
                case 'text':
                    $html .= trim('$this->eText("{COLUNA}", "{LABEL}:", {VALIDACAO}, 100, 80);');
                    break;
                case 'date':
                    $html .= trim('$this->eData("{COLUNA}", "{LABEL}:", {VALIDACAO});');
                    break;
                case 'select':
                    $html .= trim('$this->eSelect("{COLUNA}", "{LABEL}:", {VALIDACAO},{OPCOES});');
                    break;
                case 'multichekbox':
                    $html .= trim('$this->eMultiCheckbox("{COLUNA}", "{LABEL}:", {VALIDACAO},{OPCOES});');
                    break;
                case 'radio':
                    $html .= trim('$this->eRadio("{COLUNA}", "{LABEL}:", {VALIDACAO},{OPCOES});');
                    break;
                case 'textarea':
                    $html .= trim('$this->eTextarea("{COLUNA}", "{LABEL}:", {VALIDACAO},false,false,false);');
                    break;
                case 'ckeditor':
                    $html .= trim('$this->eTextarea("{COLUNA}", "{LABEL}:", {VALIDACAO},false,false,true);');
                    break;
                case 'password':
                    $html .= trim('$this->ePassword("{COLUNA}", "{LABEL}:", {VALIDACAO});');
                    break;
                case 'upload':
                    $html .= trim('
$this->addElement("Upload", "UPLOAD")
        ->setPasta("/upload/arq_arquivo/")
        ->setAction("/adm/auxiliar/jupload/")
        ->setParam("TABELA", "{COLUNA}")
        ->setCallback("loadImagem();")
        ->setMulti(true)
        ->debug(true)
        ->setLabel("Imagem:");
');
                    break;
                case 'submit':
                    $html .= trim('$this->eSubmit("ENVIAR", "{LABEL}");');
                    break;
            }
            $html = str_replace('{COLUNA}', $o["COLUNA"], $html);
            $html = str_replace('{VALOR}', (($o["VALOR"]) ? ",'" . $o["VALOR"] . "'" : ""), $html);
            $html = str_replace('{LABEL}', $o["LABEL"], $html);
            $html = str_replace('{VALIDACAO}', ($o["VALIDACAO"]) ? "true" : "false", $html);
            $html = str_replace('{OPCOES}', "", $html);
        }
        return $html;
    }

}
