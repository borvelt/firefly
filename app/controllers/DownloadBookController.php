<?php

class DownloadBookController
{

    public function downloadBook($slim)
    {
        $decrypt_name = decrypt($slim->uid);
        $book_name = explode("#", $decrypt_name);
        $book_url = $book_name[3];
        $generation_time = $book_name[2];
//        $ip = $book_name[0];
        $book_name = $book_name[1];
        if (time() - $generation_time > 900) {
            halt_app(404, null, 'link_destroyed');
        }
        if (file_exists(Config::app("webDirectory") . 'download/' . $book_name)) {
            if ($book_url) {
                $download = new Download();
                $download->download_key = $slim->uid;
                $download->request_url = $book_url;
                $download->ip = $slim->request->getIp();
                $pos = strpos($book_url, 'md5=');
                if ($pos !== false) {
                    $md5 = substr($book_url, $pos + 4, 32);
                    $book = Book::where('md5', $md5)->first();
                    $download->book = $book->id;
                }
                $download->save();
            }
            $filename = self::getFileName($book_name, $book_url);
            DownloadResponse::responseAsImage(Config::app("webDirectory") . 'download/' . $book_name, $filename);
        } else {
            halt_app(404, null, 'not_found');
        }
    }

    public function getFileName($name, $url)
    {
        $md5_pos = strpos($url, 'md5=');
        if ($md5_pos !== false && strpos($url, 'http://libgen.io') !== false) {
            $md5 = substr($url, 29);
            $book = Book::where('md5', '=', $md5)->first();
            if (!$book) {
                return $md5 . '.' . pathinfo($name, PATHINFO_EXTENSION);
            }
            return $book->title . '- ' . $book->author . '- ' . $book->year . '.' . pathinfo($name, PATHINFO_EXTENSION);
        } else {
            return end(explode('(=)', $name));
        }
    }

}
