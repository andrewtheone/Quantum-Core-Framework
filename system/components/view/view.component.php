<?php

class View{
    
    var $template_file;
    var $template;
    var $data;
    
    public function __construct()
        {
        }
        
    public function setTemplate( $template_file )
        {
        $this->template_file = $template_file;
        $this->attach( $this->template_file, NULL );
        }
    
    public function setDataObject( &$data )
        {
        $this->data = $data;
        }
    
    public function attach( $template_file, $var = '{CONTENT}' )
        {
        if( file_exists(PUBLIC_DIR."views/".$template_file.".php") )
            $template_file_path = PUBLIC_DIR."views/".$template_file;
        elseif( file_exists(SHARED_PUBLIC_DIR."views/".$template_file.".php") )
            $template_file_path = SHARED_PUBLIC_DIR."views/".$template_file;
        else
            $template_file_path = SHARED_PUBLIC_DIR."views/error/missing_template";
        ob_start();
        $data = &$this->data;
        require $template_file_path.".php" ;
        $template = ob_get_contents();
        ob_end_clean();
        if( !isset($var) )
            return $this->template = $template;
        $this->template = str_replace($var, $template, $this->template);
        return;
        }
        
    public function render()
        {
        echo $this->template;
        }
}