<?php


interface dbQueryInterface{

    public function __construct( $query_object, $table );
    
    public function __get( $field );
    
    public function row( $id = NULL );
    
    public function numRows();
    
    public function getTable();
}

