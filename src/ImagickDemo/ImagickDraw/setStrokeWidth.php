<?php

namespace ImagickDemo\ImagickDraw;

class setStrokeWidth extends ImagickDrawExample {

    function renderDescription() {
        return "";
    }

    function renderImage() {

//Create a ImagickDraw object to draw into.
        $draw = new \ImagickDraw();


        $draw->setStrokeWidth(1);

        $strokeColor = new \ImagickPixel($this->strokeColor);
        $fillColor = new \ImagickPixel($this->fillColor);

        $draw->setStrokeColor($strokeColor);
        $draw->setFillColor($fillColor);

//$draw->setStrokeWidth(1);
        $draw->line(100, 100, 400, 145);
        $draw->rectangle(100, 200, 225, 350);

        $draw->setStrokeWidth(5);
        $draw->line(100, 120, 400, 165);
        $draw->rectangle(275, 200, 400, 350);


//Create an image object which the draw commands can be rendered into
        $image = new \Imagick();
        $image->newImage(500, 400, $this->backgroundColor);
        $image->setImageFormat("png");

//Render the draw commands in the ImagickDraw object 
//into the image.
        $image->drawImage($draw);

//Send the image to the browser
        header("Content-Type: image/png");
        echo $image->getImageBlob();

    }

}




 