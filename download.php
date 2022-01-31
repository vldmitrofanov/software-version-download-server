<?php 

class Download {
    protected $platform;

    public function __construct(string $platform){
        $this->platform = $platform;
    }
   
    public function run($request) {

    }

    public function download($version = null) {
        if($version == 'latest' || !$version) {
            $this->getLatest();
        }
    }

    public function getLatest(){     
        $file_name = '';
        $json_file = dirname(__FILE__) . '/'. $this->platform . '/versions.json';

        if(!file_exists($json_file)){
            header("HTTP/1.1 404 Not Found");
            exit;
        }

        $versions = json_decode(file_get_contents($json_file), true);

        if(is_array($versions)){
            //krsort($versions);
            $latest = "0";
            foreach($versions as $key => $f){
                if(version_compare($key, $latest) == 1) {
                    $latest = $key;
                }
            }
            $file_name = $versions[$latest];
            if(!file_exists(dirname(__FILE__) . '/'. $this->platform . '/' . $file_name)) {
                echo dirname(__FILE__) . '/'. $this->platform . '/' . $file_name;
                header("HTTP/1.1 404 Not Found");
                exit;
            }
            $this->downloadZipFile('update.zip', dirname(__FILE__) . '/'. $this->platform . '/' . $file_name);
        }
    }

    public function jsonResponse($response){
        echo $response;
    }

    private function downloadZipFile($filename, $filepath) {
        header("Content-Type: application/zip");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Length: " . filesize($filepath));
        readfile($filepath);
    }

    public function checkUpdate($version){
        $json_file = dirname(__FILE__) . '/'. $this->platform . '/versions.json';
        if(!file_exists($json_file)){
            header("HTTP/1.1 404 Not Found");
            exit;
        }
        $versions = json_decode(file_get_contents($json_file), true);
        if(is_array($versions)){
            krsort($versions);
            $results = [];
            foreach($versions as $key => $local_version){
                if(version_compare($key, $version) == 1) {
                    $results[] = $key;
                }
            }
            $this->jsonResponse(json_encode($results));
            exit;
        }
        header("HTTP/1.1 422 Unknown error");
        exit;
    }
}