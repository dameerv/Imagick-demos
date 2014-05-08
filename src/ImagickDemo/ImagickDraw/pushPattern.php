<?php

namespace ImagickDemo\ImagickDraw;

class pushPattern extends ImagickDrawExample {

    function renderDescription() {
        return "";
    }

    function renderImage() {

//Create a ImagickDraw object to draw into.
        $draw = new \ImagickDraw();

        $strokeColor = new \ImagickPixel($this->strokeColor);
        $fillColor = new \ImagickPixel($this->fillColor);


        $draw->setStrokeColor($strokeColor);
        $draw->setFillColor($fillColor);

        $draw->setStrokeWidth(1);

        $draw->setStrokeOpacity(1);
        $draw->setStrokeColor($strokeColor);
        $draw->setFillColor($fillColor);

        $draw->setStrokeWidth(1);

        $draw->pushPattern("MyFirstPattern", 0, 0, 50, 50);
        for ($x = 0; $x < 50; $x += 10) {
            for ($y = 0; $y < 50; $y += 5) {
                $positionX = $x + (($y / 5) % 5);
                $draw->rectangle($positionX, $y, $positionX + 5, $y + 5);
            }
        }
        $draw->popPattern();

        $draw->setFillOpacity(0);
        $draw->rectangle(100, 100, 400, 400);
        $draw->setFillOpacity(1);

        $draw->setFillOpacity(1);

        $draw->push();
        $draw->setFillPatternURL('#MyFirstPattern');
        $draw->setFillColor('yellow');
        $draw->rectangle(100, 100, 400, 400);
        $draw->pop();


//Create an image object which the draw commands can be rendered into
        $imagick = new \Imagick();
        $imagick->newImage(500, 500, $this->backgroundColor);
        $imagick->setImageFormat("png");

//Render the draw commands in the ImagickDraw object 
//into the image.
        $imagick->drawImage($draw);

//Send the image to the browser
        header("Content-Type: image/png");
        echo $imagick->getImageBlob();


    }
}
 