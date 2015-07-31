<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    //Função que faz o auload das classes. O nome da classe deve ser definido conforme o diretorio ex: /db/AclGrupo.php a classe será new Db_AclGrupo();
    protected function _initAutoload() {
        //cria alguns autoloads customizados
        $autoloader = new Zend_Application_Module_Autoloader(array(
            'namespace' => '',
            'basePath' => dirname(__FILE__),
            'resourceTypes' => array(
                'business' => array(
                    'path' => 'business/',
                    'namespace' => 'Business'
                ),
                'db' => array(
                    'path' => 'db/',
                    'namespace' => 'Db'
                ),
                'filter' => array(
                    'path' => 'filter/',
                    'namespace' => 'Filter'
                )
            )
        ));
    }

    protected function _initSetDefaultMetatags() {
        $view = $this->bootstrap('view')->getResource('view');
        $view->ogImage = '/imagem/logo-mini.jpg';
    }

    protected function _initCss() {
        if (APPLICATION_ENV !== "libra") {
            return;
        }
        require_once APPLICATION_PATH . "/library_php/lessphp/lessc.inc.php";

        if ($handle = opendir(APPLICATION_PATH . "/../public_html/css/less")) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    $sLess = APPLICATION_PATH . "/../public_html/css/less/" . $entry;
                    if (pathinfo($sLess, PATHINFO_EXTENSION) == 'less') {
                        $cssFile = str_replace('less', 'css', $entry);
                        $sCss = APPLICATION_PATH . "/../public_html/css/" . $cssFile;
                        $oLessc = new lessc($sLess);
                        file_put_contents($sCss, $oLessc->parse());
                    }
                }
            }
            closedir($handle);
        }
    }

    protected function _initLocale() {
        $registry = Zend_Registry::getInstance();
        $locale = new Zend_Locale('pt_BR');
        $registry->set('Zend_Locale', $locale);
    }

    protected function _initDoctype() {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        //CRIA UM REGISTOR DO VIEW PARA DISPONIBILIZA-LO DE FORMA FACIL EM TODA A APLICAÇÃO
        Zend_Registry::set('view', $view);
    }

    protected function _initPlugins() {

        $bootstrap = $this->getApplication();
        if ($bootstrap instanceof Zend_Application) {
            $bootstrap = $this;
        }
        $bootstrap->bootstrap('FrontController');
        $front = $bootstrap->getResource('FrontController');
        $front->registerPlugin(new Plugin_ScriptPath());
        $front->registerPlugin(new Plugin_Inicializacao());
        $front->registerPlugin(new Plugin_LanguageRouteDetector());
    }

    protected function _initRouter() {
        $this->bootstrap('FrontController');
        $front = $this->getResource('FrontController');
        $front->addModuleDirectory(APPLICATION_PATH . '/modules');
        $router = $front->getRouter();

        $router->addRoute(
                'language', new Zend_Controller_Router_Route(
                ':lang/:controller/:action/*', array(
            'lang' => 'pt',
            'controller' => 'index',
            'action' => 'index',
            'module' => 'site'
                ), array(
            'lang' => '[a-z]{2}',
                )
        ));
        $router->addRoute(
                'language', new Zend_Controller_Router_Route(
                '/busca/*', array(
            'module' => 'site',
            'controller' => 'pagina',
            'action' => 'busca'
                )
        ));
        $router->addRoute(
            'empresa', new Zend_Controller_Router_Route(
            '/empresa/*', array(
                'module' => 'site',
                'controller' => 'pagina',
                'action' => 'empresa'
            )
        ));
        $router->addRoute(
                'institucional_detalhe', new Zend_Controller_Router_Route(
                '/adm/institucional/:pagina', array(
                    'module' => 'adm',
                    'controller' => 'pagina',
                    'action' => 'institucional',
                    'pagina' => ':pagina'
                )
        ));
        $router->addRoute(
                'inserealterainstitucional_detalhe', new Zend_Controller_Router_Route(
                '/adm/inserealterainstitucional/:pagina', array(
                    'module' => 'adm',
                    'controller' => 'pagina',
                    'action' => 'inserealterainstitucional',
                    'pagina' => ':pagina'
                )
        ));

        $router->addRoute('noticia_detalhe', new Zend_Controller_Router_Route(
                'noticia/:permalink', array(
            'module' => 'site', 'controller' => 'pagina', 'action' => 'noticiadetalhe', 'permalink' => ':permalink'
                )
        ));
        $router->addRoute('pagina_detalhe', new Zend_Controller_Router_Route(
                'institucional/:permalink', array(
            'module' => 'site', 'controller' => 'pagina', 'action' => 'institucional', 'permalink' => ':permalink'
                )
        ));
        $router->addRoute('obras', new Zend_Controller_Router_Route(
            'obras', array(
                'module' => 'site', 'controller' => 'pagina', 'action' => 'obras'
            )
        ));
        $router->addRoute('contato', new Zend_Controller_Router_Route(
                'contato', array(
            'module' => 'site', 'controller' => 'pagina', 'action' => 'contato'
                )
        ));
        $router->addRoute('novidades', new Zend_Controller_Router_Route(
                'novidades', array(
            'module' => 'site', 'controller' => 'pagina', 'action' => 'noticia'
                )
        ));
        $router->addRoute('imprensa', new Zend_Controller_Router_Route(
                'imprensa', array(
            'module' => 'site', 'controller' => 'pagina', 'action' => 'imprensa'
                )
        ));
        $router->addRoute('imprensadetalhe', new Zend_Controller_Router_Route(
                'nota/:permalink', array(
            'module' => 'site', 'controller' => 'pagina', 'action' => 'imprensadetalhe', 'permalink' => ':permalink'
                )
        ));
        $router->addRoute('produto', new Zend_Controller_Router_Route(
            'produto/:permalink', array(
                'module' => 'site', 'controller' => 'pagina', 'action' => 'produto', 'permalink' => ':permalink'
            )
        ));
        $router->addRoute('loja', new Zend_Controller_Router_Route(
                'loja', array(
            'module' => 'site', 'controller' => 'pagina', 'action' => 'loja'
                )
        ));

        $router->addRoute('download_arquivos', new Zend_Controller_Router_Route(
                'download/:id', array(
            'module' => 'adm', 'controller' => 'pagina', 'action' => 'download', 'id' => ':id'
                )
        ));
    }

    protected function _initMail() {

        $config = array(
            'auth' => 'login',
            'username' => 'teste@libradesign.com.br',
            'password' => 'adlibra200165',
            'ssl' => 'ssl',
            'port' => '465'
        );
        $mailTransport = new Zend_Mail_Transport_Smtp('smtp.gmail.com', $config);
        Zend_Mail::setDefaultTransport($mailTransport);
        Zend_Mail::setDefaultFrom('teste@libradesign.com.br', 'Libra');
    }

    protected function _initCache() {
        Zend_Db_Table_Abstract::setDefaultMetadataCache(Business_Cache::getCache(3));
    }

    protected function _initZFDebug() {
        if ($_GET['debug'] == 1) {
            $autoloader = Zend_Loader_Autoloader::getInstance();
            $autoloader->registerNamespace('ZFDebug');
            $options = array(
                'plugins' => array(
                    'Variables',
                    'File' => array('base_path' => APPLICATION_PATH),
                    'Memory',
                    'Time',
                    'Registry',
                    'Exception'
                )
            );
            if ($this->hasPluginResource('db')) {
                $this->bootstrap('db');
                $db = $this->getPluginResource('db')->getDbAdapter();
                $options['plugins']['Database']['adapter'] = $db;
            }
            if ($this->hasPluginResource('cache')) {
                $this->bootstrap('cache');
                $cache = $this - getPluginResource('cache')->getDbAdapter();
                $options['plugins']['Cache']['backend'] = $cache->getBackend();
            }

            $debug = new ZFDebug_Controller_Plugin_Debug($options);

            $this->bootstrap('frontController');
            $frontController = $this->getResource('frontController');
            $frontController->registerPlugin($debug);
        }
    }

}
