<?php

use Sunra\PhpSimple\HtmlDomParser;

class Downloader {

    private static $limitDownload = 100;
    private $url = null;
    private $client;

    public function __construct ($url = null) {
        if (is_null($url)) {
            $slim = \Slim\Slim::getInstance();
            $this->url = $slim->url;
        } else {
            $this->url = $url;
        }

        $this->client = new GuzzleHttp\Client();

        $this->checkProxy () ;

    }

    public static function limitByLink () {
        if (!isset($_SESSION["download"])) {
            $_SESSION["download"] = Downloader::$limitDownload;
        }
        if ($_SESSION["download"] == 0) {
            $translator = new Translator();
            halt_app(400, null, $translator->get('download_limited'));
        }
        $_SESSION["download"] = $_SESSION["download"] - 1;
    }

    public function setLink ($url = null) {
        if (!is_null($url)) {
            $this->url = $url;
        }
    }

    public function captchaloader () {
        $pass = parse_url($this->url);
        if (isset($pass['query'])) {
            $url = $pass['scheme'].'://'.$pass['host'].'.sci-hub.io'.$pass['path'].'?'.$pass['query'];
            if (strpos($url, "http://libgen.io") !== false) {
                $url = "http://libgen.io".$pass['path'].'?'.$pass['query'];
            }
        } else {
            $url = $pass['scheme'].'://'.$pass['host'].'.sci-hub.io'.$pass['path'];
            if (strpos($url, "http://libgen.io") !== false) {
                $url = "http://libgen.io".$pass['path'];
            }
        }
        try {
            $html_str = $this->client->request("GET", $url, ['proxy'=>$_SESSION['proxy']]);
            $html = @HtmlDomParser::str_get_html($html_str->getBody());
        } catch (\GuzzleHttp\Exception\BadResponseException $serverException) {
            return 'connection_error';
        }
        $reallink = @$html->find('iframe',0)->src;
        if($reallink) {
            try {
                $html_str = $this->client->request("GET", $url, ['proxy'=>$_SESSION['proxy']]);
                $iframhtml = @HtmlDomParser::str_get_html($html_str->getBody());
            } catch (\GuzzleHttp\Exception\BadResponseException $serverException) {
                return 'connection_error';
            }
            if($iframhtml) {
                $returnbody = $this->cookiegetter($reallink);
                return [
                    "type" => "fistcaptch",
                    "img" => $returnbody,
                    "url" => $reallink,
                ];
            } else {
                return $this->download($reallink);
            }
        } else {
            if(strpos($aa = $this->checklib($url),'http://libgen.io/') !== false) {
                $dlib	= @$html->find('a',0)->href;
                $dlib	= str_replace('../','',$dlib);
                return $this->download('http://libgen.io'.$dlib);
            } else {
                $urlcheck = @$html->find('form',0)->action;
                if (strpos($urlcheck,'solve') !== false) {
                    $loadcap =  HtmlDomParser::str_get_html($html);
                    $captchimg = @$loadcap->find('img',0)->src;
                    $captchid = @$loadcap->find('input[name=captchaId]',0)->value;
                    $geturl = $this->url;
                    return [
                        "type" => "badcaptch",
                        "img" => "http://sci-hub.io" . $captchimg,
                        "url" => $geturl,
                        "captchaId" => $captchid,
                    ];
                } else {
                    return "file_not_accessible";
                }
            }
        }
    }

    private function checklib($url) {
        $aa =  get_headers($url, 1);
        return isset($aa['Location']) ? $aa['Location'] : $url;
    }

    public function captchpasser ($which,$captch) {
        switch (trim($which)) {
            case 'fistcaptch':
                return $this->postdownload($captch);
                break;
            case 'badcaptch':
                return $this->directsic($captch);
                break;
            default:
                return 'bad_captcha';
                break;
        }
    }

