<?php

/**
 * User_Model_UserTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class User_Model_UserTable extends Doctrine_Table
{
    /**
     * Returns an instance of this class.
     *
     * @return object User_Model_UserTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('User_Model_User');
    }
    
    public function getTeamMemberByName ($username)
    {
        $user = $this->createQuery()
            ->where('name like ?', $username)
            ->andWhere('show_team = ?', 'yes');
        return $user->fetchOne();
    }
}