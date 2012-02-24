<?php

class User extends Extend{

    var $router;
    var $db;
    var $uid;
    var $user_data;

    public function init( $uid = NULL )
        {
        $this->loadComponent('router');
        $this->loadComponent('database', 'db');
        if( !isset($uid) )
            if( !$this->router->isSession('uid') )
                return;
            else
                $uid = $this->router->session('uid');
        $this->uid = $uid;
        if( $this->db->users->where('id', $uid)->fetch()->numRows() == 0 )
            return false;
        $this->user_data = $this->db->users->where('id', $uid)->fetch()->row();
        }
        
    public function __set( $var, $value)
        {
        if( isset($this->user_data->$var) )
            {
            $this->db->users->where('id', $this->uid)->update($var, $value);
            $this->user_data->{$var} = $value;
            return;
            }
        }
        
    public function __get( $var )
        {
        if( isset($this->user_data->$var) )
            return $this->user_data->{$var};
        }
    
    public function getInfo( $uid = NULL )
        {
        if( !isset($uid) )
            $uid = $this->uid;
        return $this->loadClass("userInfo", $uid);
        }
        
    public function createUser( $username, $email, $password )
        {
        $this->db->users->insert('username', $username, 'email', $email, 'password', $password);
        return $this->getUser( $this->db->lastInsertId() );
        }
        
    public function signIn( $username, $email, $password )
        {
        $login_datas = array();
        if( isset($username) && $username != '') $login_datas['username'] = $username;
        if( isset($email) && $email != '') $login_datas['email'] = $email;
        if( isset($password) ) $login_datas['password'] = $password;

        $query = $this->db->users->where($login_datas)->fetch();
        if( $query->numRows() == 0 )
            return false;
        $this->router->session('uid', $query->id);
        return $this->getUser();
        }
    
    public function getUser( $uid = NULL )
        {
        return new $this($this, $uid);
        }
        
    public function hasPermission( $permission )
        {
        if( !isset($this->uid) )
            return false;
        if( $this->loadClass("acl", $this->user_data->acl_group)->getPermission( "super_admin" ) )
            return true;
        return $this->loadClass("acl", $this->user_data->acl_group)->getPermission( $permission );
        }

}
