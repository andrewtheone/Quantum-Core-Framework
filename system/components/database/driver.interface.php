<?php

interface dbDriverInterface{

    /*
    * @param database config object
    * @return resource connection
    */
    public static function connect( $config );
    
    /*
    * @param resource connection
    * @param string table's prefix
    * @param string table
    * @return bool
    */
    public function __construct( $connection, $prefix, $table );
    
    /*
    * @param string prefix, lasts for the next function call
    * @return -
    */
    public function usePrefix( $temporaly_prefix );
    
    /*
    * @param array/null tables to export
    * @param string path to export
    * @return bool
    */
    public function export( $tables = NULL, $path = '/' );
    
    /*
    * @return object query
    */
    public function getFields();
    
    /*
    * @param string query string
    * @return object query
    */
    public function exQuery( $query );
    
    /*
    * @variation 1
    *  @params null OR function hasn't called select all fields
    * @variation 2
    *  @params string field, string field, string field, ...
    * @variation 3
    *  @params array Array('field_name' => 'field_alias', ... )
    * @return object this
    */
    public function select( /**/ );
    
    /*
    * @variation 1
    *  @params null OR function hasn't called no where clause
    * @variation 2
    *  @params string field, string value, ...
    *   @example where('username', 'test', 'ip', 'localhost') => where username='test' AND ip='localhost'
    *   @example where('username', 'test', '!ip', 'localhost') => where username='test' OR ip='localhost'
    *   @example where('username', 'test', 'ip!', 'localhost') => where username='test' AND ip!='localhost'
    *   @example where('username', 'test', 'id<', 2) => where username='test' AND id<2
    *   @example where('username', 'test', '!id>=', 2) => where username='test' OR id>=2
    * @variation 3
    *  @params array Array( 0 => Array('field' => 'username', 'operator' => '[default: = ]', 'value' => 'test', 'join' => 'AND/OR'), 1=> ..., ... )
    *   @example where( array( array('field' => 'username', 'operator' => '!=', 'value' => 'test'), array('field' => 'ip', 'operator' => '=', 'value' => 'localhost', 'join' => 'AND')))
    *      => where username!=test AND ip!=localhost
    * @return object this
    */
    public function where( /**/ );
    
    /*
    * @param string
    * @param string ASC/DESC
    * @return object this
    */
    public function orderBy( $field, $order = 'ASC' );
    
    /*
    * @param string
    * @return object this
    */
    public function groupBy( $field );
    
    /*
    * @param int
    * @param int
    * @return object this
    */
    public function limit( $offset, $limit );
    
    /*
    * @param string
    * @param string
    * @param string
    * @return object this
    */
    public function leftJoin( $foreign_table, $foreign_primary, $primary );
    
    /*
    * @param string
    * @param string
    * @param string
    * @return object this
    */
    public function rightJoin( $foreign_table, $foreign_primary, $primary );
    
    /*
    * @param string
    * @param string
    * @param string
    * @return object this
    */
    public function innerJoin( $foreign_table, $foreign_primary, $primary );
    
    /*
    * build query and call exQuery()
    */
    public function fetch();
    
    /*
    * @variation 1
    *  @param string field
    *  @param string/int value
    *   @usage where(...)->update('field', 'value', ...);
    * @variation 2
    *  @param string primary_field
    *  @param string/int primary_value
    *  @param string field
    *  @param string/int value
    *   @usage update('id', 12, 'lastlogin', 'NOW()', 'lastip', 'localhost');
    * @variation 3
    *  @param array primaries
    *  @param string field
    *  @param string/int value
    *   @usage update( array('id'=>12, '!username'=>'test'), 'lastlogin', 'NOW()', 'lastip', 'localhost')
    * @return int affected rows number
    */
    public function update( /**/ );
    
    /*
    * @variation 1
    *  @param array fields
    *  @param array values
    *   @usage insert( array(username, password), array('lol', 'lol2') )
    * @variation 2
    *  @param string field
    *  @param string/int value
    *   @usage insert('username', 'lol', 'password', 'lol2');
    * @return int lastInsertId
    */
    public function insert( /**/ );
    
    public function useTable( $table );
    
}