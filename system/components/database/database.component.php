<?php
require_once SYSTEM_DIR."components/database/driver.interface.php";
require_once SYSTEM_DIR."components/database/query.interface.php";

class Database{
    
    var $config;
    var $connection;
    
    public function __construct()
        {
        }
        
    public function setConfig( $config )
        {
        $this->config = $config;
        }
        
    public function connect( $config = NULL )
        {
        if( isset($config) )
            {
            $new_connection = new $this($this);
            $new_connection->setConfig( $config );
            $new_connection->connect();
            return $new_connection;
            }
        require_once SYSTEM_DIR."components/database/drivers/".$this->config->driver."/".$this->config->driver.".driver.php";
        require_once SYSTEM_DIR."components/database/drivers/".$this->config->driver."/".$this->config->driver.".query.php";
        $ref = new ReflectionMethod($this->config->driver."_driver", "connect");
        $this->connection = $ref->invokeArgs( NULL, array($this->config) );
        }
    
    public function __get( $table )
        {
        $class = $this->config->driver."_driver";
        return new $class( $this->connection, $this->config->prefix, $table );
        }
}
