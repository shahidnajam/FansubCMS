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
                ->addValidator('StringLength', false, array(3,255))
                ->setRequired(true)
                ->setLabel('user_admin_field_username');

        # password
        $password = $this->createElement('password', 'password1');
        $password->addValidator('StringLength', false, array(8))
                ->setRequired(true)
                ->setLabel('user_admin_field_password');

        # retype password
        $repassword = $this->createElement('password', 'password2');
        $repassword->addValidator('StringLength', false, array(8))
                ->addValidator('Identical',false)
                ->setRequired(true)
                ->setLabel('user_admin_field_retype_password');

        # Email
        $email = $this->createElement('text','email');
        $email->addValidator('EmailAddress',false)
                ->setRequired(true)
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