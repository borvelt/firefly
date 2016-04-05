<?php

class BooksController
{

    public function getBookByLink ($slim) {

        $slim->url = $slim->request->get('url');

        $dl = new Downloader();

        $response = $dl->captchaloader();

        list($slim->responseBody, $slim->responseCode) = self::generateResponse($slim, $response);

    }

    public function getBookByLinkNeedCaptcha ($slim) {

        $dl = new Downloader();

        $response = $dl->captchpasser($slim->request->post('fistcaptch'),$slim->request->post());

        list($slim->responseBody, $slim->responseCode) = self::generateResponse($slim, $response);

    }

    private static function generateResponse ($slim, $response) {

        if(isset($response['filename'])) {
            $enc = encrypt ($slim->request->getIp() . "#" . basename($response['filename']) . "#" . time() . "#" . $response['url']);
            // $slim->responseBody = ['download_link'=>$slim->urlFor("downloadBookByUID", ['uid'=>$enc])];
            return [['download_link'=>$slim->urlFor("downloadBookByUID", ['uid'=>$enc])], 200];
        } else {
            // $slim->responseBody = $response;
            // $slim->responseCode = 202;
          if($response == 'not_found') {
              return [$response, 404];
          } else {
              return [$response, 202];
          }
        }

    }

    public function searchBook ($slim) {

        $posted_data = $slim->request->post();
        $slim->responseBody = $posted_data;
        $books = Book::where('title','like','%'.$posted_data['book_name'].'%');
        $total_books = $books->count();
        $books_array = $books->skip($posted_data['page'] * $posted_data['limitation'])->take($posted_data['limitation'])->get()->toArray();
        $slim->responseBody = ['books'=>$books_array, 'total'=>$total_books];

    }

}