    private function cookiegetter($url) {
        $pass = parse_url($url);
        $url  = $pass['scheme'].'://'.$pass['host'];
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url.'/captcha/securimage_show.php');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_VERBOSE, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_COOKIESESSION, true);
        curl_setopt($curl, CURLOPT_COOKIEJAR, dirname ( __FILE__ ).'/cookie_file1.txt');
        curl_setopt($curl, CURLOPT_COOKIEFILE, dirname ( __FILE__ ).'/cookie_file1.txt');
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/40.0.0.13');
        curl_setopt($curl, CURLOPT_PROXY, $_SESSION["proxy"]);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 60);
        $captch = curl_exec($curl);
        $base64 = 'data:image/png;base64,' . base64_encode($captch);
        return $base64;
    }

    private function directsic($data) {
        $captchaId=$data['captchaId'];
        $captcha_code=$data['captcha_code'];
        $url=$data['url'];
        $postdata = http_build_query (
            array(
                'captchaId' => $captchaId,
                'captcha_code' => $captcha_code,
                'url'=> $url
            )
        );
        $opts = array('http' =>
            array(
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => $postdata,
                'proxy'   => $_SESSION["proxy"]
            )
        );
        $context  = stream_context_create($opts);
        $file = file_get_contents('http://sci-hub.io/solve', false, $context);
        $html = @HtmlDomParser::str_get_html($file);
        if (@!$html->find('input[name=captchaId]',0)) {
            if(@$html->find('div[id=proxySelector]',0) ) {
                return "try_again";
            } else {
                foreach (@$html->find('iframe') as $element1) {
                    $orgi = $element1->src;
                    if (isset($orgi) && pathinfo($orgi, PATHINFO_EXTENSION) == "pdf") {
                        return $this->download($orgi);
                    }
                }
            }
        } else {
            return "try_again_10_minutes";
        }
    }

    private function download ($url) {
        $filename = urldecode(basename($url));
        if(strpos($filename, 'md5') !== false && strpos($url, 'http://libgen.io') !== false) {
            $filename = $this->getFileNameFromUrl($url);
        }
        if (strlen($filename) >= 2000) {
            $filename =  substr($filename, 0, 10). "" . substr($filename, -5);
        }
        if (!file_exists(Config::app('webDirectory') . 'download/')) {
            mkdir(Config::app('webDirectory') . 'download/' , 0755, true);
        }
        if (file_exists(Config::app('webDirectory') . 'download/' . $filename)) {
            $path = Config::app('webDirectory') . 'download/' . $filename;
            if(filesize($path) > 2500) {
                return $this->zipit($path);
            } else {
                unlink($path);
            }
        }
        if (!preg_match("#^https?:.+#", $url)) {
            $url = 'http:'.$url;
        }
        // $aContext = ['http' => ['proxy' => $_SESSION['proxy']]];
        // $cxContext = stream_context_create($aContext);
        // $sFile = file_get_contents($url, false, $cxContext);
        $file = fopen(Config::app('webDirectory') . 'download/' . $filename, 'w+');
        $path = Config::app('webDirectory') . 'download/' . $filename;
        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_URL            => $url,
            CURLOPT_BINARYTRANSFER => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FILE           => $file,
            CURLOPT_TIMEOUT        => 150,
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/40.0.0.13',
            CURLOPT_COOKIEFILE     => dirname ( __FILE__ ).'./cookie_file1.txt',
            CURLOPT_PROXY          => $_SESSION["proxy"]
        ]);
        $response = curl_exec($curl);
        if ($response == false || $response != true || $response != 1) {
            unlink($path);
            return "not_found";
        }
        if(filesize ($path) > 2500) {
            return $this->margeit($path);
        } else {
            unlink($path);
            return "file_not_compatible";
        }
    }

    private function postdownload ($data) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $data['url']);
        curl_setopt($curl, CURLOPT_USERAGENT,'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/32.0.1700.107 Chrome/32.0.1700.107 Safari/537.36');
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, "captcha_code=".$data['captcha_code']);
        curl_setopt($curl, CURLOPT_COOKIEFILE, dirname ( __FILE__ ).'/cookie_file1.txt');
        curl_setopt($curl, CURLOPT_PROXY, $_SESSION["proxy"]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_exec($curl);
        if (curl_error($curl)) {
            return "not_found";
        }
        return $this->download($data['url']);
    }

    private function zipit($file) {
        $pdf = Config::app('webDirectory').'download/' . basename($file);
        $filename = Config::app('webDirectory').'download/' . basename($file).'.zip';
        if(!file_exists($filename)) {
            $skip = [" ", "-", ",", "&", "*", "(", ")", "#", "@", "!", "~", "=", "+", "^", "%", "$", "/", "\\", "'", "\""];
            $replace = ["\ ", "\-", "\,", "\&", "\*", "\(", "\)", "\#", "\@", "\!", "\~", "\=", "\+", "\^", "\%", "\$", "\/", "\\", "\'",'\"'];
            system("zip --junk-paths -P http://motarjeminiran.com " . str_replace($skip, $replace, $filename) . " ". str_replace($skip, $replace, $pdf));
            ob_clean();
        }
        return ['filename' => $filename, 'url' => $this->url];
    }

    private function margeit ($file) {
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        if( $ext != 'pdf' ) {
            return $this->zipit($file);
        }
        try {
            require_once(__DIR__ . "/pdf-old/fpdf.php");
            require_once(__DIR__ . "/pdf-old/fpdi.php");
            // initiate FPDI
            $pdf = new FPDI();
            // add a page
            $pdf->AddPage();
            $pdf->SetFont('Helvetica');
            $pdf->SetTextColor(255, 0, 0);
            $pdf->SetXY(30, 30);
            $pdf->Image(Config::app("webDirectory") . "images/motarjemin iran 00111.jpg", 5, 5, 200, 0, '', "http://www.motarjeminiran.com/");
            // set the source file
            $pageCount = $pdf->setSourceFile($file);
            // import page 1
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                // import a page
                $templateId = $pdf->importPage($pageNo);
                // get the size of the imported page
                $size = $pdf->getTemplateSize($templateId);
                // create a page (landscape or portrait depending on the imported page size)
                if ($size['w'] > $size['h']) {
                    $pdf->AddPage('L', array($size['w'], $size['h']));
                } else {
                    $pdf->AddPage('P', array($size['w'], $size['h']));
                }
                // use the imported page
                $pdf->useTemplate($templateId);
                $pdf->SetFont('Helvetica');
                $pdf->SetXY(5, 5);
                $pdf->Write(8, '');
            }
            $pdf->Output($file,'F');
            return $this->zipit($file);
        } catch (Exception $e) {
            return $this->zipit($file);
        }
    }

    private function getFileNameFromUrl ($url) {
        $filename = null;
        $headers = get_headers($url);
        foreach ($headers as $header) {
            if (strpos($header, 'Content-Disposition') !== false) {
                $filename = str_replace("\"", '', end(explode('=',$header)));
            }
        }
        return $filename;
    }

    private function setProxy () {
        //download http://hideme.ru/api/proxylist.php?out=plain&code=973164094&uptime=350&ports=8080&anon=4
        $proxy = file_get_contents("http://hideme.ru/api/proxylist.php?out=plain&code=973164094&uptime=350&ports=8080&anon=4");
        //split it to array load randomly
        $proxys = explode("\n", $proxy);
        $random = rand(0,count($proxy));
        return 'tcp://'.trim($proxys[$random]);
    } 

    private function checkProxy () {
        if( !isset($_SESSION["proxy"]) &&!isset($_SESSION['times'])) {
            $_SESSION["proxy"] = $this->setProxy();
            $_SESSION['time'] = time() + 10*60;
        }
        if($_SESSION["time"] == time()) {
            $_SESSION["proxy"] = $this->setProxy();
            $_SESSION['time'] = time() + 10*60;
        }
    }

}
