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
 * 
 * Helps with the application logging
 * @author Hikaru-Shindo <hikaru@animeownage.de>
 * @package FansubCMS
 * @subpackage Helper
 */
class FansubCMS_Helper_Log extends Zend_Log
{
    /**
     * @var FansubCMS_Helper_Log
     */
    protected static $_instance;
    /**
     * @var Zend_Log
     */
    protected static $_logger;
    /**
     * 
     * constructs the helper
     */
    protected function __construct ()
    {
        Doctrine::getTable('Task')->get
    }
    /**
     * Get an instance of the helper
     * @return FansubCMS_Helper_Log
     */
    public function getInstance ()
    {
        if (empty(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
}