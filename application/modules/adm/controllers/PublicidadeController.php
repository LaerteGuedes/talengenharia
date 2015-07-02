<?php
class Adm_PublicidadeController extends ZC_Controller_Action {

	protected $_data;
	protected $_dbPublicidade;

	public function init(){
		$this->_dbPublicidade = new Db_PubPublicidade();
		parent::init();
	}

	public function indexAction(){
		$this->view->vo = $this->_dbPublicidade->fetchAll()->paginator();
	}

	public function inserealteraAction(){
		parent::inserealterapagina(new Adm_Form_PubPublicidade(),$this->_dbPublicidade, '/adm/publicidade/', false, '');
	}

	protected function save(){
		parent::save($this->_dbPublicidade);
	}

	public function excluirAction(){
		parent::excluir($this->_dbPublicidade);
	}

	public function juploadAction(){
		$db = Zend_Db_Table::getDefaultAdapter();
		$dbArqImagem = new Db_ArqArquivo();
		$db->beginTransaction();
		try {
			$oFile = new Business_File($_FILES['Filedata'], $this->_data['folder']);
			$id = $this->_dbArqImagem->save(array('ID_PAGINA'=>$this->_data['ID'],'TABELA'=>$this->_data['TABELA'],'NOME_ARQUIVO'=>$_FILES['Filedata']['name'],'TIPO'=>$oFile->file_type));
			$oFile->setId($id);
			$oFile->upload();
			$db->commit();
			echo $id;
		} catch (Exception $exc){
			$db->rollBack();
			echo $exc->getMessage();
		}
		exit;
	}
}