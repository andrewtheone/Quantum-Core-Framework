<?php

class Formvalidator{

    static private $fields = array();
    static private $errors = array();
    
    static function setRule( $fieldname, $realname, $rules )
        {
        if( !count($_POST) )
            return;
        self::$fields[] = array(
            'fieldname' => $fieldname,
            'realname' => $realname,
            'rules' => $rules
        );
        }

    static function run()
        {
        if( !count($_POST) )
            return "<no post>";
        
        foreach( self::$fields as $field )
            {
            $rules = explode("|", $field['rules']);
            foreach( $rules as $rule )
                {
                switch( $_POST[ $field['fieldname'] ] )
                    {
                    case (preg_match("/\>(?P<min_length>[\d]*)/", $rule, $matches)?$_POST[ $field['fieldname'] ]:!$_POST[ $field['fieldname'] ]):
                        if( strlen($_POST[ $field['fieldname'] ]) < $matches['min_length'] )
                            self::$errors[] = "%error.formvalidator.tooshort[".$field['realname']."][".$matches['min_length']."]%";
                    break;
                    case (preg_match("/\<(?P<min_length>[\d]*)/", $rule, $matches)?$_POST[ $field['fieldname'] ]:!$_POST[ $field['fieldname'] ]):
                        if( strlen($_POST[ $field['fieldname'] ]) > $matches['min_length'] )
                            self::$errors[] = "%error.formvalidator.toolong[".$field['realname']."][".$matches['min_length']."]%";
                    break;
                    case (preg_match("/r_or\((?P<another_field>[a-zA-Z0-9\.\_\[\]]*)\)\((?P<another_realname>[a-zA-Z0-9\.\_\[\]\%]*)\)/", $rule, $matches)?$_POST[ $field['fieldname'] ]:!$_POST[ $field['fieldname'] ]):
                        if( $_POST[ $field['fieldname'] ] == '' AND $_POST[ $matches['another_field'] ] == '' )
                            self::$errors[] = "%error.formvalidator.neither[".$field['realname']."][".$matches['another_realname']."]%";
                    break;
                    case (preg_match("/r/", $rule, $matches)?$_POST[ $field['fieldname'] ]:!$_POST[ $field['fieldname'] ]):
                        if( $_POST[ $field['fieldname'] ] == '' )
                            self::$errors[] = "%error.formvalidator.required[".$field['realname']."]%";
                    break;
                    case (preg_match("/callback__(?P<function>.*)/", $rule, $matches)?$_POST[ $field['fieldname'] ]:!$_POST[ $field['fieldname'] ]):
                        if(($callback = call_user_func($matches['function'], $field['fieldname'], $field['realname'])) !== TRUE )
                            self::$errors[] = $callback;
                    break;
                    
                    }
                }
            }

        if( count(self::$errors) )
            return false;
        return true;
        }
    
    static function getErrors()
        {
        return self::$errors;
        }
}