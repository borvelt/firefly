<?php

class DownloadResponse
{
    public static function responseAsImage($path)
    {
        ob_clean();
        $skip = [" ", "-", ",", "&", "*", "(", ")", "#", "@", "!", "~", "=", "+", "^", "%", "$", "/", "\\", "'", "\"","."];
        $replace = ["\ ", "\-", "\,", "\&", "\*", "\(", "\)", "\#", "\@", "\!", "\~", "\=", "\+", "\^", "\%", "\$", "\/", "\\", "\'",'\"', "\."];
        header("Content-Type: application/zip");
        header("Content-Disposition: attachment; filename=" . str_replace($skip, $replace, basename($path)));
        header("Content-Length: " . filesize($path));
        readfile($path);
        exit;
    }

}
