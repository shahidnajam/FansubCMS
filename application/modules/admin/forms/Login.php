<?php

class Admin_Form_Login extends Zend_Form {

    public function __construct($options = null) {
        parent::__construct($options);

        $this->setName('login')
                ->setAction('#')
                ->setMethod('post')
                ->setAttrib('id', 'login');

        # username
        $username = $this->createElement('text', 'username');
        $username->addValidator('NotEmpty', true, array(
                    'messages' => array(
                        Zend_Validate_NotEmpty::IS_EMPTY => 'default_form_error_empty_value'
                        )))
                ->setRequired(true)
                ->addFilter('StringToLower')
                ->setLabel('admin_field_username');

        # password
        $password = $this->createElement('password', 'password');
        $password->addValidator('NotEmpty', true, array(
                    'messages' => array(
                        Zend_Validate_NotEmpty::IS_EMPTY => 'default_form_error_empty_value'
                        )))
                ->setRequired(true)
                ->setLabel('admin_field_password');

        # add elements to the form
        $this->addElement($username)
                ->addElement($password)
                # login button
                ->addElement('submit', 'submit', array('label' => 'admin_field_login', 'class' => 'button'));
    }

}