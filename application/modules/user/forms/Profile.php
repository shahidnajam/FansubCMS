<?php
class User_Form_Profile extends Zend_Form {
    public function __construct($action,$options=array()) {
        parent::__construct($options);

        $user = Zend_Auth::getInstance()->getIdentity();

        $this->setName('profile')
             ->setAction('#')
             ->setMethod('post')
             ->setAttrib('id', 'profile');

            # password
            $password = $this->createElement('password', 'password1');
            $password->addValidator('StringLength', false, array(8))
                ->setRequired(false)
                ->setLabel('user_admin_field_new_password');

            # retype password
            $repassword = $this->createElement('password', 'password2');
            $repassword->addValidator('StringLength', false, array(8))
                ->addValidator('Identical',false)
                ->setRequired(false)
                ->setLabel('user_admin_field_retype_new_password');

            # Email
            $email = $this->createElement('text','email');
            $email->addValidator('EmailAddress',false)
            ->setRequired(true)
            ->setValue($user->email)
            ->setLabel('email');

            # Profiltext
            $description = $this->createElement('textarea','description');
            $description->setValue($user->description)
            ->setAttrib('cols',40)
            ->setAttrib('rows',15)
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