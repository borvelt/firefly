<?php

class BooksController
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

    public function searchBook ($slim) {

        $posted_data = $slim->request->post();
        $slim->responseBody = $posted_data;
        $books = Book::where('title','like','%'.$posted_data['book_name'].'%')->get();
        $slim->responseBody = $books;

    }

}
