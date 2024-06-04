<?php
class ThumbImage{
    public $params = array();
    public $DST = array();
    public $SRC = array();

    public function __construct() {
    }

    public function getCreateThumb($chSrcFile, $chThumbFile, $x, $y, $imgcrop=0, $croptarget='t', $quality='90', $chWatermarkFile="", $chWPosition="") {
        $this->DST['file'] = $chThumbFile;
        $this->DST['width'] = $x;
        $this->DST['height'] = $y;

        $this->params['file'] = $chSrcFile;
        $this->params['crop'] = $imgcrop;

        $temp = getimagesize($this->params['file']);
        $this->SRC['file']		= $this->params['file'];
        $this->SRC['width']	= $temp[0];
        $this->SRC['height']	= $temp[1];
        $this->SRC['type']		= $temp['mime']; // 1=GIF, 2=JPG, 3=PNG, SWF=4
        $this->SRC['string']	= $temp[3];
        $this->SRC['filename'] 	= basename($this->params['file']);
        $this->SRC['modified'] 	= filemtime($this->params['file']);

        // 크롭일 경우
        if($this->params['crop'] and $x and $y) {
            if ($this->SRC['width'] > $this->DST['width'] || $this->SRC['height'] > $this->DST['height']) {
                $width_ratio = $this->SRC['width']/$this->DST['width'];
                $height_ratio = $this->SRC['height']/$this->DST['height'];

                // Es muss an der Breite beschnitten werden
                if ($width_ratio > $height_ratio) {
                    $this->DST['offset_w'] = round(($this->SRC['width']-$this->DST['width']*$height_ratio)/2);
                    $this->SRC['width'] = round($this->DST['width']*$height_ratio);
                // es muss an der H?e beschnitten werden
                } else if ($width_ratio < $height_ratio) {
                    if ($croptarget != "t") {
                        $this->DST['offset_h'] = round(($this->SRC['height']-$this->DST['height']*$width_ratio)/2); // 중앙 크롭
                    } else {
                        $this->DST['offset_h'] = 0; //상단 크롭
                    }
                    $this->SRC['height'] = round($this->DST['height']*$width_ratio);
                }
            } else {
                $this->DST['offset_w'] = $this->SRC['width'] = $this->SRC['width'];
                $this->DST['offset_h'] = $this->SRC['height'] = $this->SRC['height'];
            }
        } else {
            // 리사이즈일 경우
            if ($this->SRC['width'] > $this->DST['width'] || $this->SRC['height'] > $this->DST['height']) {
                $this->params['longside'] = $this->DST['width'];
                $this->params['shortside'] = $this->DST['height'];

                $temp_large=$temp[0]>$temp[1] ? $temp[0] : $temp[1];
                // 양쪽 다 있을 경우 비율로 리사이즈
                if($x and $y){
                    // 세로가 클 경우
                    if ($temp[0] < $temp[1]) {
                        $tempw = (100*$y)/$temp[1];
                        $this->DST['width']=ceil(($temp[0]*$tempw)/100);
                        $this->DST['height']=$y;
                    } else {
                        // 가로가 클 경우
                        $temph = (100*$x)/$temp[0];
                        $this->DST['width']=$x;
                        $this->DST['height']=ceil(($temp[1]*$temph)/100);
                    }
                // 한쪽이 안정해졌을경우 ( 한쪽에만 맞춤 )
                } else if(!$x || !$y) {
                    if($x){ // width 를 수치로 고정
                        $this->DST['width']=$x;
                        $this->DST['height']=ceil($this->DST['width'] * $temp[1] / $temp[0]);
                    } else{
                        $this->DST['height']=$y;
                        $this->DST['width']=ceil($this->DST['height'] * $temp[0] / $temp[1]);
                    }
                // 양쪽 다 정해졌을 경우
                } else {
                    $this->DST['width']=$x;
                    $this->DST['height']=$y;
                }
            } else {
                $this->DST['width']=$this->SRC['width'];
                $this->DST['height']=$this->SRC['height'];
            }
        }

        if ($this->SRC['type'] == "image/gif")	$this->SRC['image'] = imagecreatefromgif($this->SRC['file']);
        if ($this->SRC['type'] == "image/jpeg")	$this->SRC['image'] = imagecreatefromjpeg($this->SRC['file']);
        if ($this->SRC['type'] == "image/png")	$this->SRC['image'] = imagecreatefrompng($this->SRC['file']);

        if (!empty($this->params['type'])) $this->DST['type']	= $this->params['type'];
        else $this->DST['type']	= $this->SRC['type'];

        $this->DST['image'] = imagecreatetruecolor($this->DST['width'], $this->DST['height']);
        imagecopyresampled($this->DST['image'], $this->SRC['image'], 0, 0, $this->DST['offset_w'], $this->DST['offset_h'], $this->DST['width'], $this->DST['height'], $this->SRC['width'], $this->SRC['height']);

        //-------- 워터마크 처리일 경우 ----------//
        if ($chWatermarkFile && $chWPosition) {
            $this->getWatermark($chWatermarkFile, $chWPosition);
        }
        //-------------------------------------//
        if ($this->DST['type'] == "image/png")	@imagepng($this->DST['image'], $this->DST['file']);
        else @imagejpeg($this->DST['image'], $this->DST['file'], $quality);
        @chmod($this->DST['file'], 0606);
        imagedestroy($this->DST['image']);
        return $chThumbFile;
    }

