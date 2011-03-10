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
 * This class is the abstract basis for every table
 * @author Hikaru-Shindo <hikaru@animeownage.de>
 * @package FansubCMS
 * @subpackage Api
 * 
 */
class Cms_Api_Table
{
    /**
     * 
     * The query used by the table
     * @var Doctrine_Query
     */
    protected $_query;
    /**
     * 
     * The current page
     * @var integer
     */
    protected $_page = 1;
    /**
     * 
     * The number of elements per page
     * @var integer
     */
    protected $_pageCount = 25;
    /**
     * 
     * Table actions list
     * @var array
     */
    protected $_actions = array();
    
    /**
     * 
     * The class constructor
     * @param Doctrine_Query $q
     */
    public function __construct(Doctrine_Query $q)
    {
        $this->setQuery($q);
    }
    
    /**
     * 
     * Sets the current page
     * @param integer $page
     */
    public function setPage($page)
    {
        $this->_page = $page;
    }
    
    /**
     * 
     * Get the current page
     * @return integer
     */
    public function getPage()
    {
        return $this->_page;
    }
    
    /**
     * 
     * Set count for items per page
     * @param integer $count
     */
    public function setPageCount($count)
    {
        $this->_pageCount = $count;
    }
    
    /**
     * 
     * Get the count of items per page
     * @return integer
     */
    public function getPageCount()
    {
        return $this->_pageCount;
    }
    
    /**
     * 
     * Sets the query
     * @param Doctrine_Query $q
     */
    public function setQuery(Doctrine_Query $q)
    {
        $q->addSelect('id');
        $this->_query = $q;
    }
    
    /**
     * 
     * Returns the query
     * @return Doctrine_Query
     */
    public function getQuery()
    {
        return $this->_query;
    }
    
    /**
     * 
     * Add a single action to the table
     * 
     * The option array has to contain at least the following fields:
     * [name] - A unique name for the action
     * [label] - The label for the action
     * [target][module] - The module of the action
     * 
     * It may also contain:
     * [privilege] - The ACL privilege to be used (defaults: view)
     * [target][controller] - The controller of the action (defaults to: index)
     * [target][action] - The action of the action (defaults to: index)
     * [target][params][paramname] - Set the value of param paramname to value.
     *                               If you need selected field of table use __fieldname__
     *                               (defaults: id => __id__)
     * 
     * @param array $options
     * @throws Exception if option param is missing
     */
    public function addAction($options)
    {
        if(empty($options['name'])) {
            throw new Exception('Name cannot be empty');
        }
        if(empty($options['label'])) {
            throw new Exception('A label is needed');
        }
        if(empty($options['target']['module'])) {
            throw new Exception('Target module cannot be empty');
        }
        if(empty($options['target']['controller'])) {
            $options['target']['controller'] = 'index';
        }
        if(empty($options['target']['action'])) {
            $options['target']['action'] = 'index';
        }
        if(!count($options['target']['params'])) {
            $options['target']['params'] = array();
            $options['target']['params']['id'] = '__id__';
        }

        $this->_actions[$options['name']] = $options;
    }
    
    /**
     * 
     * Remove an action added previously
     * @param string $name
     */
    public function removeAction($name)
    {
        unset($this->_actions[$name]);
    }
    
    /**
     * 
     * Get the action of the name $name
     * @param string $name
     * @return array|boolean If no action of this name exists this method will return false
     */
    public function getAction($name)
    {
        if(!empty($this->_actions[$name])) {
            return $this->_actions[$name];
        }
        return false;
    }
    
    /**
     * 
     * Returns all set actions
     * @return array
     */
    public function getActions()
    {
        return $this->_actions;
    }
    
    /**
     * 
     * Returns the paginator of the table
     * @return Zend_Paginator
     */
    public function getPaginator()
    {
        $paginator = new Zend_Paginator(new FansubCMS_Paginator_Adapter_Doctrine($this->_query, true));
        $paginator->setCurrentPageNumber($this->_page);
        $paginator->setItemCountPerPage($this->_pageCount);

        return $paginator;
    }
}