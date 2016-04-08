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
            return [['download_link'=>$slim->urlFor("downloadBookByUID", ['uid'=>$enc])], 200];
        } else if (isset($response['type'])) {
            return [$response, 202];
        } else {
            switch ($response) {
              case 'bad_captcha':
                return [$response, 400];
                break;
              case 'not_found':
                return [$response, 404];
                break;
              case 'connection_error':
                return [$response, 503];
                break;
              case 'error_create_zip_file':
                return [$response, 503];
                break;
                case 'file_not_accessible':
                  return [$response, 410];
                  break;
              default:
                return [$response, 410];
                break;
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

    public function downloadedBooks ($slim) {
        $limitation = $slim->request->get ('limitation');
        $offset = $slim->request->get ('page');
        $limitation = !is_null($limitation) ? $limitation : 50;
        $offset = !is_null($offset) ? $offset : 0;
        $downloads_count = Download::all()->count();
        $downloads = Download::orderBy('created_at', 'DESC')->skip($limitation * $offset)->take ($limitation)->get()->toArray();
        $slim->responseBody = ['downloads'=>$downloads, 'total'=>$downloads_count];
    }

}
