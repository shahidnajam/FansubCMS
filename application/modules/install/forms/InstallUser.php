<?php

class Install_Form_InstallUser extends Zend_Form {

    public function __construct($options=array()) {
        parent::__construct($options);
        $this->setName('adduser')
                ->setAction('#')
                ->setMethod('post')
                ->setAttrib('id', 'adduser');

        # username
        $username = $this->createElement('text', 'username')
                        ->addValidator('StringLength', false, array(3, 255))
                        ->setRequired(true)
                        ->addValidator('NotEmpty', true, array(
                            'messages' => array(
                                Zend_Validate_NotEmpty::IS_EMPTY => 'default_form_error_empty_value'
                                )))
                        ->setLabel('user_admin_field_username');

        # password
        $password = $this->createElement('password', 'password1');
        $password->addValidator('StringLength', true, array(
                    'min' => 8,
                    'max' => 64,
                    'messages' => array(
                        Zend_Validate_StringLength::TOO_LONG => 'default_form_error_length',
                        Zend_Validate_StringLength::TOO_SHORT => 'default_form_error_length'
                        )))
                ->setRequired(true)
                ->addValidator('NotEmpty', true, array(
                    'messages' => array(
                        Zend_Validate_NotEmpty::IS_EMPTY => 'default_form_error_empty_value'
                        )))
                ->setLabel('user_admin_field_password');

        # retype password
        $repassword = $this->createElement('password', 'password2');
        $repassword->addValidator('StringLength', true, array(
                    'min' => 8,
                    'max' => 64,
                    'messages' => array(
                        Zend_Validate_StringLength::TOO_LONG => 'default_form_error_length',
                        Zend_Validate_StringLength::TOO_SHORT => 'default_form_error_length'
                        )))
                ->addValidator('Identical', false, array(
                    'messages' => array(
                        Zend_Validate_Identical::NOT_SAME => 'user_form_error_passwords_not_match'
                        )))
                ->setRequired(true)
                ->addValidator('NotEmpty', true, array(
                    'messages' => array(
                        Zend_Validate_NotEmpty::IS_EMPTY => 'default_form_error_empty_value'
                        )))
                ->setLabel('user_admin_field_retype_password');

        # Email
        $email = $this->createElement('text', 'email');
        $email->addValidator('EmailAddress', false, array(
                    'messages' => array(
                        Zend_Validate_EmailAddress::DOT_ATOM => 'default_form_error_email',
                        Zend_Validate_EmailAddress::INVALID_FORMAT => 'default_form_error_email',
                        Zend_Validate_EmailAddress::INVALID_HOSTNAME => 'default_form_error_email',
                        Zend_Validate_EmailAddress::INVALID_LOCAL_PART => 'default_form_error_email',
                        Zend_Validate_EmailAddress::INVALID_MX_RECORD => 'default_form_error_email',
                        Zend_Validate_EmailAddress::INVALID_SEGMENT => 'default_form_error_email',
                        Zend_Validate_EmailAddress::LENGTH_EXCEEDED => 'default_form_error_email',
                        Zend_Validate_EmailAddress::QUOTED_STRING => 'default_form_error_email'
                        )))
                ->setRequired(true)
                ->addValidator('NotEmpty', true, array(
                    'messages' => array(
                        Zend_Validate_NotEmpty::IS_EMPTY => 'default_form_error_empty_value'
                        )))
                ->setLabel('email');

        # add elements to the form
        $this->addElement($username)
                ->addElement($password)
                ->addElement($repassword)
                ->addElement($email)
                # edit button
                ->addElement('submit', 'submit', array('label' => 'install_default_create'));
    }

    public function isValid($data) {
        $retypePw = $this->getElement('password2');
        $retypePw->getValidator('Identical')->setToken($data['password1']);
        return parent::isValid($data);
    }

}