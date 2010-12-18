<?php

class Projects_Form_EditProjectEpisode extends Zend_Form {

    public function __construct(array $values = array(), $insert = false, $options = array()) {
        parent::__construct($options);

        $this->setName($insert ? 'addprojectepisode' : 'editprojectepisode')
                ->setAction('#')
                ->setMethod('post')
                ->setAttrib('id', $insert ? 'addprojectepisode' : 'editprojectepisode');

        # title
        $title = $this->createElement('text', 'title')
                        ->addValidator('StringLength', false, array(
                            'min' => 2,
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
                        ->setValue(isset($values['title']) ? $values['title'] : null)
                        ->setLabel('project_admin_field_episode_title');

        # crc
        $crc = $this->createElement('text', 'crc')
                        ->addFilter('stringToUpper')
                        ->addValidator('Hex', false, array(
                            'messages'=>array(
                            Zend_Validate_Hex::NOT_HEX => 'project_episode_form_error_crc_hex'
                            )
                        ))
                        ->addValidator('StringLength', false, array(
                            'min'=>8,
                            'max'=>8,
                            'messages'=>array(
                                Zend_Validate_StringLength::TOO_LONG => 'project_episode_form_error_crc_length',
                                Zend_Validate_StringLength::TOO_SHORT => 'project_episode_form_error_crc_length'
                            )))
                        ->addValidator('NotEmpty', true, array(
                            'messages' => array(
                                Zend_Validate_NotEmpty::IS_EMPTY => 'default_form_error_empty_value'
                                )))
                        ->setRequired(true)
                        ->setValue(isset($values['crc']) ? $values['crc'] : null)
                        ->setLabel('project_admin_field_episode_crc');

        # number
        $number = $this->createElement('text', 'number')
                        ->addValidator('Int', false, array(
                            'meesages' => array(
                                Zend_Validate_Int::NOT_INT => 'default_form_error_int'
                            )
                        ))
                        ->setRequired(true)
                        ->addValidator('NotEmpty', true, array(
                            'messages' => array(
                                Zend_Validate_NotEmpty::IS_EMPTY => 'default_form_error_empty_value'
                                )))
                        ->setValue(isset($values['number']) ? $values['number'] : null)
                        ->setLabel('project_admin_field_episode_number');

        # number
        $version = $this->createElement('text', 'version')
                        ->addValidator('Int', false, array(
                            'messages' => array(
                                Zend_Validate_Int::NOT_INT => 'default_form_error_int'
                            )
                        ))
                        ->setRequired(true)
                        ->addValidator('NotEmpty', true, array(
                            'messages' => array(
                                Zend_Validate_NotEmpty::IS_EMPTY => 'default_form_error_empty_value'
                                )))
                        ->setValue(isset($values['version']) ? $values['version'] : '1')
                        ->setLabel('project_admin_field_episode_version');

        # acodec
        $acodec = $this->createElement('text', 'acodec')
                        ->addValidator('StringLength', false, array(
                            'min' => 3,
                            'max' => 10,
                            'messages' => array(
                                Zend_Validate_StringLength::TOO_LONG => 'default_form_error_length',
                                Zend_Validate_StringLength::TOO_SHORT => 'default_form_error_length'
                                )))
                        ->setRequired(true)
                        ->addValidator('NotEmpty', true, array(
                            'messages' => array(
                                Zend_Validate_NotEmpty::IS_EMPTY => 'default_form_error_empty_value'
                                )))
                        ->setValue(isset($values['acodec']) ? $values['acodec'] : null)
                        ->setLabel('project_admin_field_episode_acodec');

        # container
        $container = $this->createElement('text', 'container')
                        ->addValidator('StringLength', false, array(
                            'min' => 3,
                            'max' => 10,
                            'messages' => array(
                                Zend_Validate_StringLength::TOO_LONG => 'default_form_error_length',
                                Zend_Validate_StringLength::TOO_SHORT => 'default_form_error_length'
                                )))
                        ->setRequired(true)
                        ->addValidator('NotEmpty', true, array(
                            'messages' => array(
                                Zend_Validate_NotEmpty::IS_EMPTY => 'default_form_error_empty_value'
                                )))
                        ->setValue(isset($values['container']) ? $values['container'] : null)
                        ->setLabel('project_admin_field_episode_container');

        # vcodec
        $vcodec = $this->createElement('text', 'vcodec')
                        ->addValidator('StringLength', false, array(
                            'min' => 4,
                            'max' => 10,
                            'messages' => array(
                                Zend_Validate_StringLength::TOO_LONG => 'default_form_error_length',
                                Zend_Validate_StringLength::TOO_SHORT => 'default_form_error_length'
                                )))
                        ->setRequired(true)
                        ->addValidator('NotEmpty', true, array(
                            'messages' => array(
                                Zend_Validate_NotEmpty::IS_EMPTY => 'default_form_error_empty_value'
                                )))
                        ->setValue(isset($values['vcodec']) ? $values['vcodec'] : null)
                        ->setLabel('project_admin_field_episode_vcodec');

        # projects
        $table = Doctrine::getTable('Project');
        $project = $this->createElement('select', 'project')
                        ->setRequired(true)
                        ->addValidator('NotEmpty', true, array(
                            'messages' => array(
                                Zend_Validate_NotEmpty::IS_EMPTY => 'default_form_error_empty_value'
                                )))
                        ->setLabel('project_admin_field_episode_project')
                        ->setValue(isset($values['project_id']) ? $values['project_id'] : null)
                        ->setMultiOptions($table->getProjects());

        # released
        if (isset($values['released_at'])) {
            if (empty($values['released_at'])) {
                $released = 'no';
            } else {
                $released = 'yes';
            }
        } else {
            $released = 'no';
        }
        $released = $this->createElement('radio', 'released')
                        ->setMultiOptions(array(
                            'yes' => 'yes_term',
                            'no' => 'no_term'
                        ))
                        ->setLabel('project_admin_field_episode_released')
                        ->setValue($released);

        # add elements to the form
        $this->addElement($title)
                ->addElement($project)
                ->addElement($number)
                ->addElement($version)
                ->addElement($container)
                ->addElement($vcodec)
                ->addElement($acodec)
                ->addElement($crc)
                ->addElement($released)
                # edit button
                ->addElement('submit', $insert ? 'add' : 'update', array('label' => $insert ? 'add' : 'update', 'class' => 'button'));
    }

}