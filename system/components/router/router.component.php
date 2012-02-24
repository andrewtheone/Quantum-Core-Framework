<?php

class Router{

    var $get = array();
    var $post = array();
    
    public function __construct()
        {
        $query = (isset($_GET['route'])?explode("/", $_GET['route']):array());
        if( isset($query[0]) && !empty($query[0]) )
            $this->get['module'] = $query[0];
        for( $i=1; $i<count($query); $i++ )
            {
            if( isset($query[$i+1]) )
                $this->get[ $query[$i] ] = $query[$i+1];
            ++$i;
            }
        
        foreach( $_POST as $key => $value )
            {
            $this->post[$key] = addslashes($value);
            }
        }
        
    public function isGet( $key, $value = NULL )
        {
        if( isset($value) )
            return (bool)($this->get[$key] == $value);
        return (bool)array_key_exists($key, $this->get); 
        }
    
    public function isPost( $key, $value = NULL )
        {
        if( isset($value) )
            return (bool)($this->post[$key] == $value);
        return (bool)array_key_exists($key, $this->post); 
        }
    
    public function isSession( $key, $value = NULL )
        {
        if( isset($value) )
            return (bool)($_SESSION[$key] == $value);
        return (bool)array_key_exists($key, $_SESSION); 
        }
        
    public function get( $key )
        {
        return (array_key_exists($key, $this->get)?$this->get[$key] : NULL); 
        }
    
    public function post( $key )
        {
        return (array_key_exists($key, $this->post)?$this->post[$key] : NULL); 
        }
    
    public function session( $key, $value = NULL )
        {
        if( isset($value) )
            {
            $_SESSION[$key] = $value;
            return;
            }
        return (array_key_exists($key, $_SESSION)?$_SESSION[$key] : NULL); 
        }
        
    public function redirection( $url )
        {
        header("Location: http://".URL.$url);
        exit();
        }

}
