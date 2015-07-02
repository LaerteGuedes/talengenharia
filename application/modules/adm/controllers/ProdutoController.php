<?php

class Adm_ProdutoController extends ZC_Controller_Action{

    protected $_data;
    protected $_dbProduto;

    public function init(){
        $this->_dbProduto = new Db_ProProduto;
        $this->_head = new Business_Head;
        parent::init();
        $this->view->data = $this->_data;
    }

    public function indexAction(){
        $this->view->voProdutos = $this->_dbProduto->fetchAll(array('pro_produto.NOME IS NOT NULL'))->paginator();
        $this->view->destaque = true;

        $this->view->title = 'Gerência de Produtos';
    }

    public function inserealteraAction(){
        $bHead = new Business_Head;
        $bHead->jqueryMask();

        $bData = new ZC_Date();
        $form = new Adm_Form_ProProduto();
        $this->_data['DATA'] = $bData->toDb($this->_data['DATA']);
        $form->eHidden('ID_PAI', $this->_data['ID_PAI']);

        if ($this->_request->isPost()){
            $this->_data['PERMALINK'] = Business_String::formatPermalink($this->_data['NOME']);
        }

        $this->inserealterapagina($form, $this->_dbProduto, '/adm/produto/index', false, 'institucional');
    }

    public function excluirAction() {
        $redirect = $this->_data['move'] ? '/adm/produto/' . $this->_data['move'] : null;
        if ($this->_data['produto'] && $this->_data['move']) {
            $redirect = '/adm/' . $this->_data['move'] . '/' . $this->_data['produto'];
        }
        parent::excluir($this->_dbProduto, $redirect);
    }

    public function adddestaqueordemAction() {
        ZC_Db_Filtro::setParam('TIPO', 'noticia');
        $oProduto = $this->_dbProduto->fetchRow(array("pro_produto.ID = ?" => $this->_data['ID']));

        $valor = ($this->_data['VALOR'] == 0) ? null : $this->_data['VALOR'];
        $oSegProduto = ($valor) ? $this->_dbProduto->fetchRow(array("pro_produto.FL_DESTAQUE_ORDEM = ?" => $valor)) : NULL;
        $valorAntigo = $oProduto->FL_DESTAQUE_ORDEM;

        $where = array(
            'FL_DESTAQUE_ORDEM' => $valor,
        );
        $whereAux = array(
            'FL_DESTAQUE_ORDEM' => $valorAntigo,
        );

        $this->_dbProduto->update($where, array('ID = ?' => $this->_data["ID"]));
        if ($oSegProduto) {
            $this->_dbProduto->update($whereAux, array('ID = ?' => $oSegPagina->ID));
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

        $this->_dbProduto->update($where, array('ID = ?' => $this->_data["ID"]));

        $this->disableView();
        exit;
    }

}

?>