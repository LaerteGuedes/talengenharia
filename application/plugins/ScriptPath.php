<?php
class Plugin_ScriptPath extends Zend_Controller_Plugin_Abstract{
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {
		
//        $view = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
//        $view->setViewBasePathSpec(':moduleDir/views');
//        $view = Zend_Controller_Action_HelperBroker::getExistingHelper('ViewRenderer')->view;
//        $scriptPath = sprintf('%s/modules/%s/views',
//                APPLICATION_PATH,
//                $request->getModuleName()
//				);
//        if (file_exists($scriptPath)) {
//            $view->addScriptPath($scriptPath);
//        }
    }
}