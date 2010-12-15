<?php
/**
 * This class extends the Zend_Controller_Action to provide some features to
 * the controllers in this application we need.
 *
 * @author Hikaru-Shindo <hikaru@animeownage.de>
 * @see Zend_Controller_Action
 * @package FansubCMS
 * @subpackage Controller
 * @version 1.0
 */
class FansubCMS_Controller_Action extends Zend_Controller_Action {
    public $acl,
            $session,
            $request,
            $defaultUseRole;

    /**
     * The class constructor
     * @param Zend_Controller_Request_Abstract $request
     * @param Zend_Controller_Response_Abstract $response
     * @param array $invokeArgs
     */
    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array()) {
        parent::__construct($request, $response, $invokeArgs);
        $this->defaultUseRole = 'fansubcms_user_custom_role_logged_in_user';
        $this->request = $request;
        $this->session = Zend_Registry::get('applicationSessionNamespace');
        $this->session->tableActions = array();
        $this->acl = Zend_Registry::get('Zend_Acl');
        $envSettings = Zend_Registry::get('environmentSettings');
        
        if(!empty($this->session->message)) {
            $this->view->message = $this->session->message;
            $this->view->message_type = $this->session->message_type;
            unset($this->session->message);
            unset($this->session->message_type);
        }
    }

    /**
     * translates the given key
     * @param string $key
     * @param array $params
     * @return string
     */
    public function translate($key,$params = array()) {
        return $this->view->translate($key,$params);
    }
}
?>
