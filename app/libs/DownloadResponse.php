<?php

class DownloadResponse
{
    public static function responseAsImage($path)
    {
        ob_clean();
        header("Content-Type: application/zip");
       	exit(basename($path));
        header("Content-Disposition: attachment; filename='" . basename($path) . "'");
        header("Content-Length: " . filesize($path));
        readfile($path);
        exit;
    }

}
