<?php

class Lister extends extend{

    var $lists = array();
    var $view = NULL;
    
    var $data;
    var $template_tag = '{LIST}';
    var $template = 'admin/simplelist';
    var $rules = array();

    public function init()
        {
        $this->loadComponent('view');
        $this->setAutoload(AUTOLOAD_AFTER_MODULE, 'Library.Lister', $this, 'generate', array(true));
        return $this;
        }
    
    public function dataSource( $data )
        {
        $this->data = $data;
        return $this;
        }
    
    public function addColumn( $position, $name, $content )
        {
        $this->rules['addColumn'][] = array( 'position' => $position, 'name' => $name, 'content' => $content);
        return $this;
        }
    
    public function forbidColumn( $name )
        {
        if( !array_key_exists('forbidColumns', $this->rules) )
            $this->rules['forbidColumns'] = array();
        $temp_arr = array();
        if( is_array($name) )
            $temp_arr += $name;
        else
            array_push($temp_arr, $name);
        foreach( $temp_arr as $name )
            array_push($this->rules['forbidColumns'], $name);
        return $this;
        }
    
    public function overwriteColumn( $field, $name, $content = NULL )
        {
        $this->rules['overwrite'][] = array( 'field' => $field, 'name' => $name, 'content' => $content);
        return $this;
        }
        
    public function setTemplateTag( $tag = '{LIST}' )
        {
        $this->template_tag = $tag;
        return $this;
        }
        
    public function setTemplate( $template = 'admin/simplelist' )
        {
        $this->template = $template;
        return $this;
        }
    
    public function generate( $push_template = false )
        {
        if( !$push_template )
            {
            $hash = count($this->lists);
            $data = new stdClass();
            while( $sdata =  $this->data->row() )
                {
                $cols = false;
                $i = (isset($data->rows)?count($data->rows):0);
                if( !isset($data->columns) )
                    $cols = true;
                $column_i = 0;
                foreach( $sdata as $col => $val )
                    {
                    if(array_key_exists('addColumn', $this->rules) )
                        {
                        foreach( $this->rules['addColumn'] as $column )
                            {
                            if( $column['position'] == $column_i )
                                {
                                if( $cols )
                                    $data->columns[] = $column['name'];
                                preg_match_all("/\%data\.([a-zA-Z0-9_]*)\%/", $column['content'], $matches);
                                for($e=0;$e<count($matches[0]);$e++)
                                    {
                                    $explode = explode(".", $matches[1][$e]);
                                    $var = &$sdata;
                                    foreach($explode as $s)
                                        $var = &$var->$s;
                                    $column['content'] = str_replace($matches[0][$e], $var, $column['content']);
                                    }
                                $data->rows[$i][] = $column['content'];
                                }
                            }
                        }
                    if( !array_key_exists('forbidColumns', $this->rules) || (array_key_exists('forbidColumns', $this->rules) && !in_array($col, $this->rules['forbidColumns'])) )
                        {
                        if( $cols )
                            {
                            if( strpos($col, '.') !== FALSE )
                                $data->columns[] = "%database.".$col."%";
                            else
                                $data->columns[] = "%database.".$this->data->getTable().".".$col."%";
                            }
                        if( array_key_exists('overwrite', $this->rules) )
                            foreach( $this->rules['overwrite'] as $overwrite )
                                {
                                if( $overwrite['field'] == $col )
                                    {
                                    if( $overwrite['name'] != NULL ) $data->columns[count($data->columns)-1] = $overwrite['name'];
                                    if( $overwrite['content'] != NULL )
                                        {
                                        preg_match_all("/\%data\.([a-zA-Z0-9_]*)\%/", $overwrite['content'], $matches);
                                        for($e=0;$i<count($matches[0]);$e++)
                                            {
                                            $explode = explode(".", $matches[1][$e]);
                                            $var = &$sdata;
                                            foreach($explode as $s)
                                                $var = &$var->$s;
                                            $overwrite['content'] = str_replace($matches[0][$e], $var, $overwrite['content']);
                                            }
                                            $val = str_replace("%value%", $val, $overwrite['content']);
                                        }
                                    }
                                }
                        $data->rows[$i][] = $val;
                        $column_i++;
                        }
                    }
                }
            $this->lists[ $hash ] =
                array('data' => $data,
                      'template' => $this->template,
                      'template_tag' => $this->template_tag);

            $this->data = NULL;
            $this->template = 'admin/simplelist';
            $this->template_tag = '{LIST}';
            $this->rules = array();
            return;
            }else
            {
            foreach( $this->lists as $list )
                {
                $this->view->data->lister->columns = $list['data']->columns;
                $this->view->data->lister->rows = $list['data']->rows;
                $this->view->attach($list['template'], $list['template_tag']);
                }
            }
        }
}