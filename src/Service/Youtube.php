<?php

namespace App\Service;

use Symfony\Component\Yaml\Yaml;
use \Exception;

class Youtube
{
    private $key;

    public function __construct()
    {
        $this->key = Yaml::parseFile('/var/www/html/config/secrets.yaml')['youtubeApiKey'];
    }

    public function videoLength($videoId)
    {
        $data = @file_get_contents("https://www.googleapis.com/youtube/v3/videos?id={$videoId}&part=contentDetails&key={$this->key}");
        if ($data === false) {
            throw new Exception("Unable to load {$videoId} using credentials {$this->key};\n" . error_get_last()['message']);
        }
        $obj = json_decode($data);
        $pt = $obj->items[0]->contentDetails->duration;
        return $this->ISO8601ToSeconds($pt);
    }
    
    private function ISO8601ToSeconds(string $ISO8601)
    {
        $interval = new \DateInterval($ISO8601);

        return ($interval->d * 24 * 60 * 60) +
        ($interval->h * 60 * 60) +
        ($interval->i * 60) +
        $interval->s;
    }
}
