<?php

class Business_Imagem {

    public $url;
    public $_oArquivo;

    function __construct($id) {
        $this->size = array(71, 150, 300, 400, 696, 955);
        $this->id = $id;
        $this->dbArquivo = new Db_ArqArquivo();
        $this->oArquivo = $this->dbArquivo->setPadrao(false)->fetchRow(array('ID =?' => $id));


        $url = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT']);


        if ($this->oArquivo->DATA) {
            $data = new ZC_Date();
            $ano = $data->render($this->oArquivo->DATA, "yyyy");
            $mes = $data->render($this->oArquivo->DATA, "MM");
            $this->url = '/upload/arq_arquivo/' . $ano . '/' . $mes . "/";
        } else {
            $this->url = '/upload/arq_arquivo/';
        }
        $this->path = $url;
        $this->_oArquivo = $this->oArquivo;
    }

    function getUrl($size = null) {
        $oImagem = $this->_oArquivo;
        $nome = ($size) ? $this->id . '-' . $size . '.' . $oImagem->EXT : $this->id . '.' . $oImagem->EXT;
        return $this->url . $nome;
    }

    function getImagem($vMaxSize = null, $valign = 'center', $link = null) {
        if ($link) {
            $image = $link;
            $sTamanho = 'width="' . $vMaxSize[0] . '" height="' . $vMaxSize[1] . '"';
        } else {
            $image = $this->getProxImage(max($vMaxSize));
            if (!is_file($this->path . $image)) {
                return false;
            }
            if ($vMaxSize) {
                $vSize = $this->resizeMaior($vMaxSize, $image);
                $sTamanho = 'width="' . $vSize[0] . '" height="' . $vSize[1] . '"';
            }
        }

        $sImagem = (is_array($vMaxSize)) ? '<div class="img" style="width:' . $vMaxSize[0] . 'px;height:' . $vMaxSize[1] . 'px;overflow: hidden;">' : '<div>';
        switch ($valign) {
            case 'top':
                $sImagem .= '<img src="' . $image . '" ' . $sTamanho . ' style="margin-left:' . $vSize[2] . 'px;margin-top:0px"/>';
                break;
            case 'bottom':
                $sImagem .= '<img src="' . $image . '" ' . $sTamanho . ' style="margin-left:' . $vSize[2] . 'px;margin-top:' . ($vSize[3] * 2) . 'px"/>';
                break;
            default:
                $sImagem .= '<img src="' . $image . '" ' . $sTamanho . ' style="margin-left:' . $vSize[2] . 'px;margin-top:' . $vSize[3] . 'px"/>';
                break;
        }

        $sImagem .= '</div>';
        return $sImagem;
    }

    function resizeMaior($vMaxSize, $image) {

        list($img_w, $img_h) = getimagesize($this->path . $image);
        $nProporcao = ($vMaxSize[0] / $img_w);
        $w = $img_w * $nProporcao;
        $h = $img_h * $nProporcao;
        if ($h < $vMaxSize[1]) {
            $nProporcao = ($vMaxSize[1] / $img_h);
            $w = $img_w * $nProporcao;
            $h = $img_h * $nProporcao;
        }
        $sw = ($vMaxSize[0] - $w) / 2;
        $sh = ($vMaxSize[1] - $h) / 2;
        return array($w, $h, $sw, $sh);
    }

    function resizeMenor($nMaxSize, $image) {
        list($img_w, $img_h) = getimagesize($this->path . $image);
        if ($img_w < $img_h) {
            $nProporcao = 100 - (100 * $nMaxSize / $img_w);
            $h = floor($img_h - ($img_h * $nProporcao / 100));
            $w = $nMaxSize;
        } else {
            $nProporcao = 100 - (100 * $nMaxSize / $img_h);
            $w = floor($img_w - ($img_w * $nProporcao / 100));
            $h = $nMaxSize;
        }
        return array($w, $h);
    }

    public function getProxImage($size = null) {
        $oImage = $this->dbArquivo->setPadrao(false)->fetchRow(array('ID =?' => $this->id));
        if ($size) {
            $size = $this->getProxSize($size);
            $nome = ($size) ? $oImage->ID . '-' . $size . '.' . $oImage->EXT : $oImage->ID . '.' . $oImage->EXT;
        } else {
            $nome = $oImage->ID . '.' . $oImage->EXT;
        }
        return $this->url . $nome;
    }

    protected function getProxSize($size) {
        foreach ($this->size as $key => $value) {
            if ($value >= $size) {
                $nA = $value;
                $nB = $key <> 0 ? $this->size[$key - 1] : $value;
                $nMedia = ($nA + $nB) / 2;
                if ($size < ($nMedia * 1.5)) {
                    return $nA;
                } else {
                    return $nB;
                }
            }
        }
        return null;
    }

}
