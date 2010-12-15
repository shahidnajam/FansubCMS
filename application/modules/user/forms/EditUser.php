<?php
class User_Form_EditUser extends Zend_Form {
    public function __construct(array $values = array(), $insert = false, $options = array()) {
        parent::__construct($options);

        $this->setName($insert ? 'adduser' : 'edituser')
             ->setAction('#')
             ->setMethod('post')
             ->setAttrib('id', $insert ? 'adduser' : 'edituser');

            # username
            $username = $this->createElement('text', 'username')
                    ->addValidator('StringLength', false, array(3,255))
                    ->setRequired(true)
                    ->setValue(isset($values['name']) ? $values['name'] : null)
                    ->setLabel('user_admin_field_username');

            # password
            $password = $this->createElement('password', 'password1');
            $password->addValidator('StringLength', false, array(8))
                ->setRequired($insert ? true : false)
                ->setLabel('user_admin_field_password');

            # retype password
            $repassword = $this->createElement('password', 'password2');
            $repassword->addValidator('StringLength', false, array(8))
                ->addValidator('Identical',false)
                ->setRequired($insert ? true : false)
                ->setLabel('user_admin_field_retype_password');

            # Email
            $email = $this->createElement('text','email');
            $email->addValidator('EmailAddress',false)
            ->setRequired(true)
            ->setValue(isset($values['email']) ? $values['email'] : null)
            ->setLabel('email');

            # Roles
            $roles = $this->createElement('multiCheckbox', 'roles')
                    ->setLabel('user_admin_field_roles')
                    ->setValue(isset($values['UserRole']) ? $values['UserRole'] : null)
                    ->setMultiOptions(UserRole::getRoles());

            # Tasks
            $table = Doctrine_Core::getTable('Task');
            if(count($table->getTasks())) {
                $tasks = $this->createElement('multiCheckbox', 'tasks')
                        ->setLabel('user_admin_field_tasks')
                        ->setValue(isset($values['UserTask']) ? $values['UserTask'] : null)
                        ->setMultiOptions($table->getTasks());
            }

            # Profiltext
            $description = $this->createElement('textarea','description');
            $description->setValue(isset($values['description']) ? $values['description'] : null)
            ->setAttrib('cols',40)
            ->setAttrib('rows',15)
            ->setLabel('description');

            $team = $this->createElement('radio', 'show_team')
                    ->setMultiOptions(array(
                        'yes'=>'yes_term',
                        'no'=>'no_term'
                        ))
                    ->setRequired(true)
                    ->setLabel('user_admin_field_show_team')
                    ->setValue(isset($values['show_team']) ? $values['show_team'] : 'yes');

            $active = $this->createElement('radio', 'active')
                    ->setMultiOptions(array(
                        'yes'=>'yes_term',
                        'no'=>'no_term'
                        ))
                    ->setRequired(true)
                    ->setLabel('user_admin_field_active')
                    ->setValue(isset($values['active']) ? $values['active'] : 'yes');

            $activated = $this->createElement('radio', 'activated')
                    ->setMultiOptions(array(
                        'yes'=>'yes_term',
                        'no'=>'no_term'
                        ))
                    ->setRequired(true)
                    ->setLabel('user_admin_field_activated')
                    ->setValue(isset($values['activated']) ? $values['activated'] : 'yes');

            # add elements to the form
            $this->addElement($username)
                ->addElement($password)
                ->addElement($repassword)
                ->addElement($email)
                ->addElement($roles);
            if(isset($tasks)) {
                $this->addElement($tasks);
            }
            $this->addElement($description)
                ->addElement($team)
                ->addElement($active)
                ->addElement($activated)
                # edit button
                ->addElement('submit', $insert ? 'add' : 'update', array('label' => $insert ? 'add' : 'update', 'class'=>'button'));
    }

    public function isValid($data) {
    	$retypePw = $this->getElement('password2');
    	$retypePw->getValidator('Identical')->setToken($data['password1']);
    	return parent::isValid($data);
    }
}