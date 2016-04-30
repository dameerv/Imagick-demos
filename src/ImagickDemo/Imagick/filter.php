<?php

namespace ImagickDemo\Imagick;

class filter extends \ImagickDemo\Example
{
    function getOriginalImage()
    {
        return $this->control->getOriginalURL();
    }

    function getOriginalFilename()
    {
        return $this->control->getImagePath();
    }

    public function renderTitle()
    {
        return "Filter";
    }

    public function render()
    {
        return $this->renderImageURL();
    }
}
