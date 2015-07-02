<?php

class Business_File {

    public $file_tmp;
    public $file_name;
    public $pasta;
    public $vResize;

    function __construct($file = null, $pasta = null) {
        if ($file && $pasta) {
            $this->setConfig($file, $pasta);
        }
    }

    public function setConfig($file, $pasta, $id = '') {



        $tipos = array('jpg', 'png', 'jpeg', 'gif');
        if (in_array(strtolower(end(explode(".", $file['name']))), $tipos)) {
            $image = 'image';
        } else {
            $image = current(explode('/', $this->file_mime));
        }

        #### CRIA REPOSITORIO DE IMAGENS POR ANO E MES - Felipe (04/2014)
        $dirAno = $_SERVER['DOCUMENT_ROOT'] . $pasta . '/' . date('Y');
        $dirMes = $_SERVER['DOCUMENT_ROOT'] . $pasta . '/' . date('Y') . '/' . date('m');

        if (!is_dir($dirAno)) {
            mkdir($dirAno, 0775);
        }
        if (!is_dir($dirMes)) {
            mkdir($dirMes, 0775);
        }

        $this->pasta = str_replace('//', '/', $dirMes . "/");
        $this->file_tmp = $file['tmp_name'];
        $this->file_ext = strtolower(end(explode(".", $file['name'])));
        $this->file_mime = $this->get_mime($file['tmp_name']);
        $this->file_type = $image;
        $this->file_id = $id;
        $this->file_name = $id . '.' . $this->file_ext;
    }

    function setId($id) {
        $this->file_id = $id;
        $this->file_name = $id . '.' . $this->file_ext;
    }

    public function upload($true) {

        copy($this->file_tmp, $this->pasta . $this->file_name);

        return $this;
    }

    public function multiResize($vResize) {
        if (count($vResize)):
            foreach ($vResize as $nTamanho):
                $this->resize($nTamanho);
            endforeach;
        endif;
    }

    public function multiResizeCrop($vResize, $crop) {
        if (count($vResize)):
            foreach ($vResize as $nTamanho):
                $this->resizeCrop($nTamanho, $crop);
            endforeach;
        endif;
    }

    function resizeCrop($data) {

        $img_origem = $data["img_original"]["tmp_name"];
        $pasta_destino = $this->pasta;

        
//        Business_Util::debug($pasta_destino);
        
        
        $Arquivo = pathinfo($img_origem);
        $ArquivoSize = getimagesize($img_origem);

        $ArqOriginalWidth = $ArquivoSize[0];
        $ArqOriginalHeight = $ArquivoSize[1];

        switch ($Arquivo['extension']) {
            case "jpeg":
            case "jpg":
            case "pjpeg":
                $iFotoOriginal = $img = imagecreatefromjpeg($img_origem);
                break;
            case 'gif':
                $iFotoOriginal = $img = imagecreatefromgif($img_origem);
                break;
            case 'png':
                $iFotoOriginal = $img = imagecreatefrompng($img_origem);
                break;
        }

        list($width, $height) = getimagesize($img_origem);
        $iFotoFinal = imagecreatetruecolor($data['w'], $data['h']);

        $x = $data['x'] * -1;
        $x2 = $data['x2'];
        $y = $data['y'] * -1;
        $y2 = $data['y2'];
        $w = round($data['w']);
        $h = round($data['h']);

//        Business_Util::debug($data);

        imagecopyresampled($iFotoFinal, $iFotoOriginal, $x, $y, 0, 0, $ArqOriginalWidth, $ArqOriginalHeight, $ArqOriginalWidth, $ArqOriginalHeight);

        $file_name = $nome = $this->file_id . '.' . $data["EXT"];

        switch ($Arquivo['extension']) {
            case "jpeg":
            case "jpg":
            case "pjpeg":
                imagejpeg($iFotoFinal, $pasta_destino . $file_name, 79);
                break;
            case 'gif':
                imagegif($iFotoFinal, $pasta_destino . "/" . $file_name);
                break;
            case 'png':
                imagesavealpha($iFotoOriginal, true);
                $newImage = imagecreatetruecolor($data['w'], $data['h']);
                $background = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
                imagecolortransparent($newImage, $background);
                imagealphablending($newImage, false);
                imagesavealpha($newImage, true);
                imagecopyresampled($newImage, $iFotoOriginal, ($data['x'] * -1), ($data['y'] * -1), 0, 0, $width, $height, $width, $height);
                $iFotoOriginal = $newImage;
                imagepng($iFotoOriginal, $pasta_destino . "/" . $file_name);
                break;
            default :
                imagejpeg($iFotoFinal, $pasta_destino . "/" . $file_name, 79);
                break;
        }
    }

