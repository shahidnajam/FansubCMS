<?php
class Form_InstallUser extends Zend_Form {
	public function __construct($options=array()) {
		parent::__construct($options);
		$this->setName('adduser')
		->setAction('#')
		->setMethod('post')
		->setAttrib('id', 'adduser');

		# name
		$name = $this->createElement('text','name');
		$name->addValidator('StringLength',false,array(3,32))
		->setRequired(true)
		->setLabel('Username');

		# password
		$password = $this->createElement('password', 'password1');
		$password->addValidator('StringLength', false, array(8))
		->setRequired(true)
		->setLabel('Password');

		# retype password
		$repassword = $this->createElement('password', 'password2');
		$repassword->addValidator('StringLength', false, array(8))
		->addValidator('Identical',false)
		->setRequired(true)
		->setLabel('Retype password');

		# Email
		$email = $this->createElement('text','email');
		$email->addValidator('EmailAddress',false)
		->setRequired(true)
		->setLabel('E-Mail');

		# add elements to the form
		$this->addElement($name)
		->addElement($password)
		->addElement($repassword)
		->addElement($email)
		# edit button
		->addElement('submit', 'submit', array('label' => 'Create'));
	}

	public function isValid($data) {
		$retypePw = $this->getElement('password2');
		$retypePw->getValidator('Identical')->setToken($data['password1']);
		return parent::isValid($data);
	}
}