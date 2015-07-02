<?php

class Business_Util {

    public $url;

    function __construct($id) {
        
    }

    function debug($var, $exitable = true) {
        echo"<pre>";
        $var = print_r($var);
        if ($exitable):
            exit();
        endif;
        return $var;
    }

    function convertData($data, $banco = false) {
        $bData = new ZC_Date();
        if ($banco):
            $dataConvertida = $bData->toDb($data);
        else:
            $dataConvertida = $bData->toString($data);
        endif;
        return $dataConvertida;
    }

    function showGalleryByArray($voPagina, array $size, $divClass="", $divImageClass="", $galeriaClass="", $idIterator=false) {
        echo '<div class="'.$galeriaClass.'">';
        foreach ($voPagina as $oPagina) {
            echo ($idIterator ? "<div class='".$divClass."' id='SHOW".$oPagina->ID."'>" : "<div class='" . $divClass . "'>");
            $oPagina->getArquivo();
            $arquivos = $oPagina->getArquivo();

            foreach ($arquivos as $arquivo) {
                echo "<div class='" . $divImageClass . "'>" . $$arquivo->renderImagem($size) . '</div>';
            }
            echo "</div>";
        }
        echo "</div>";
    }
    
    function showGalleryList($voPagina, array $size, $ulClass="", $liClass="",$galeriaClass="", $idIterator=false){
        echo '<div class="'.$galeriaClass.'">';
        foreach ($voPagina as $oPagina) {
            echo ($idIterator ? "<ul class='".$ulClass."' id='SHOW".$oPagina->ID."'>" : "<ul class='" . $ulClass . "'>");
            $oPagina->getArquivo();
            $arquivos = $oPagina->getArquivo();

            foreach ($arquivos as $arquivo) {
                echo "<li class='" . $divImageClass . "'>" . $$arquivo->renderImagem($size) . '</div>';
            }
            echo "</div>";
        }
        echo "</div>";
    }

    function showGalleryByPagina($oPagina, array $size, $divClass = "", $divImageClass = "", $galeriaClass="") {
        echo '<div class="'.$galeriaClass.'">';
        $arquivos = $oPagina->getArquivo();

        foreach ($arquivos as $arquivo) {
            echo "<div class='" . $divClass . "'><div class='" . $divImageClass . "'>" . $$arquivo->renderImagem($size) . '</div></div>';
        }
         echo "</div>";
    }

    function getData() {
        return Zend_Controller_Front::getInstance()->getRequest()->getParams();
    }

    function getExt($ext) {
        switch ($ext) {
            case 'docx' :
            case 'xlsx' :
            case 'pptx' :
                $arq = 'doc';
                break;
            case 'pdf':
                $arq = 'pdf';
                break;
            case 'avi':
            case 'mp4':
            case 'mpeg':
            case 'mpeg2':
                $arq = 'video';
                break;
            case 'mp3':
            case 'wma':
            case 'ogg':
            case 'wave':
                $arq = 'audio';
                break;
            default:
                $arq = 'arquivo';
                break;
        }
        return $arq;
    }
    
     public static function showClassMethods($object, $exit = true){
        $methods = get_class_methods($object);
        foreach ($methods as $method){
            echo $method.'<br/>';
        }
        $exit ? exit() : '';
    }

}
