<?php

class FormGenerator extends extend{

    var $forms = array();
    var $view = NULL;
    
    var $data;
    var $values;
    var $template_tag = '{FORM}';
    var $template = 'admin/simpleform';
    var $rules = array();
    
    public function init()
        {
        $this->loadComponent('view');
        $this->setAutoload(AUTOLOAD_AFTER_MODULE, 'Library.FormGenerator', $this, 'generate', array(true));
        return $this;
        }
    
    public function setFields( $data )
        {
        $this->data = $data;
        return $this;
        }
    
    public function setTemplateTag( $tag = '{FORM}' )
        {
        $this->template_tag = $tag;
        return $this;
        }
    
    public function setTemplate( $template = 'admin/simpleform' )
        {
        $this->template = $template;
        return $this;
        }
    
    public function addField( $position, $name, $content = NULL )
        {
        $this->rules['addField'][] =
            array('position' => $position,
                  'name' => $name,
                  'content' => $content);
        return $this;
        }
        
    public function forbidField( $name )
        {
        if( !array_key_exists('forbidFields', $this->rules) )
            $this->rules['forbidFields'] = array();
        $temp_arr = array();
        if( is_array($name) )
            $temp_arr += $name;
        else
            array_push($temp_arr, $name);
        foreach( $temp_arr as $name )
            array_push($this->rules['forbidFields'], $name);
        return $this;
        }
    
    public function overwriteField( $field_name, $name, $content = NULL )
        {
        $this->rules['overwriteField'][] =
            array('field_name' => $field_name,
                  'name' => $name,
                  'content' => $content);
        return $this;
        }
        
    public function setFieldType( $field_name, $type, $datasource = NULL, $datasource_prim = NULL, $datasource_name = NULL )
        {
        $this->rules['fieldType'][] =
            array('field_name' => $field_name,
                  'type' => $type,
                  'datasource' => $datasource,
                  'datasource_prim' => $datasource_prim,
                  'datasource_name' => $datasource_name);
        return $this;
        }
        
    public function setValues( $values_query_object )
        {
        $this->values = $values_query_object;
        return $this;
        }
        
    public function generate( $push_template = false )
        {
        if( !$push_template )
            {
            
            $form = array();
            
            while( $row = $this->data->row() )
                {
                $i = count($form);
                while(true && array_key_exists("addField", $this->rules))
                    {
                    $i_t = $i;
                    foreach($this->rules["addField"] as $field)
                        {
                        $form[$i]['name'] = $field['name'];
                        $form[$i]['field_name'] = $field['field_name'];
                        $form[$i]['content'] = $field['content'];
                        $form[$i]['value'] = '';
                        $form[$i]['type'] = NULL;
                        $form[$i]['ds'] = NULL;
                        $form[$i]['ds_prim'] = NULL;
                        $i++;
                        }
                        if( $i == $i_t )
                            break;
                    }
                if( (array_key_exists("forbidField", $this->rules) && !in_array($row->Field, $this->rules["forbidField"])) || !array_key_exists("forbidField", $this->rules))
                    {
                    $form[$i]['field_name'] = $row->Field;
                    $form[$i]['name'] = "%database.".$this->data->getTable().".".$row->Field."%";
                    $form[$i]['content'] = NULL;
                    $form[$i]['value'] = '';
                    $form[$i]['type'] = NULL;
                    $form[$i]['ds'] = NULL;
                    $form[$i]['ds_prim'] = NULL;
                    }
                if( array_key_exists("overwriteField", $this->rules) )
                    foreach($this->rules["overwriteField"] as $over)
                        {
                        if( $over['field_name'] == $row->Field )
                            {
                            $form[$i]['name'] = (isset($over['name'])?$over['name']:$form[$i]['name']);
                            $form[$i]['field_name'] = (isset($over['field_name'])?$over['field_name']:$form[$i]['field_name']);
                            $form[$i]['content'] = (isset($over['content'])?$over['content']:$form[$i]['content']);
                            }
                        }
                }
                foreach($form as &$f)
                    {
                    if( array_key_exists("fieldType", $this->rules) )
                        foreach($this->rules["fieldType"] as $ft)
                            {
                            if( $ft['field_name'] == $f['field_name'] )
                                {
                                $f['type'] = $ft['type'];
                                if( isset($ft["datasource"]) )
                                    {
                                    $f['ds'] = $ft["datasource"];
                                    $f['ds_prim'] = $ft["datasource_prim"];
                                    $f['ds_name'] = $ft["datasource_name"];
                                    }
                                }
                            }
                    $f['value'] = ($this->values->{$f['field_name']}?$this->values->{$f['field_name']}:'');
                   }
                $form['__e']['template'] = $this->template;
                $form['__e']['template_tag'] = $this->template_tag;
                $this->forms[] = $form;
                
                $this->data = NULL;
                $this->values = NULL;
                $this->rules = array();
                $this->setTemplateTag();
                $this->setTemplate();
            }else
            {
            foreach( $this->forms as $form )
                {
                $__e = $form['__e'];
                unset($form['__e']);
                $this->view->data->form = $form;
                $this->view->attach($__e['template'], $__e['template_tag']);
                }
            }
        }
        
}