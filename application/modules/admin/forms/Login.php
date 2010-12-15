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
        $username->addValidator('stringLength', false, array(3, 32))
                 ->setRequired(true)
                 ->addFilter('StringToLower')
                 ->addValidator('notEmpty')
                 ->setLabel('admin_field_username');

        # password
        $password = $this->createElement('password', 'password');
        $password->setRequired(true)
                 ->setLabel('admin_field_password');

        # add elements to the form
        $this->addElement($username)
             ->addElement($password)
             # login button
             ->addElement('submit', 'submit', array('label' => 'admin_field_login', 'class' => 'button'));
    }
}