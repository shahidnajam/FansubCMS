<?php
/*
 *  This file is part of FansubCMS.
 *
 *  FansubCMS is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  FansubCMS is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with FansubCMS.  If not, see <http://www.gnu.org/licenses/>
 */

/**
 * User
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    FansubCMS
 * @subpackage Models
 * @author     FansubCMS Developer <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7200 2010-02-21 09:37:37Z beberlei $
 */
class User_Model_User extends User_Model_Base_User
{
    /**
     * sets the users password to a secure hash generated by phpPass
     * 
     * @param string $password
     */
    public function setPassword($password) 
    {
        $hasher = new PasswordHash(8, false);
        $this->_set('password', $hasher->HashPassword($password));
    }

    /**
     * Sets the user's password and assumes it is already hashed
     * 
     * @param string $password 
     */
    public function setHashedPassword($password)
    {
        $this->_set('password', $password);
    }

    /**
     * This method returns an array of the team
     * @param integer $project id of a project | bool false for site team
     * @return Array $team | bool false on fail
     */
    public static function getTeam($project=false) 
    {
        if($project !== false) {
            $team = false; // not yet implemented
        } else {
            $q = Doctrine_Query::create();
            $q->from('User_Model_User u')
                    ->where('u.show_team = ?','yes')
                    ->orderBy('u.name ASC');
            $users = $q->fetchArray();
            //$users = $q->execute();
            foreach($users as $user) {
                $q = Doctrine_Query::create();
                $q->select('t.name')
                        ->from('User_Model_Task t')
                        ->leftJoin('t.User_Model_UserTask ut')
                        ->where('ut.user_id = ?',$user['id']);
                $task = $q->fetchArray();
                $user['tasks'] = $task;
                $team[] = $user;
            }
        }
        return $team;
    }

    /**
     * logs the user in, returns Zend_Auth_Result
     * @author Hikaru-Shindo <hikaru@animeownage.de>
     * @param string $username
     * @param string $password
     * @return Zend_Auth_Result
     */
    public static function login($username,$password) 
    {
        // Get our authentication adapter and check credentials
        $adapter = new FansubCMS_Auth_Adapter($username,$password);
        $auth    = Zend_Auth::getInstance();
        $result  = $auth->authenticate($adapter);
        return $result;
    }

    /**
     * logs the user out
     * @author Hikaru-Shindo <hikaru@animeownage.de>
     * @return void
     */
    public static function logout()
    {
        Zend_Auth::getInstance()->clearIdentity();
    }

    /**
     * This method returns true if the user is logged in an false if not
     * @author Hikaru-Shindo <hikaru@animeownage.de>
     * @return bool
     */
    public static function isLoggedIn()
    {
        return Zend_Auth::getInstance()->hasIdentity();
    }

    public function getRoles()
    {
        $ret = array();
        foreach($this->User_Model_Role as $role) {
            $ret[] = $role->role_name;
        }
        return $ret;
    }

    public function getTasks()
    {
        $ret = array();
        foreach($this->User_Model_UserTask as $task) {
            $ret[] = $task->User_Model_Task->id;
        }
        return $ret;
    }

    public function updateProfile(array $values)
    {
        $this->name = $values['username'];
        if(!empty($values['password1'])) {
            $this->password = $values['password1'];
        }
        $this->description = $values['description'];
        $this->email = $values['email'];
        $this->active = $values['active'];
        $this->activated = $values['activated'];
        $this->show_team = $values['show_team'];
        # save
        $this->save();
        # add roles
        if(is_array($values['roles'])) {
            foreach($this->User_Model_Role as $role) {
                $role->delete();
            }
            foreach($values['roles'] as $role) {
                $r = new User_Model_Role;
                $r->user_id = $this->id;
                $r->role_name = $role;
                $r->save();
            }
        }
        if(is_array($values['tasks'])) {
            $unlink = array();
            foreach($this->User_Model_UserTask as $task) {
                $task->delete();
            }
            foreach($values['tasks'] as $task) {
                $ut = new User_Model_UserTask;
                $ut->task_id = $task;
                $ut->user_id = $this->id;
                $ut->save();
            }
        }
    }

    public function hasRole($name)
    {
        $roles = $this->getRoles();
        return in_array($name, $roles);
    }
    
    /**
     * Checks if the password is correct
     *
     * @param string $password
     * @return boolean 
     */
    public function validatePassword($password)
    {      
        $validator = new PasswordHash(8, false);
        
        if($validator->CheckPassword($password, $this->password)) {
            return true;
        } else if($this->password == hash('sha256', $this->id . $password) || $this->password == hash('sha256', $password)) {
            $this->password = $password;
            $this->save();
            return true;
        }
        
        return false;       
    }

}