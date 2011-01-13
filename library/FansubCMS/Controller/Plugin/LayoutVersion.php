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
 * This plugin sets the view paths according to set layout and the fallback to default.
 * @package FansubCMS
 * @subpackage Controller_Plugins
 * @author Hikaru-Shindo <hikaru@animeownage.de>
 * @version SVN: $Id
 */
class FansubCMS_Controller_Plugin_LayoutVersion extends Zend_Controller_Plugin_Abstract {
    private $_layout;
    public $view;

    public function __construct($layout) {
        $this->_layout = $layout;
        $this->view = Zend_Layout::getMvcInstance()->getView();
    }

    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        # add the paths for the gadgets
        $dir = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'addons' .
                DIRECTORY_SEPARATOR . 'gadgets' . DIRECTORY_SEPARATOR .
                'views' . DIRECTORY_SEPARATOR . $this->_layout . DIRECTORY_SEPARATOR . 'scripts';
        if(is_dir($dir)) { # we should only add if it exists
            $this->view->addScriptPath($dir);
        } else {
            $this->view->addScriptPath(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'addons' .
                    DIRECTORY_SEPARATOR . 'gadgets' . DIRECTORY_SEPARATOR .
                    'views' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . 'scripts');
        }
        # add the default view paths

        $this->view->addScriptPath(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'cms' . DIRECTORY_SEPARATOR .
                'views' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . 'scripts');
        $this->view->addScriptPath(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'modules' .
                DIRECTORY_SEPARATOR . $request->getModuleName() . DIRECTORY_SEPARATOR .
                'views' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . 'scripts');
        $this->view->addScriptPath(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'addons' .
                DIRECTORY_SEPARATOR . $request->getModuleName() . DIRECTORY_SEPARATOR .
                'views' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . 'scripts');
        # if it's not default add also the special version
        if($this->_layout != 'default') {
            $dir = APPLICATION_PATH .  DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'cms' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $this->_layout . DIRECTORY_SEPARATOR . 'scripts';
            if(is_dir($dir)) # we should only add if it exists
                $this->view->addScriptPath($dir);

            $dir = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'modules' .
                    DIRECTORY_SEPARATOR . $request->getModuleName() . DIRECTORY_SEPARATOR .
                    'views' . DIRECTORY_SEPARATOR . $this->_layout . DIRECTORY_SEPARATOR . 'scripts';
            if(is_dir($dir)) # we should only add if it exists
                $this->view->addScriptPath($dir);

            $dir = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'addons' .
                    DIRECTORY_SEPARATOR . $request->getModuleName() . DIRECTORY_SEPARATOR .
                    'views' . DIRECTORY_SEPARATOR . $this->_layout . DIRECTORY_SEPARATOR . 'scripts';
            if(is_dir($dir)) # we should only add if it exists
                $this->view->addScriptPath($dir);
        }
    }
}