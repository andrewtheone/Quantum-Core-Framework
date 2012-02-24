<?php

require_once "defines.php";
require_once SYSTEM_DIR."classes/application/application.class.php";
require_once SYSTEM_DIR."classes/extend/extend.class.php";

class Bootstrap{
    
    var $system;
    
    public function __construct()
        {
        if( is_dir(WWWROOT."public/".DOMAIN."/") )
            {
            if( file_exists(WWWROOT."public/".DOMAIN."/redirection") )
                {
                $redirection = trim(file_get_contents(WWWROOT."public/".DOMAIN."/redirection"));
                define("PUBLIC_DIR", WWWROOT."public/".$redirection."/");
                define("STATIC_DIR", WWWROOT."static/".$redirection."/");
                }else{
                define("PUBLIC_DIR", WWWROOT."public/".DOMAIN."/");
                define("STATIC_DIR", WWWROOT."static/".DOMAIN."/");
                }
            }else{
            define("PUBLIC_DIR", WWWROOT."public/".DEFAULT_DOMAIN."/");
            define("STATIC_DIR", WWWROOT."static/".DEFAULT_DOMAIN."/");
            }
        if( file_exists(PUBLIC_DIR."system.php") )
            require_once PUBLIC_DIR."system.php";
        else
            require_once SYSTEM_DIR."system.php";
        $this->system = new System();
        }
}
