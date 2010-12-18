<?php

class User_Form_EditTask extends Zend_Form {

    public function __construct(array $values = array(), $insert = false, $options = array()) {
        parent::__construct($options);

        $this->setName($insert ? 'addtask' : 'edittask')
                ->setAction('#')
                ->setMethod('post')
                ->setAttrib('id', $insert ? 'addtask' : 'edittask');

        # username
        $name = $this->createElement('text', 'taskname')
                        ->addValidator('StringLength', false, array(
                            'min'=>3,
                            'max'=>255,
                            'messages'=>array(
                                Zend_Validate_StringLength::TOO_LONG => 'default_form_error_length',
                                Zend_Validate_StringLength::TOO_SHORT => 'default_form_error_length'
                            )))
                        ->addValidator('NotEmpty', true, array(
                            'messages' => array(
                                Zend_Validate_NotEmpty::IS_EMPTY => 'default_form_error_empty_value'
                                )))
                        ->setRequired(true)
                        ->setValue(isset($values['name']) ? $values['name'] : null)
                        ->setLabel('user_admin_field_taskname');


        # add elements to the form
        $this->addElement($name)
                # edit button
                ->addElement('submit', $insert ? 'add' : 'update', array('label' => $insert ? 'add' : 'update', 'class' => 'button'));
    }

}