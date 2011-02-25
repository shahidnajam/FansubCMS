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
 * This class stores the user object as an array in the session and retrieves it as an object
 * @package FansubCMS
 * @subpackage Authentication
 * @author Hikaru-Shindo <hikaru@animeownage.de>
 */
class FansubCMS_Auth_Storage_DoctrineSession extends Zend_Auth_Storage_Session
{
    protected static $_data;
    
    public function __construct($namespace = self::NAMESPACE_DEFAULT, $member = self::MEMBER_DEFAULT)
    {
        /*
         * @todo this is a dirty way to do it. Find a better way.
         */
        Doctrine::loadModels(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'user' . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'Base');
        parent::__construct($namespace, $member);
    }
    
    public function write($contents)
    {
        parent::write($contents->toArray(false));
    }
    
    public function read()
    {
        $objArr = parent::read();
        if(empty(self::$_data)) {
            $table = Doctrine::getTable('User_Model_User');
            self::$_data = $table->findOneBy('id',$objArr['id']);
        }        
        return self::$_data;
        
    }
}