<?php

class Projects_Form_EditProject extends Zend_Form {

    public function __construct(array $values = array(), $insert = false, $options = array()) {
        parent::__construct($options);

        $this->setName($insert ? 'addproject' : 'editproject')
                ->setAction('#')
                ->setMethod('post')
                ->setAttrib('id', $insert ? 'addproject' : 'editproject');

        # name
        $name = $this->createElement('text', 'name')
                        ->addValidator('StringLength', false, array(
                            'min' => 3,
                            'max' => 255,
                            'messages' => array(
                                Zend_Validate_StringLength::TOO_LONG => 'default_form_error_length',
                                Zend_Validate_StringLength::TOO_SHORT => 'default_form_error_length'
                                )))
                        ->setRequired(true)
                        ->addValidator('NotEmpty', true, array(
                            'messages' => array(
                                Zend_Validate_NotEmpty::IS_EMPTY => 'default_form_error_empty_value'
                                )))
                        ->setValue(isset($values['name']) ? $values['name'] : null)
                        ->setLabel('project_admin_field_name');

        # name-jp
        $namejp = $this->createElement('text', 'name_jp')
                        ->addValidator('StringLength', false, array(
                            'min' => 3,
                            'max' => 255,
                            'messages' => array(
                                Zend_Validate_StringLength::TOO_LONG => 'default_form_error_length',
                                Zend_Validate_StringLength::TOO_SHORT => 'default_form_error_length'
                                )))
                        ->setRequired(true)
                        ->addValidator('NotEmpty', true, array(
                            'messages' => array(
                                Zend_Validate_NotEmpty::IS_EMPTY => 'default_form_error_empty_value'
                                )))
                        ->setValue(isset($values['name_jp']) ? $values['name_jp'] : null)
                        ->setLabel('project_admin_field_name_jp');

        # status
        $status = $this->createElement('select', 'status')
                        ->setLabel('project_admin_field_status')
                        ->setRequired(true)
                        ->addValidator('NotEmpty', true, array(
                            'messages' => array(
                                Zend_Validate_NotEmpty::IS_EMPTY => 'default_form_error_empty_value'
                                )))
                        ->setValue(isset($values['status']) ? $values['status'] : 'planned')
                        ->setMultiOptions(array('planned' => 'project_status_planned',
                            'progress' => 'project_status_progress',
                            'pending' => 'project_status_pending',
                            'completed' => 'project_status_completed',
                            'dropped' => 'project_status_dropped',
                            'licensed' => 'project_status_licensed'));


        # desctiption
        $description = $this->createElement('textarea', 'description');
        $description->setValue(isset($values['description']) ? $values['description'] : null)
                ->setRequired(true)
                ->addValidator('NotEmpty', true, array(
                    'messages' => array(
                        Zend_Validate_NotEmpty::IS_EMPTY => 'default_form_error_empty_value'
                        )))
                ->setAttrib('cols', 40)
                ->setAttrib('rows', 15)
                ->setLabel('description');

        $poster = $this->createElement('text', 'poster')
                        ->addValidator('StringLength', false, array(
                            'min' => 5,
                            'max' => 255,
                            'messages' => array(
                                Zend_Validate_StringLength::TOO_LONG => 'default_form_error_length',
                                Zend_Validate_StringLength::TOO_SHORT => 'default_form_error_length'
                                )))
                        ->setRequired(true)
                        ->addValidator('NotEmpty', true, array(
                            'messages' => array(
                                Zend_Validate_NotEmpty::IS_EMPTY => 'default_form_error_empty_value'
                                )))
                        ->setValue(isset($values['poster']) ? $values['poster'] : null)
                        ->setLabel('project_admin_field_poster');

        $miniposter = $this->createElement('text', 'mini_poster')
                        ->addValidator('StringLength', false, array(
                            'min' => 5,
                            'max' => 255,
                            'messages' => array(
                                Zend_Validate_StringLength::TOO_LONG => 'default_form_error_length',
                                Zend_Validate_StringLength::TOO_SHORT => 'default_form_error_length'
                                )))
                        ->setRequired(true)
                        ->addValidator('NotEmpty', true, array(
                            'messages' => array(
                                Zend_Validate_NotEmpty::IS_EMPTY => 'default_form_error_empty_value'
                                )))
                        ->setValue(isset($values['mini_poster']) ? $values['mini_poster'] : null)
                        ->setLabel('project_admin_field_mini_poster');

        $private = $this->createElement('radio', 'private')
                        ->setMultiOptions(array(
                            'yes' => 'yes_term',
                            'no' => 'no_term'
                        ))
                        ->setLabel('project_admin_field_private')
                        ->setValue(isset($values['private']) ? $values['private'] : 'yes');

        # add elements to the form
        $this->addElement($name)
                ->addElement($namejp)
                ->addElement($status)
                ->addElement($description)
                ->addElement($poster)
                ->addElement($miniposter)
                ->addElement($private)
                # edit button
                ->addElement('submit', $insert ? 'add' : 'update', array('label' => $insert ? 'add' : 'update', 'class' => 'button'));
    }

}