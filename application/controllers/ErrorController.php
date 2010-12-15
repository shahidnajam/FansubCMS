<?php
/**
 * This controller should handle errors in the application.
 *
 * @package FansubCMS
 * @subpackage Controllers
 * @author Hikaru-Shindo <hikaru@animeownage.de>
 */
class Default_ErrorController extends FansubCMS_Controller_Action {
    /**
     * This function handles all the general errors.
     * @return void
     */
    public function errorAction() {
        $errors = $this->_getParam('error_handler');
        $this->view->title = $this->translate("error_encountered");
        if($errors == null) {
        	$errors = new stdClass();
        	$errors->type = Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER;
        	$errors->exception = new Exception("Invalid controller specified (error)");
        	$errors->request = $this->getRequest();
        }
        $this->view->exception = $errors->exception;
        $this->view->request   = $errors->request;
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:

                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->error = $this->translate("notfound_error");
                $this->renderScript('error/40x.phtml');
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->error = $this->translate("application_error");
                $this->renderScript('error/50x.phtml');
                break;
        }
    }

    /**
     * This method will be called if the user does not have access to the resource
     * specified.
     * @return void
     */
    public function deniedAction() {
    	$this->view->title = $this->translate("error_encountered");
    	$this->view->error = $this->translate("denied_error");
    	$this->getResponse()->setHttpResponseCode(403); // forbidden, 
    	                                                // alternative 401 unauthorized 
    	                                                // s. http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
    }
}