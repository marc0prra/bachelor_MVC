<?php

require_once("models/Media.php");

class MediaController{
    static function library() {
        $medias = Media::getMedias();
        require_once('views/library.html');
    }
}
