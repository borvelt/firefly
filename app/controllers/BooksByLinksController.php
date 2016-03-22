<?php

class BooksByLinksController
{

    public function getBookByLink ($slim) {

        $slim->url = $slim->request->get('url');

        $dl = new Downloader();

        $response = $dl->captchaloader();

        self::generateResponse($slim, $response);

    }

    public function getBookByLinkNeedCaptcha ($slim) {

        $dl = new Downloader();

        $response = $dl->captchpasser($slim->request->post('fistcaptch'),$slim->request->post());

        self::generateResponse($slim, $response);

    }

    private static function generateResponse ($slim, $response) {
        if(isset($response['filename'])) {
            $enc = encrypt ($slim->request->getIp() . "#" . basename($response['filename']) . "#" . time());
            $slim->responseBody = ['download_link'=>$slim->urlFor("downloadBookByUID", ['uid'=>$enc])];
        } else {
            $slim->responseBody = $response;
            $slim->responseCode = 202;
        }
    }

}
