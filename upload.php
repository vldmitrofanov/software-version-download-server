<?php



class Upload
{
    protected $platform;
    protected $route;
    protected $filetypes = [
        'win32' => ['application/zip'],
        'darwin' => [
            'application/octet-stream' ,
            'application/zlib'
        ],
        'deb64' => [
            'application/x-gzip',
            'application/x-gtar',
            'application/x-tgz'
        ],
    ];

    public function __construct($platform, $route, $headers = [])
    {
        $this->platform = $platform;
        $this->route = $route;
    }

    public function run($request)
    {
        if ($_FILES["file"]) {
            $this->runUpload($request);
        }
    }

    private function runUpload($request)
    {
        $latest = "0.0.0";
        $versions = [];
        $version_provided = false;

        $version_type = empty($request['update_type']) ? 'patch' : $request['update_type'];
        if (!empty($request['version']) && preg_match('@^\d+\.\d+\.\d+$@', $request['version'])) {
            $latest = $request['version'];
            $version_provided = true;
        }

        if (!empty($_FILES['file']['error'])) {
            header("HTTP/1.1 422 Upload error");
            exit;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $file_type = finfo_file($finfo, $_FILES["file"]["tmp_name"]);
        finfo_close($finfo);

        if (!empty($this->filetypes[$this->platform]) && !in_array($file_type, $this->filetypes[$this->platform])) {
            header("HTTP/1.1 422 Wrong file type");
            exit;
        }

        $new_file_path = dirname(__FILE__) . '/' . $this->platform . '/' . $_FILES["file"]["name"];

        if (file_exists($new_file_path)) {
            header("HTTP/1.1 422 File exists");
            exit;
        }

        move_uploaded_file($_FILES["file"]["tmp_name"], $new_file_path);

        $json_file = dirname(__FILE__) . '/' . $this->platform . '/versions.json';
        if (file_exists($json_file)) {
            $versions = json_decode(file_get_contents($json_file), true);

            if (is_array($versions)) {
                foreach ($versions as $key => $f) {
                    if (!$version_provided) {
                        if (version_compare($key, $latest) == 1) {
                            $latest = $key;
                        }
                    } else {
                        if (version_compare($key, $latest) == 0) {
                            header("HTTP/1.1 422 Version exists");
                            unlink($new_file_path);
                            exit;
                        }
                    }
                }
            }
        }

        $ver = explode('.', $latest);

        if (!$version_provided) {
            switch ($version_type) {
                case 'patch':
                    $ver[2]++;
                    break;
                case 'minor':
                    $ver[1]++;
                    $ver[2] = 0;
                    break;
                case 'major':
                    $ver[0]++;
                    $ver[1] = 0;
                    $ver[2] = 0;
            }
        }

        $ver = !$version_provided ? implode('.', $ver) : $latest;
        $versions[$ver] = $_FILES["file"]["name"];
        krsort($versions);
        file_put_contents(dirname(__FILE__) . '/' . $this->platform . '/versions.json', json_encode($versions));
    }
}
