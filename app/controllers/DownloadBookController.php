<?php

class DownloadBookController {

    public function downloadBook ($slim) {
        $decrypt_name = decrypt($slim->uid);
        $book_name = explode("#",$decrypt_name);
        $book_name = $book_name[1];
        $generation_time = $book_name[2];
        if(time() - $generation_time > 60) {
            halt_app(404, 'link_destroyed');
        }
        if ( file_exists(Config::app("webDirectory") . 'download/' . $book_name) ) {
            DownloadResponse::responseAsImage(Config::app("webDirectory") . 'download/' . $book_name);
        } else {
           halt_app(404, 'not_found');
        }
    }

}