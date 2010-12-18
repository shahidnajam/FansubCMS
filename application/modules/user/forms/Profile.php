<?php

class User_Form_Profile extends Zend_Form {

    public function __construct($action, $options=array()) {
        parent::__construct($options);

        $user = Zend_Auth::getInstance()->getIdentity();

        $this->setName('profile')
                ->setAction('#')
                ->setMethod('post')
                ->setAttrib('id', 'profile');

        # password
        $password = $this->createElement('password', 'password1');
        $password->addValidator('StringLength', false, array(
                    'min' => 8,
                    'max' => 64,
                    'messages' => array(
                        Zend_Validate_StringLength::TOO_LONG => 'default_form_error_length',
                        Zend_Validate_StringLength::TOO_SHORT => 'default_form_error_length'
                        )))
                ->setRequired(false)
                ->setLabel('user_admin_field_new_password');

        # retype password
        $repassword = $this->createElement('password', 'password2');
        $repassword->addValidator('StringLength', false, array(
                    'min' => 8,
                    'max' => 64,
                    'messages' => array(
                        Zend_Validate_StringLength::TOO_LONG => 'default_form_error_length',
                        Zend_Validate_StringLength::TOO_SHORT => 'default_form_error_length'
                        )))
                ->addValidator('Identical', false)
                ->setRequired(false)
                ->setLabel('user_admin_field_retype_new_password');

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
                ->setValue($user->email)
                ->setLabel('email');

        # Profiltext
        $description = $this->createElement('textarea', 'description');
        $description->setValue($user->description)
                ->setAttrib('cols', 40)
                ->setAttrib('rows', 15)
                ->setLabel('description');


        # add elements to the form
        $this->addElement($password)
                ->addElement($repassword)
                ->addElement($email)
                ->addElement($description)
                # edit button
                ->addElement('submit', 'update', array('label' => 'update', 'class' => 'button'));
    }

    public function isValid($data) {
        $retypePw = $this->getElement('password2');
        $retypePw->getValidator('Identical')->setToken($data['password1']);
        return parent::isValid($data);
    }

}