    function resize($nTamanho, $rename = true) {

        $img_origem = $this->pasta . $this->file_name;

        $pasta_destino = $this->pasta;

        $Arquivo = pathinfo($img_origem);

        switch ($Arquivo['extension']) {
            case "jpeg":
            case "jpg":
            case "pjpeg":
                $iFotoOriginal = $img = imagecreatefromjpeg($img_origem);
                break;
            case 'gif':
                $iFotoOriginal = $img = imagecreatefromgif($img_origem);
                break;
            case 'png':

                $iFotoOriginal = $img = imagecreatefrompng($img_origem);

                break;
        }
        $nLarguraOrg = $vDimensao['largura'] = imagesx($img);
        $nAlturaOrg = $vDimensao['altura'] = imagesy($img);

        if ($nLarguraOrg > $nTamanho || $nAlturaOrg > $nTamanho):
            //comentario referente a um calculo com $nTamanho = 60
            if ($nLarguraOrg < $nAlturaOrg) {
                //alt = 100 / larg = 200;
                $nProporcao = 100 - (100 * $nTamanho / $nLarguraOrg); //70
                $nAltura = floor($nAlturaOrg - ($nAlturaOrg * $nProporcao / 100)); //30
                $vDimensao['largura'] = $nTamanho;
                $vDimensao['altura'] = $nAltura;
            } else {
                //alt = 200 / larg = 100;
                $nProporcao = 100 - (100 * $nTamanho / $nAlturaOrg); //70
                $nLargura = floor($nLarguraOrg - ($nLarguraOrg * $nProporcao / 100)); //30
                $vDimensao['largura'] = $nLargura;
                $vDimensao['altura'] = $nTamanho;
            }
        endif;
        $iFotoFinal = imagecreatetruecolor($vDimensao['largura'], $vDimensao['altura']);
        imagecopyresampled($iFotoFinal, $iFotoOriginal, 0, 0, 0, 0, $vDimensao['largura'], $vDimensao['altura'], $nLarguraOrg, $nAlturaOrg);
        $file_name = ($rename) ? $this->file_id . '-' . $nTamanho . '.' . $this->file_ext : $this->file_name;

        switch ($Arquivo['extension']) {
            case "jpeg":
            case "jpg":
            case "pjpeg":
                imagejpeg($iFotoFinal, $pasta_destino . "/" . $file_name, 79);
                break;
            case 'gif':
                imagegif($iFotoFinal, $pasta_destino . "/" . $file_name);
                break;
            case 'png':
                imagesavealpha($iFotoOriginal, true);
                $newImage = imagecreatetruecolor($vDimensao['largura'], $vDimensao['altura']);

// Make a new transparent image and turn off alpha blending to keep the alpha channel
                $background = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
                imagecolortransparent($newImage, $background);
                imagealphablending($newImage, false);
                imagesavealpha($newImage, true);
                imagecopyresampled($newImage, $iFotoOriginal, 0, 0, 0, 0, $vDimensao['largura'], $vDimensao['altura'], $nLarguraOrg, $nAlturaOrg);
                $iFotoOriginal = $newImage;
                imagepng($iFotoOriginal, $pasta_destino . "/" . $file_name);
                break;
            default :
                imagejpeg($iFotoFinal, $pasta_destino . "/" . $file_name, 79);
                break;
        }
    }

    function get_mime($filename) {
        preg_match("/\.(.*?)$/", $filename, $m);
        switch (strtolower($m[1])) {
            case "js": return "application/javascript";
            case "json": return "application/json";
            case "jpg": case "jpeg": case "jpe": return "image/jpg";
            case "png": case "gif": case "bmp": return "image/" . strtolower($m[1]);
            case "css": return "text/css";
            case "xml": return "application/xml";
            case "html": case "htm": case "php": return "text/html";
            default:
                if (function_exists("mime_content_type")) { # if mime_content_type exists use it. 
                    $m = mime_content_type($filename);
                } else if (function_exists("")) {    # if Pecl installed use it 
                    $finfo = finfo_open(FILEINFO_MIME);
                    $m = finfo_file($finfo, $filename);
                    finfo_close($finfo);
                } else {    # if nothing left try shell 
                    if (strstr($_SERVER[HTTP_USER_AGENT], "Windows")) { # Nothing to do on windows 
                        return ""; # Blank mime display most files correctly especially images. 
                    }
                    if (strstr($_SERVER[HTTP_USER_AGENT], "Macintosh")) { # Correct output on macs 
                        $m = trim(exec('file -b --mime ' . escapeshellarg($filename)));
                    } else {    # Regular unix systems 
                        $m = trim(exec('file -bi ' . escapeshellarg($filename)));
                    }
                }
                $m = explode(";", $m);
                return trim($m[0]);
        }
    }

}
