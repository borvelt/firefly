<?php

class DownloadResponse
{
    public static function responseAsImage($path, $filename) {
        ob_clean();
        header("Content-Type: application/zip");
        header('Content-Disposition: attachment; filename="' . addslashes($filename) . '"');
        header("Content-Length: " . filesize($path));
        readfile($path);
        exit;
    }

}
