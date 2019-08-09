<?php

namespace App\Service;

use App\Entity;
use \Exception;

class ButtonManager
{
    public function create($type, $display, $frame, $controller, $image, $url)
    {
        $button = new Entity\Button;
        $button->setTwigKey('btn1'); // tmp value to be set later

        if ($type === Entity\Button::TRIGGER_FRAME) {
            if ($frame === null || $controller === null || $image === null) {
                throw new Exception("Missing fields: need to POST 'buttonFrameSelect', 'controllerId', 'file'");
            }
            $imageName = $this->saveImage($image);
        } elseif ($type === Entity\Button::PLAY) {
            if ($controller === null) {
                throw new Exception("Missing fields: need to POST 'controllerId'");
            }
            $imageName = 'play.svg';
        } elseif ($type === Entity\Button::TRIGGER_URL) {
            if ($controllerId === null) {
                throw new Exception("Missing fields: need to POST 'controllerId', 'UrlSelect', 'file'");
            }
            $imageName = $this->saveImage($image);
        } else {
            throw new Exception("Unknown button type {$type}");
        }

        $button->setOnDisplay($display);
        $button->setTriggerFrame($frame);
        $button->addController($controller);
        $button->setType($type);
        $button->setTriggerUrl($url);
        $button->setImage($imageName);

        return $button;
    }

    private function saveImage($image)
    {
        if ($image->isValid()) {
            $path = '/var/www/html/public/uploads/';
            $name = $image->getClientOriginalName();
            if (file_exists($path . $name)) {
                $name = uniqid() . '.' . $image->guessClientExtension();
            }
            $image->move($path, $name);
            return $name;
        }
    }
}
