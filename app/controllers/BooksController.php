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

    	$slim->url = $slim->request->post('_url');

        $dl = new Downloader();

        if ($slim->request->post('fistcaptch')) {
            $captch = $slim->request->post('fistcaptch');
        } else if ($slim->request->post('badcaptch')) {
            $captch = $slim->request->post('badcaptch');
        }

        $response = $dl->captchpasser($captch, $slim->request->post());

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
                case 'file_not_compatible':
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
        $searchType = (isset($posted_data['search-type'])) ? $posted_data['search-type'] : null;
        $book = new Book();
        if (!in_array($searchType, $book->getTableColumns())) {
          $slim->responseCode = 400;
          $slim->responseMessage = "bad_request";
          return ;
        }
        $defaultOrderBy = isset($posted_data['search-order']) ? $posted_data['search-order'] : 'id' ;
        $defaultSortBy = isset($posted_data['search-sort']) ? $posted_data['search-sort'] : 'ASC' ;
        $books = $book->where($searchType, 'like', '%'.$posted_data['book_name'].'%')->where('is_blocked', false);
        $total_books = $books->count();
        $books_array = $books->skip($posted_data['page'] * $posted_data['limitation'])->take($posted_data['limitation'])->orderBy($defaultOrderBy, $defaultSortBy)->get()->toArray();
        $slim->responseBody = ['books'=>$books_array, 'total'=>$total_books, 'searchBy'=> $posted_data['search-type'], 'orderBy'=> $defaultOrderBy, 'sort'=> $defaultSortBy];
    }

    public function downloadedBooks ($slim) {
        $limitation = $slim->request->get('limitation');
        $offset = $slim->request->get('page');
        $limitation = !is_null($limitation) ? $limitation : 50;
        $offset = !is_null($offset) ? $offset : 0;
        $downloads_count = Download::count();
        $downloads = Download::orderBy('created_at', 'DESC')->skip($limitation * $offset)->take ($limitation)->get()->toArray();
        $slim->responseBody = ['downloads'=>$downloads, 'total'=>$downloads_count];
    }

    public function reportedBooks ($slim) {
        $limitation = $slim->request->get ('limitation');
        $offset = $slim->request->get ('page');
        $limitation = !is_null($limitation) ? $limitation : 50;
        $offset = !is_null($offset) ? $offset : 0;
        $reports_count = Report::groupBy('book')->count();
        $db = Bootstrap::Eloquent()->getConnection();
        $reports = $db->table('reports')->select('*', $db->raw('count(*) as total'))->groupBy('book')->join('books','book','=','books.id')->skip($limitation * $offset)->take ($limitation)->get();
        $slim->responseBody = ['reports'=>$reports, 'total'=>$reports_count];

    }

    public function reportBook ($slim) {
        $posted_data = $slim->request->post();
        $book = Book::find($posted_data['book_id']);
        if (!$book) {
            $slim->responseMessage = "book_not_found";
            $slim->responseCode = 404;
            return ;
        }
        Report::create(['book'=>$book->id, 'ip'=>$posted_data['ip']]);
        $slim->responseMessage = "report_success";
    }

    public function blockBook ($slim) {
        $posted_data = $slim->request->post();
        $book = Book::find($posted_data['book_id']);
        if (!$book) {
            $slim->responseMessage = "book_not_found";
            $slim->responseCode = 404;
            return ;
        }
        $book->is_blocked = !$book->is_blocked;
        $book->save();
        $slim->responseMessage = "operation_success";
    }

    public function getCover ($slim) {
      $md5 = $slim->cover_md5 . ".jpg";
      $path = Config::app('webDirectory').'covers/' . $md5;
      if(file_exists($path)) {
        $fp = file_get_contents ($path);
      } else {
        $url = "http://libgen.io/covers/0/".$md5;
        $file = fopen(Config::app('webDirectory') . 'covers/' . $md5, 'w+');
        $client = new GuzzleHttp\Client(['timeout'  => 3600]);
        try {
          $fp = $client->request("GET", $url, ['save_to'=>$file, 'proxy'=>'127.0.0.1:9050']);
          $fp = file_get_contents ($path);
        } catch (Exception $serverException) {
          $fp = false;
        }
      }
      if($fp) {
        header("Content-Type: image/jpeg");
        header("Content-Length: " . filesize($path));
        echo $fp;
        exit;
      } else {
        @unlink($path);
        $slim->responseMessage = 'cover_not_found';
        $slim->responseCode = 404;
      }
    }
}
