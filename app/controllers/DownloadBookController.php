<?php

class DownloadBookController {

    public function downloadBook ($slim) {
        $decrypt_name = decrypt($slim->uid);
        $book_name = explode("#",$decrypt_name);
        $book_name = $book_name[1];
        if ( file_exists(Config::app("webDirectory") . 'download/' . $book_name) ) {
            DownloadResponse::responseAsImage(Config::app("webDirectory") . 'download/' . $book_name);
        } else {
            exit("not-found...");
        }
    }

}