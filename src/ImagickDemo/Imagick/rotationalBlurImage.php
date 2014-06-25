<?php

namespace ImagickDemo\Imagick;

class rotationalBlurImage extends \ImagickDemo\Example {

    function getOriginalImage() {
        return $this->control->getURL().'&original=true';
    }
    
    function render() {
        $output = "I have no idea how this is different from radialBlurImage. radialBlur is deprecated in ImageMagick";
        $output .= $this->renderImageURL();
        return $output;
    }
}