<?php
class Plugin_LanguageRouteDetector extends Zend_Controller_Plugin_Abstract
{
    protected $_supportedLanguages = array('pt', 'en', 'es');
    protected $_regions = array('pt' => 'BR', 'en' => 'US', 'es' => 'ES');
 
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $lang = $request->getParam('lang', '');
 
        $zendLocale = Zend_Registry::get('Zend_Locale');
        $zendTranslate = Zend_Registry::get('Zend_Translate');
 
        $locale = '';
 
        if (!in_array($lang, $this->_supportedLanguages)) {
            $locale = 'pt_BR';
        } else {
            $locale = $lang . '_' . $this->_regions[$lang];
        }
 
        $zendLocale->setLocale($locale);
        $zendTranslate->setLocale($locale);
 
        Zend_Registry::set('Zend_Locale', $zendLocale);
        Zend_Registry::set('Zend_Translate', $zendTranslate);
    }
}