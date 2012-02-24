<?php

preg_match_all("/%([a-z\.\_]*)([a-zA-Z0-9\.\_\%\:\-\[\]]*)%/", $this->view->template, $matches);
for($i=0;$i<count($matches[1]);$i++)
    {
    $string = $matches[0][$i];
    $entry = $this->db->language_entries->where('entry', $matches[1][$i])->fetch();
    if( $entry->numRows() === 1 )
        {
        $text = $this->db->language_texts->where('language_id', 1, 'entry_id', $entry->id)->fetch()->text;
        $arguments = array();
        preg_match("/\[([^\]]*)\]/", $matches[2][$i], $preg_arguments);
        while(count($preg_arguments) != 0)
            {
            $arguments[count($arguments)+1] = $preg_arguments[1];
            $matches[2][$i] = str_replace($preg_arguments[0], '', $matches[2][$i]);
            preg_match("/\[([^\]]*)\]/", $matches[2][$i], $preg_arguments);
            }
        for($e=1;$e<count($arguments)+1;$e++)
            $text = str_replace('$'.$e, $arguments[$e], $text);
        $this->view->template = str_replace($string, $text, $this->view->template);
        }
    }
    
preg_match_all("/\%data\.([a-zA-Z0-9\._\[\]]*)\%/", $this->view->template, $matches);
for($i=0;$i<count($matches[0]);$i++)
    {
    $explode = explode(".", $matches[1][$i]);
    $var = &$this->data;
    foreach($explode as $s)
        $var = &$var->$s;
    $this->view->template = str_replace($matches[0][$i], $var, $this->view->template);
    }