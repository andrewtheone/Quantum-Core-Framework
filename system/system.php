<?php

class System extends Application{
    
    public function __construct()
        {
        $this->data = new stdClass();
        $this->loadComponent("database", "db");
        $this->db->setConfig( $this->loadConfig("database") );
        $this->db->connect();
        $this->loadComponent("router");
        $this->loadComponent("view");
        $this->view->setTemplate("template");
        $this->view->setDataObject($this->data);
        $settings = array();
        $rows = $this->db->settings->fetch();
        while($row = $rows->row())
            $settings[ $row->variable ] = $row->value;
        $this->loadPluginByStatus(PLUGIN_BEFORE_MODULE);
        $this->loadPluginByStatus(PLUGIN_BA_MODULE);
        $this->triggerAutoload(AUTOLOAD_BEFORE_MODULE);
        $this->loadModule( (($this->router->isGet('module') && (bool)$this->db->modules->where('name', $this->router->get('module'))->fetch()->numRows())?$this->router->get('module'):$settings['homepage']) );
        $this->triggerAutoload(AUTOLOAD_AFTER_MODULE);
        $this->loadPluginByStatus(PLUGIN_AFTER_MODULE);
        $this->loadPluginByStatus(PLUGIN_BA_MODULE);
        $this->view->render();
        }
        
    public function loadPluginByStatus( $status )
        {
        $query = $this->db->plugins->where('status', $status)->fetch();
        while($row = $query->row())
            $this->loadPlugin($row->name);
        }

}
