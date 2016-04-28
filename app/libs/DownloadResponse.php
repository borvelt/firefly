<?php

class DownloadResponse
{
    public static function responseAsImage($path) {
        ob_clean();
        header("Content-Type: application/zip");
        header('Content-Disposition: attachment; filename="' . addslashes(basename($path)) . '"');
        header("Content-Length: " . filesize($path));
        readfile($path);
        exit;
    }

}
