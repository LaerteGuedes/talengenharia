<?php

class Business_Head {

    protected $view;

    public function __construct() {
        $this->view = Zend_Registry::get('view');
        $this->_data = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        $this->_module = $this->_data['module'];
    }

    function loadDefault() {
        $this->view->headScript()->prependFile($this->view->baseUrl('/library/jquery-migrate-1.2.1.min.js'));
        $this->view->headScript()->prependFile($this->view->baseUrl('/library/jquery-1.11.1.min.js'));
        return $this;
    }

    function loadByModule() {
        switch ($this->_module):
            case 'adm':
                $this->view->headLink()->prependStylesheet($this->view->baseUrl('/css/adm.css'));
                $this->jqueryValidation();
                if (ZC_Alerta::count()) {
                    $this->jqueryTools();
                }
                $this->view->headScript()->appendFile($this->view->baseUrl('/js/bootstrap_adm.js'));
                $this->jSortable();
                $this->bootstrap();
                $this->jModernizr();
                break;
            default:
                if (ZC_Alerta::count()) {
                    $this->jqueryTools();
                }
                $this->bootstrap();
                $this->view->headLink()->prependStylesheet($this->view->baseUrl('/fonts/stylesheet.css'));
                $this->view->headLink()->prependStylesheet($this->view->baseUrl('/imagem/site/ycon.css'));
                $this->jqueryCycle2();
                $this->easing();
                $this->skrollr();
                $this->jqueryValidation();
                $this->view->headLink()->appendStylesheet($this->view->baseUrl('/css/site.css'));
                $this->view->headScript()->appendFile($this->view->baseUrl('/js/bootstrap_site.js'));
                break;
        endswitch;
        return $this;
    }

    function show() {
        echo $this->view->headLink();
        echo $this->view->headScript();
    }

    public function setTitle($title) {
        $this->view->headTitle($title . ' - ' . $this->view->translate(Zend_Registry::get("oConfiguracao")->TITULO));
    }

    public function getTitle() {
        echo $this->view->headTitle();
    }

    public function bootstrap() {
        $this->view->headLink()->prependStylesheet($this->view->baseUrl('/library/bootstrap/css/bootstrap.css'));
        $this->view->headScript()->appendFile($this->view->baseUrl('/library/bootstrap/js/bootstrap.min.js'));
    }

    public function jqueryValidation() {
        $this->view->headLink()->appendStylesheet($this->view->baseUrl('/library/validationEngine2.1/css/validationEngine.jquery.css'));
        $this->view->headScript()->appendFile($this->view->baseUrl('/library/validationEngine2.1/jquery.validationEngine.js'));
        $this->view->headScript()->appendFile($this->view->baseUrl('/library/validationEngine2.1/languages/jquery.validationEngine-pt.js'));
    }

    public function jqueryCycle() {
        $this->view->headScript()->appendFile($this->view->baseUrl('/library/jquery.cycle.min.js'));
    }
    public function skrollr() {
        $this->view->headScript()->appendFile($this->view->baseUrl('/library/skrollr/skrollr.min.js'));
    }

    public function jqueryCycle2() {
        $this->view->headScript()->appendFile($this->view->baseUrl('/library/cycle2/jquery.cycle2.min.js'));
        $this->view->headScript()->appendFile($this->view->baseUrl('/library/cycle2/jquery.cycle2.swipe.min.js'));
        $this->view->headScript()->appendFile($this->view->baseUrl('/library/cycle2/jquery.cycle2.scrollVert.min.js'));
        $this->view->headScript()->appendFile($this->view->baseUrl('/library/cycle2/jquery.cycle2.carousel.min.js'));
    }

    public function easing() {
        $this->view->headScript()->appendFile($this->view->baseUrl('/library/easing/jquery.easing.1.3.js'));
    }

    public function jqueryTools() {
        $this->view->headLink()->appendStylesheet($this->view->baseUrl('/library/jqueryTools/scrollable-horizontal.css'));
        $this->view->headLink()->appendStylesheet($this->view->baseUrl('/library/jqueryTools/tools.css'));
        $this->view->headScript()->appendFile($this->view->baseUrl('/library/jqueryTools/jquery.tools.min.js'));
    }

