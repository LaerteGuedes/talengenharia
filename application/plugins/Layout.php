<?

class Plugin_Layout extends Zend_Layout_Controller_Plugin_Layout {

    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        $this->_setupLayout($request->getModuleName());
    }

    protected function _setupLayout($moduleName) {
        //muda arquivo de layout conforme o modulo
        $moduleName = (!$moduleName) ? "site" : $moduleName;
        $this->getLayout()->setLayout('layout_' . $moduleName);
    }

}

