<?php

class Application{
    
    var $__components = array();
    var $__classes = array();
    var $__libraries = array();
    var $__autoloader = array('__system'=>array(AUTOLOAD_BEFORE_MODULE=>true));
    var $data;
    
    public function __construct( )
        {
        $args = &func_get_args();
        $this->__components = &$args[0]->__components;
        $this->__classes = &$args[0]->__classes;
        $this->__libraries = &$args[0]->__libraries;
        $this->__autoloader = &$args[0]->__autoloader;
        $this->data = &$args[0]->data;
        unset($args[0]);
        if( method_exists($this, 'init') )
            call_user_func_array(array($this, 'init'), $args);
        }
        
    public function setAutoload( $trigger, $name, &$object, $function, $args )
        {
        if( $this->__autoloader['__system'][AUTOLOAD_BEFORE_MODULE] == false && $trigger == AUTOLOAD_BEFORE_MODULE )
            return;
        if( !isset($this->__autoloader[$trigger]) )
            $this->__autoloader[$trigger] = array();
        $this->__autoloader[ $trigger ][ count($this->__autoloader[$trigger])] =
            array('name' => $name,
                 'object' => $object,
                 'function' => $function,
                 'args' => $args);
        return;
        }
        
    public function triggerAutoload( $trigger )
        {
        if( !isset($this->__autoloader[$trigger]) || !count($this->__autoloader[$trigger]) )
            return;

        foreach( $this->__autoloader[$trigger] as &$autoload )
            {
            if( isset($autoload['object']) )
                call_user_func_array(array($autoload['object'], $autoload['function']), $autoload['args']);
            else
                call_user_func_array($autoload['function'], $autoload['args']);
            unset($autoload);
            }
        }
    
    public function loadComponent( $component_name, $alias = NULL )
        {
        if( array_key_exists($component_name, $this->__components) )
            {
            $this->{($alias?$alias:$component_name)} = &$this->__components[$component_name];
            return;
            }
            
        if( file_exists(SYSTEM_DIR."components/".$component_name."/".$component_name.".component.php") )
            {
            require_once SYSTEM_DIR."components/".$component_name."/".$component_name.".component.php";
            $this->__components[$component_name] = &$this->extend($component_name);
            $this->{($alias?$alias:$component_name)} = &$this->__components[$component_name];
            }else{
            die("System: Component <b>".$component_name."</b> is missing!");
            }
        }
        
    public function loadClass( $class_name /*, args*/ )
        {
        $args = &func_get_args();
        $class_name = $args[0];
        
        if( array_key_exists($class_name, $this->__classes) && count($args) > 1 )
            call_user_func_array(array($this->__classes[$class_name], 'init'), array_slice($args, 1));
        
        if( array_key_exists($class_name, $this->__classes) )
            return $this->__classes[$class_name];
            
        if( file_exists(SYSTEM_DIR."classes/".$class_name."/".$class_name.".class.php") )
            {
            require_once SYSTEM_DIR."classes/".$class_name."/".$class_name.".class.php";
            }else{
            die("System: Class <b>".$class_name."</b> is missing!");
            }
        
        $this->__classes[$class_name] = &call_user_func_array(array($this, 'extend'), $args);
        return $this->__classes[$class_name];
        }
    
    public function loadConfig( $config_name )
        {
        if( file_exists(SYSTEM_DIR."configs/".$config_name."/".$config_name.".config.php") )
            require_once SYSTEM_DIR."configs/".$config_name."/".$config_name.".config.php";
            elseif( file_exists(PUBLIC_DIR."configs/".$config_name."/".$config_name.".config.php") )
                require_once PUBLIC_DIR."configs/".$config_name."/".$config_name.".config.php";
                elseif( file_exists(SHARED_PUBLIC_DIR."configs/".$config_name."/".$config_name.".config.php") )
                    require_once SHARED_PUBLIC_DIR."configs/".$config_name."/".$config_name.".config.php";
                    else
                    die("System: Configuration file <b>".$config_name."</b> is missing!");
        return $config;
        }
        
    public function loadLibrary( $library_name /*, args*/ )
        {
        $args = &func_get_args();
        $library_name = $args[0];
        if( array_key_exists($library_name, $this->__libraries) )
            return $this->__libraries[$library_name];
        if( file_exists(SHARED_PUBLIC_DIR."libraries/".$library_name."/".$library_name.".library.php") )
            {
            require_once SHARED_PUBLIC_DIR."libraries/".$library_name."/".$library_name.".library.php";
            }elseif( file_exists(PUBLIC_DIR."libraries/".$library_name."/".$library_name.".library.php") )
                {
                require_once PUBLIC_DIR."libraries/".$library_name."/".$library_name.".library.php";
                }else{
                die("System: Library <b>".$library_name."</b> couldn't be found!");
                }
        $this->__libraries[$library_name] = &call_user_func_array(array($this, 'extend'), $args);
        return $this->__libraries[$library_name];
        }
        
    public function loadHelper( $helper_name )
        {
        if( file_exists(SHARED_PUBLIC_DIR."helpers/".$helper_name."/".$helper_name.".helper.php") )
            {
            require_once SHARED_PUBLIC_DIR."helpers/".$helper_name."/".$helper_name.".helper.php";
            }elseif( file_exists(PUBLIC_DIR."helpers/".$helper_name."/".$helper_name.".helper.php") )
                {
                require_once PUBLIC_DIR."helpers/".$helper_name."/".$helper_name.".helper.php";
                }else{
                die("System: Helper <b>".$helper_name."</b> couldn't be found!");
                }
        return new $helper_name();
        }
        
    public function loadModule( $module_name )
        {
        $data = &$this->data;
        if( file_exists(PUBLIC_DIR."modules/".$module_name."/module.php") )
            {
            require_once PUBLIC_DIR."modules/".$module_name."/module.php";
            }elseif( file_exists(SHARED_PUBLIC_DIR."modules/".$module_name."/module.php") )
                {
                require_once SHARED_PUBLIC_DIR."modules/".$module_name."/module.php";
                }else{
                $this->loadModule('404');
                }
        }
        
    public function loadPlugin( $plugin_name )
        {
        $data = &$this->data;
        if( file_exists(PUBLIC_DIR."plugins/".$plugin_name."/".$plugin_name.".plugin.php") )
            {
            require PUBLIC_DIR."plugins/".$plugin_name."/".$plugin_name.".plugin.php";
            }elseif( file_exists(SHARED_PUBLIC_DIR."plugins/".$plugin_name."/".$plugin_name.".plugin.php") )
                {
                require SHARED_PUBLIC_DIR."plugins/".$plugin_name."/".$plugin_name.".plugin.php";
                }
        }
        
    public function extend( $class_name /*, args*/ )
        {
        $args = func_get_args();
        if( get_parent_class($args[0]) == 'Extend' )
            {
            $ref = new ReflectionClass($args[0]);
            $args[0] = &$this;
            return $ref->newInstanceArgs($args);
            }
        return new $args[0]();
        }

}