    public function jqueryUi() {
        $this->view->headLink()->appendStylesheet($this->view->baseUrl('/library/jquery-ui-1.10.2.custom/css/smoothness/jquery-ui-1.10.2.custom.min.css'));
        $this->view->headScript()->appendFile($this->view->baseUrl('/library/jquery-ui-1.10.2.custom/ui.js'));
    }

    public function jqueryPrettyPhoto() {
        $this->view->headLink()->appendStylesheet($this->view->baseUrl('/library/prettyPhoto/css/prettyPhoto.css'));
        $this->view->headScript()->appendFile($this->view->baseUrl('/library/prettyPhoto/js/jquery.prettyPhoto.js'));
    }

    function jShadowbox() {
        $this->view->headLink()->prependStylesheet($this->view->baseUrl('/library/shadowbox-3.0.3/shadowbox.css'));
        $this->view->headScript()->appendFile($this->view->baseUrl('/library/shadowbox-3.0.3/shadowbox.js'));
    }

    public function jCKEditor() {
        $this->view->headScript()->appendFile($this->view->baseUrl('/library/ckeditor/ckeditor.js'));
    }

    public function jUploadfy() {
        $this->view->headLink()->appendStylesheet($this->view->baseUrl('/library/uploadify/uploadify.css'));
        $this->view->headScript()->appendFile($this->view->baseUrl('/library/uploadify/jquery.uploadify.min.js'));
    }

    public function jUploadfive() {
        $this->view->headLink()->appendStylesheet($this->view->baseUrl('/library/uploadifive/uploadifive.css'));
        $this->view->headScript()->appendFile($this->view->baseUrl('/library/uploadifive/jquery.uploadifive.min.js'));
    }

    public function jqueryTree() {
        $this->view->headScript()->appendFile($this->view->baseUrl('/library/jtree/jquery.tree.js'));
    }

    public function jqueryBackgroundPosition() {
        $this->view->headScript()->appendFile($this->view->baseUrl('/library/jquery.backgroundPosition.js'));
    }

    public function jqueryMask() {
        //http://digitalbush.com/projects/masked-input-plugin/
        $this->view->headScript()->appendFile($this->view->baseUrl('/library/jquery.mask.min.js'));
    }

    public function gMaps() {
        $this->view->headScript()->appendFile('http://maps.google.com/maps/api/js?sensor=false&language=pt-BR&region=BR');
        $this->view->headScript()->appendFile($this->view->baseUrl('/library/gmaps.js'));
    }

    public function jMeioMask() {
        $this->view->headScript()->appendFile($this->view->baseUrl('/library/meiomask.js'));
    }

    public function jTag() {
        $this->view->headLink()->appendStylesheet($this->view->baseUrl('/library/tagManage/bootstrap-tagmanager.css'));
        $this->view->headScript()->appendFile($this->view->baseUrl('/library/tagManage/bootstrap-tagmanager.js'));
    }

    public function jCharCount() {
        $this->view->headScript()->appendFile($this->view->baseUrl('/library/charCount/charCount.js'));
        $this->view->headLink()->appendStylesheet($this->view->baseUrl('/library/charCount/css/charCount.css'));
    }

    public function jDateTimePiker() {
        $this->view->headScript()->appendFile($this->view->baseUrl('/library/datetimepicker/jquery.datetimepicker.js'));
        $this->view->headLink()->appendStylesheet($this->view->baseUrl('/library/datetimepicker/jquery.datetimepicker.css'));
    }

    public function jSortable() {
        //$this->view->headLink()->appendStylesheet($this->view->baseUrl('/library/sortable/themes/base/jquery.ui.all.css'));
        $this->view->headScript()->appendFile($this->view->baseUrl('/library/sortable/ui/jquery.ui.core.js'));
        $this->view->headScript()->appendFile($this->view->baseUrl('/library/sortable/ui/jquery.ui.widget.js'));
        $this->view->headScript()->appendFile($this->view->baseUrl('/library/sortable/ui/jquery.ui.mouse.js'));
        $this->view->headScript()->appendFile($this->view->baseUrl('/library/sortable/ui/jquery.ui.sortable.js'));
    }
    
    public function jModernizr(){
        $this->view->headScript()->appendFile($this->view->baseUrl('/library/modernizr/modernizr.js'));
    }
    
    public function jsColor(){
        $this->view->headScript()->appendFile($this->view->baseUrl('/library/jscolor/jscolor.js'));
    }
            
}
