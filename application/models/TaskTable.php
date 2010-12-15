<?php
/**
 */
class TaskTable extends Doctrine_Table
{
    public function getTasks() {
        $tasks = $this->findAll(Doctrine_Core::HYDRATE_ARRAY);
        $return = array();
        foreach($tasks as $task) {
            $return[$task['id']] = $task['name'];
        }
        return $return;
    }
}