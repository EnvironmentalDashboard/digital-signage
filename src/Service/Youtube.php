<?php

namespace App\Service;

use Symfony\Component\Yaml\Yaml;
use \Exception;

class Youtube
{
	private $key;

    public function __construct() {
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
		$pt = substr($pt, 2);
		$parts = explode('M', $pt);
		if (is_numeric($parts[0])) { // e.g. PT21M21S
			return (int) ($parts[0] * 60) + rtrim($parts[1], 'S');
		} else { // e.g. PT10S
			return (int) rtrim($parts[0], 'S');
		}
    }
}
