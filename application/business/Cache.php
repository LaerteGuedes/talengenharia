<?php

class Business_Cache {

    private function loadConfig() {
        $dbPagina = new Db_PagPagina();

        $cache = $this->getCache(3);

        $vo = $cache->load('config');

        $data = ZC_Util::getData();
        if ($data['debug']) {
            $dbPagina->debug();
        }

        if ($vo === false || $data['limpa']) {
            $cache->clean(Zend_Cache::CLEANING_MODE_ALL);
            unset($vo);
            $vConfFav["DESTAQUE_FAVICON"] = $vConfMan["DESTAQUE_MANUTENCAO"] = $vConfMail["DESTAQUE_MAIL_TOPO"] = array();
            $dbConf = new Db_Config();
            $oConf = $dbConf->fetchRow("ID = 1");
            $oImgConfMan = $oConf->getDestaque("config_manutencao");
            if ($oImgConfMan->_oArquivo->ID) {
                $vConfMan = array(
                    'manutencao' => $oImgConfMan->getUrl(),
                    'manutencao_ext' => $oImgConfMan->_oArquivo->EXT
                );
            }
            $oImgConfFav = $oConf->getDestaque("config_favicon");
            if ($oImgConfFav->_oArquivo->ID) {
                $vConfFav = array(
                    'favicon' => $oImgConfFav->getUrl(),
                    'favicon_ext' => $oImgConfFav->_oArquivo->EXT
                );
            }
            
            $oImgConfMail = $oConf->getDestaque("config_mail_topo");
            if ($oImgConfMail->_oArquivo->ID){
                $vConfMail = array(
                    'mail_topo' => $oImgConfMail->getUrl(),
                    'mail_topo_ext' => $oImgConfMail->_oArquivo->EXT 
                );
            }
            
            $vo['config'] = $oConf->toArray();
            $vo['config'] = $vo['config'] + $vConfFav + $vConfMan + $vConfMail;
           
            $cache->save($vo['config'], 'config');
        }
    }

    public function get($item) {
        $this->loadConfig();
        $cache = $this->getCache(3);

        $vv = $cache->load($item);
        return $vv;
    }

    static function partial($file, $params = null, $time = 120, $distincttitle = null) {
        $bHead = new Business_Head();
        if ($file == '_widgets/videos.phtml' || $file == '_widgets/imagemdasemana.phtml') {
            $bHead->jqueryCycle();
        }
        $cache_name = str_replace('-', '', ZC_Util::cleanForShortURL($file));
        $cache_name_distinct = ($distincttitle) ? str_replace('-', '', ZC_Util::cleanForShortURL($distincttitle)) : '';
        $cache_name = $cache_name . $cache_name_distinct;
        $cache = Business_Cache::getCache($time);
        $vCache = $cache->load($cache_name);
        $data = ZC_Util::getData();

        if ($vCache == false || $data['limpa']) {
            $view = Zend_Registry::get('view');
            $vCache = $view->partial($file, $params);
            $cache->save($vCache, $cache_name);
            return $vCache;
        } else {
            return $vCache;
        }
    }

    static function getCache($minuteLifetime = 120) {
        $frontendOptions = array(
            'lifetime' => $minuteLifetime * 60, // cache lifetime of 2 hours
            'automatic_serialization' => true
        );
        $backendOptions = array('cache_dir' => APPLICATION_PATH . '/../cache/');
        return Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
    }

}
