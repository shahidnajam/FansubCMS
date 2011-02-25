<?php
/**
 * @copyright  2008, Giorgio Sironi
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @version    $Id$
 * @link       http://sf.net/projects/ossigeno
 */
// note: The original name of this class is Otk_Paginator_Adapter_Doctrine!
class FansubCMS_Paginator_Adapter_Doctrine implements Zend_Paginator_Adapter_Interface {
    protected $_query;
    
    public function __construct(Doctrine_Query $query) {
        $this->_query = $query;
    }
    
    public function getItems($offset, $count) {
        $query = clone $this->_query;
        $result = $query->limit($count)
                        ->offset($offset)
                        ->execute();
        return $result;
    }
    
    public function count() {
        return count($this->_query->execute());
    }
}
