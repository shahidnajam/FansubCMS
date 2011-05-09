<?php
/**
 * This view helper provides access to the default
 */
class FansubCMS_View_Helper_Markitup extends Zend_View_Helper_Abstract 
{
    protected static $_types = array(
        'html' => 'default',
        'textile' => 'textile'
    );
    
    protected $_identifier;
    protected $_type;
    protected static $_version = 'common';
    
    public function markitup($identifier = '#text', $type = 'textile')
    {       
        $this->_identifier = $identifier;
        $this->_type = $type;
        
        return $this;
    }
    
    public function setVersion($version = 'common')
    {
        self::$_version = $version;
    }
    
    public function __toString()
    {
        $type = empty(self::$_types[$this->_type]) ? 'default' : self::$_types[$this->_type];
        
        return '<script type="text/javascript" src="' . $this->view->baseUrl() . '/media/common/js/markitup/jquery.markitup.js"></script>
            <script type="text/javascript" src="' . $this->view->baseUrl() . '/media/' . self::$_version . '/js/markitup/sets/' . $type . '/set.js"></script>
            <script type="text/javascript" >
              $(document).ready(function() {
                $("' . $this->_identifier . '").markItUp(mySettings);
              });
            </script>';
    }
    
}