<?php
/**
 */
class UserTable extends Doctrine_Table
{
    public function getTeamMemberByName($username) {
        $user = $this->createQuery()
                ->where('name like ?', $username)
                ->andWhere('show_team = ?','yes');
        return $user->fetchOne();
    }
}