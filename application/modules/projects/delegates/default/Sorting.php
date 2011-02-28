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
class Projects_Delegate_Default_Sorting
{
    public function __construct($settings, $request)
    {
        $this->request = $request;
        $this->settings = $settings;
    }
    
    public function sortProjects($projects)
    {
        $return = array();
        # sort the projects according to their status
        foreach($projects as $project) {
            if(empty($return[$project->status])) {
                $return[$project->status] = array();
            }
            $return[$project->status][] = $project;
        }
        return $return;
    }
}