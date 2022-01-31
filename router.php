<?php
require_once 'config.php';
require_once 'upload.php';
require_once 'download.php';

class Router {
    protected $request_uri, $request, $request_method, $settings;
    public function __construct($request_uri, $request, $request_method)
    {
        $this->request_uri = $request_uri; 
        $this->request = $request; 
        $this->request_method = $request_method;
        $this->settings = new Settings();
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
    }

    public function run(){
        $platform = isset($this->request_uri[1])? $this->request_uri[1] : null;
        if(!$platform || !$this->settings->checkPlatform($platform)) {
            header("HTTP/1.1 404 Not Found");
            exit;
        }

        $route = [];
        foreach($this->request_uri as $key=> $part) {
            if($key < 2) continue;
            $route[] = $part;
        }

        if(strtolower($this->request_method) == 'post') {
            $headers = [];
            foreach (getallheaders() as $name => $value) {
                $headers[$name] = $value;
            }
            if(empty($headers['Token']) || !$this->settings->checkToken($headers['Token'])){
                header("HTTP/1.1 401 Not Authorized");
                exit;
            }
            $upload = new Upload($platform, $route);
            $upload->run($this->request);
        }

        if(strtolower($this->request_method) == 'get') { 
            $this->runGet($platform, $route);
        }

    }

    public function runGet($platform, $route) {
        $download = new Download($platform);
        if($route)
            switch($route[0]){
                case 'download' :
                    $version = isset($route[1])? $route[1] : null;
                    $download->download($version);
                    break;
                case 'check' :
                    $download->checkUpdate($this->request['version']);
                    break;
                case 'phpinfo' :
                    header('Content-Type: text/html; charset=UTF-8');
                    phpinfo();
                    break;
            }
        $download->run($this->request);
    }
}