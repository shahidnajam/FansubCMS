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

class Admin_DevtoolsController extends FansubCMS_Controller_Action 
{
    public function init()
    {
        if(APPLICATION_ENV != 'development')
        {
            throw new Zend_Controller_Dispatcher_Exception('Invalid controller specified (devtools)');
            die;
        }
        $front = Zend_Controller_Front::getInstance();
        $front->unregisterPlugin('ZFDebug_Controller_Plugin_Debug');
        $this->_helper->layout()->disableLayout();
        set_time_limit(0);
    }
    
    public function preDispatch() {
        $this->view->partialName = $this->getRequest()->getParam('action');
    }
    
    public function indexAction()
    {
        if(!isset($this->view->title)) {
            $this->view->title = "Main page";
        }

        if(!isset($this->view->showMenu)) {
            $this->view->showMenu = true;
        }
    }
    
    public function migrationAction()
    {
        $pdata = new stdClass();
        $changes = Admin_Api_DoctrineTool::generateMigrations(true);
        $changeCount = 0;

        $migration = Admin_Api_DoctrineTool::getMigration();

        $currentVersion = $migration->getCurrentVersion();
        $latestVersion = $migration->getLatestVersion();

        $pdata->migrationNeeded = ($latestVersion > $currentVersion);

        $pdata->currentVersion = $currentVersion;
        $pdata->latestVersion = $latestVersion;

        // FirePHP_Init::init()->fb($pdata);
        
        foreach($changes as $changeType => $arr) {
            $changeCount += count($arr);
        }

        if($changeCount > 0) {
            $pdata->hasChanges = true;
            $pdata->changeCount = $changeCount;
            $pdata->changes = $changes;
        }
        else {
            $pdata->hasChanges = false;
        }

        $this->view->partialData = $pdata;
        $this->_forward('index');
    }
}