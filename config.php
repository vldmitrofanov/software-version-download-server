<?php 

require_once 'env.php';

class Settings {

    protected $platforms = [];
    
    protected $tokens = [];

    public function __construct()
    {
        if(!defined('ENV_PLATFORMS')){
            throw new Exception('ENV_PLATFORMS must be defined. Edit env.php to define this constant');
        }

        if(!defined('ENV_TOKENS')){
            throw new Exception('ENV_TOKENS must be defined. Edit env.php to define this constant');
        }

        $this->platforms = ENV_PLATFORMS;
        $this->tokens = ENV_TOKENS;
    }

    public function checkPlatform($string){
        if(in_array($string, $this->platforms)){
            return true;
        }
        return false;
    }

    public function checkToken($string){
        if(in_array($string, $this->tokens)){
            return true;
        }
        return false;
    }
}

