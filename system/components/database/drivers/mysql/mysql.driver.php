<?php
require_once "mysql.query.php";

class Mysql_driver{
    
    var $_db;
    var $_table;
    var $_query;
    var $_where = array();
    var $_order_by = '';
    var $_group_by = '';
    var $_left_join = '';
    var $_select = ' *';
    var $_limit = '';
    
    public function __construct( &$db, $table )
        {
        $this->_db = $db;
        $this->_table = $table;
        $this->_query = '';
        return $this;
        }
        
    public function execQuery( $query = '' )
        {
        if( empty($query) )
            $query = $this->_query;
        echo $query."<br>";
        return new Mysql_query($this->_db->connection->query($query));
        }
        
    /*Insert method*/
    public function insert( /*array(fields), array(values) or field,value,...*/ )
        {
        $args = func_get_args();
        $fields = $args[0];
        $values = $args[1];
        if( count($args) == 1 )
            {
            $fields = array_keys($args[0]);
            $values = array_values($args[0]);
            }else
        if( !is_array($args[0]) )
            {
            $fields = array();
            $values = array();
            for($i=0; $i<count($args); $i++)
                {
                $fields[] = $args[$i];
                $values[] = $args[$i+1];
                $i++;
                }
            }
        $this->_query .= "INSERT INTO `".$this->_db->config->prefix.$this->_table."`";
        $this->_query .= " (".implode(",", $fields).")";
        $this->_query .= " VALUES ('".implode("','", $values)."')";
        return $this->execQuery();
        }
    
    /*Update method*/
    public function update( /*
    array( primaries ), field, value, ... OR
    array( primaries ), array( field => value) OR
    array(prim_fields), array(prim_values), field, values,... OR
    array(prim_fields), array(prim_values), array(field => value) OR
    primary_field, primary_value, field, value,... OR*/ )
        {
        $args = func_get_args();
        if( count($this->_where) == 0 )
            {
            if( is_array($args[0]) && (!is_array($args[1]) || count($args) == 2 ) )
                {
                $this->where(array_keys($args[0]), array_values($args[0]));
                unset($args[0]);
                }else
                {
                $this->where($args[0], $args[1]);
                unset($args[0], $args[1]);
                }
            $args = array_values($args);
            }
        $sets = array();
        if( is_array($args[0]) && is_array($args[1]) )
            for($i=0;$i<count($args[0]);$i++)
                $sets[ $args[0][$i] ] = $args[1][$i];
        if( is_array($args[0]) && count($args) == 1 )
            foreach($args[0] as $k => $v)
                $sets[ $k ] = $v;
        if( !is_array($args[0]) && count($args) > 1 )
            for($i=0;$i<count($args);$i++)
                {
                $sets[ $args[$i] ] = $args[$i+1];
                $i++;
                }
        foreach( $sets as $k => &$v )
            {
            if( strpos($k, '=') === false )
                $v = "`".$this->_db->config->prefix.$this->_table."`.`".$k."`=".(is_int($v)?$v:"'".$v."'");
            else
                $v = "`".$this->_db->config->prefix.$this->_table."`.`".$k."`".(is_int($v)?$v:"'".$v."'");
            }
        $this->_query .= "UPDATE `".$this->_db->config->prefix.$this->_table."` SET ";
        $this->_query .= implode(', ', $sets);
        $this->_query .= implode('', $this->where());
        return $this->execQuery();
        }
    
    /*Select methods*/
    public function orderBy( $field_name, $order = 'ASC' )
        {
        $this->_order_by = " ORDER BY `".$this->_db->config->prefix.$this->_table."`.`".$field_name."`, ".$order."";
        return $this;
        }
        
    public function groupBy( $field_name )
        {
        $this->_group_by = " GROUP BY `".$this->_db->config->prefix.$this->_table."`.`".$field_name."`";
        return $this;
        }
        
    public function limit( $offset, $limit )
        {
        $this->_limit = " LIMIT ".$offset.",".$limit;
        return $this;
        }
    
    public function leftJoin($table, $prim_key, $foreign_key)
        {
        $this->_left_join .= " LEFT JOIN `".$this->_db->config->prefix.$table."` ON `".$this->_db->config->prefix.$this->_table."`.`".$prim_key."`=`".$this->_db->config->prefix.$table."`.`".$foreign_key."`";
        //$this->_select .= ", `".$this->_db->config->prefix.$table."`.* as ".$this->_db->config->prefix.$table.".*";
        return $this;
        }
    
    public function rightJoin(/*...*/)
        {
        return $this;
        }
    
    public function innerJoin(/*...*/)
        {
        return $this;
        }
    
    public function where(/*fields, values,... OR array(fields), array(values) */)
        {
        $args = func_get_args();
        if( count($args) == 0 )
            {
            $i = 0;
            $where = array();
            foreach( $this->_where as $sub_dimension )
                {
                    foreach( $sub_dimension as $k => $v )
                    {
                    $not = false;
                    if( $k[0] == '!' )
                        {
                        $not = true;
                        $k = substr($k, 1);
                        }
                    if( (strpos($k, '>') OR strpos($k, '<') OR strpos($k, '=')) === false )
                        $v = "`".$this->_db->config->prefix.$this->_table."`.`".$k."`=".(is_int($v)?$v:"'".$v."'");
                    else{
                        preg_match("/([a-zA-Z0-9\_\-]*)(?P<marks>[\!\=]*)/", $k, $matches);
                        $k=str_replace($matches['marks'], '', $k);
                        $v = "`".$this->_db->config->prefix.$this->_table."`.`".$k."`".$matches['marks'].(is_int($v)?$v:"'".$v."'");
                        }
                    if( $i != 0 )
                        if( $not )
                            $v = ' OR '.$v;
                        else
                            $v = ' AND '.$v;
                    else
                        $v = ' WHERE '.$v;
                    $i++;
                    $where[] = $v;
                    }
                }
            return $where;
            }
        if( is_array($args[0]) && count($args) == 1 )
            {
            foreach($args[0] as $k=>$v)
                $this->_where[][ $k ] = $v;
            }else
        if( is_array($args[0]) && count($args) > 1 )
            {
            for($i=0;$i<count($args[0]);$i++)
                $this->_where[][ $args[0][$i] ] = $args[1][$i];
            }else
            {
            for($i=0;$i<count($args);$i++)
                {
                $this->_where[][ $args[$i] ] = $args[$i+1];
                $i++;
                }
            }
        return $this;
        }
    
    public function fetch()
        {
        $this->_query .= "SELECT".$this->_select." FROM `".$this->_db->config->prefix.$this->_table."`".$this->_left_join.implode('',$this->where()).$this->_group_by.$this->_order_by.$this->_limit;
        return $this->execQuery();
        }
}
