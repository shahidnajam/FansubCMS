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
 * This class provides access to version management
 * @author Hikaru-Shindo <hikaru@fansubcode.org>
 *
 */
class FansubCMS_Version
{
    /**
     * 
     * The current application version
     * @var string
     */
    const VERSION = '0.9-git';
    /**
     * 
     * Zend Framework
     * @var string
     */
    const ZEND = 'zend';
    /**
     * 
     * Doctrine ORM
     * @var string
     */
    const DOCTRINE = 'doctrine';
       
    /**
     * 
     * Get the current version
     * @return string
     */
    public static function getCurrentVersion()
    {
        return self::VERSION;
    }
    
    /**
     * 
     * Get the version of framework given
     * @param string $framework
     * @return string
     */
    public static function getFrameworkVersion($framework)
    {
        if($framework == self::ZEND) {
            return Zend_Version::VERSION;
        } elseif($framework == self::DOCTRINE) {
            return Doctrine_Core::VERSION;
        }
        
        return '';
    }
    
    /**
     * 
     * This function will check for new releases.
     * Not implemented yet.
     */
    public static function getLatestVersion()
    {
        return false;
    }
}