<?php

class DownloadBookController {

    public function downloadBook ($slim) {
        $decrypt_name = decrypt($slim->uid);
        $book_name = explode("#",$decrypt_name);
        $generation_time = $book_name[2];
        $ip = $book_name[0];
        $book_name = $book_name[1];
        if(time() - $generation_time > 60) {
            halt_app(404, null, 'link_destroyed');
        }
        // if ($slim->request->getIp() != $ip) {
        //     halt_app(404, null, 'invalid_ip');
        // }
        if ( file_exists(Config::app("webDirectory") . 'download/' . $book_name) ) {
            DownloadResponse::responseAsImage(Config::app("webDirectory") . 'download/' . $book_name);
        } else {
           halt_app(404, null, 'not_found');
        }
    }

}
