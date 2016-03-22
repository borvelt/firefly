<?php
class TwigView extends \Slim\View {
    public static $twigDirectory = 'Twig';
    public static $twigOptions = array (
        'cache' => /*false*/ 'app/cache/twig/',
    );
    private $twigEnvironment = null;
    public function render($template, $data = null, $status = null) {
        $slim = \Slim\Slim::getInstance();    
        if(!is_null($data)) {
            $this->appendData($data);
        }
        if(!is_null($status)) {
            $slim->response->setStatus($status);
        }
        $env = $this->getEnvironment();
        $template = $env->loadTemplate($template);
        $html = $template->render($this->data->all());
        $slim->response->headers->set('Content-Type','text/html; charset=utf-8');
        $slim->response->setBody($html);
    }
    private function getEnvironment() {
        if ( !$this->twigEnvironment ) {
            Twig_Autoloader::register();
            $loader = new Twig_Loader_Filesystem($this->getTemplatesDirectory());
            $this->twigEnvironment = new Twig_Environment(
                $loader,
                self::$twigOptions
            );
        }
        return $this->twigEnvironment;
    }
}