    public function getWatermark($chWatermarkFile, $chWPosition) {
        $aWaterTemp = getimagesize($chWatermarkFile);
        $_WM['width']	= $aWaterTemp[0];
        $_WM['height']	= $aWaterTemp[1];
        $_WM['type']	= $aWaterTemp[mime]; // 1=GIF, 2=JPG, 3=PNG, SWF=4
        $_WM['string']	= $aWaterTemp[3];

        if ($_WM['type'] == "image/gif")	$_WM['image'] = imagecreatefromgif($chWatermarkFile);
        if ($_WM['type'] == "image/jpeg")	$_WM['image'] = imagecreatefromjpeg($chWatermarkFile);
        if ($_WM['type'] == "image/png")	$_WM['image'] = imagecreatefrompng($chWatermarkFile);
        switch($chWPosition) {
            case('TL') :
                $_WM['x'] = 10;
                $_WM['y'] = 10;
                break;
            case('TC') :
                $_WM['x'] = ($this->DST['width'] - $_WM['width']) / 2;
                $_WM['y'] = 10;
                break;
            case('TR') :
                $_WM['x'] = ($this->DST['width'] - $_WM['width']) - 10;
                $_WM['y'] = 10;
                break;
            case('CL') :
                $_WM['x'] = 10;
                $_WM['y'] = ($this->DST['height'] - $_WM['height']) / 2;
                break;
            case('CC') :
                $_WM['x'] = ($this->DST['width'] - $_WM['width']) / 2;
                $_WM['y'] = ($this->DST['height'] - $_WM['height']) / 2;
                break;
            case('CR') :
                $_WM['x'] = 10;
                $_WM['y'] = ($this->DST['height'] - $_WM['height']) / 2;
                break;
            case('BL') :
                $_WM['x'] = 10;
                $_WM['y'] = ($this->DST['height'] - $_WM['height']) - 10;
                break;
            case('BC') :
                $_WM['x'] = ($this->DST['width'] - $_WM['width']) / 2;
                $_WM['y'] = ($this->DST['height'] - $_WM['height']) - 10;
                break;
            case('BR') :
                $_WM['x'] = 10;
                $_WM['y'] = ($this->DST['height'] - $_WM['height']) - 10;
                break;
        }
        imagecopyresampled($this->DST['image'], $_WM['image'], $_WM['x'], $_WM['y'], 0, 0, $_WM['width'] , $_WM['height'], $_WM['width'] , $_WM['height']);
    }
}
?>