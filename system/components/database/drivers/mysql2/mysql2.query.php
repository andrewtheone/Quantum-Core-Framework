<?php

class Mysql2_query implements dbQueryInterface{
    
    var $query_object;
    var $row_seek = 0;
    var $result;
    var $table;
    
    public function __construct( $query_object, $table )
        {
        $this->query_object = $query_object;
        $this->table = $table;
        if( is_object($this->query_object) )
            $this->result = $this->query_object->fetch_object();
        return $this;
        }
        
    public function __get( $var )
        {
        return $this->result->{$var};
        }
        
    public function row( $row_seek = NULL )
        {
        if( isset($row_seek) )
            $this->row_seek = $row_seek;
        if( $this->query_object->data_seek($this->row_seek) && ($this->row_seek < $this->numRows())  )
            {
            $this->row_seek += 1;
            return $this->query_object->fetch_object();
            }
        return false;
        }
    
    public function numRows()
        {
        return (int)$this->query_object->num_rows;
        }
        
    public function getTable()
        {
        return $this->table;
        }

}