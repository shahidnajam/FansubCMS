<?php
/*
 *  This file is part of FansubCMS.
 *
 *  FansubCMS is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  FansubCMS is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with FansubCMS.  If not, see <http://www.gnu.org/licenses/>
*/

class Projects_Form_EditProjectEpisode extends Zend_Form {

    public function __construct(array $values = array(), $insert = false, $options = array()) {
        parent::__construct($options);

        $this->setName($insert ? 'addprojectepisode' : 'editprojectepisode')
                ->setAction('#')
                ->setMethod('post')
                ->setAttrib('id', $insert ? 'addprojectepisode' : 'editprojectepisode');

        # title
        $title = $this->createElement('text', 'title')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty', true, array(
                'messages' => array(
                        Zend_Validate_NotEmpty::IS_EMPTY => 'default_form_error_empty_value'
                )))
                ->addValidator('StringLength', false, array(
                'min' => 2,
                'max' => 255,
                'messages' => array(
                        Zend_Validate_StringLength::TOO_LONG => 'default_form_error_length',
                        Zend_Validate_StringLength::TOO_SHORT => 'default_form_error_length'
                )))
                ->setRequired(true)
                ->setValue(isset($values['title']) ? $values['title'] : null)
                ->setLabel('project_admin_field_episode_title');

        # crc
        $crc = $this->createElement('text', 'crc')
                ->addFilter('stringToUpper')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty', true, array(
                'messages' => array(
                        Zend_Validate_NotEmpty::IS_EMPTY => 'default_form_error_empty_value'
                )))
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
                ->setRequired(true)
                ->setValue(isset($values['crc']) ? $values['crc'] : null)
                ->setLabel('project_admin_field_episode_crc');

        # number
        $number = $this->createElement('text', 'number')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty', true, array(
                'messages' => array(
                        Zend_Validate_NotEmpty::IS_EMPTY => 'default_form_error_empty_value'
                )))
                ->addValidator('Int', false, array(
                'meesages' => array(
                        Zend_Validate_Int::NOT_INT => 'default_form_error_int'
                )
                ))
                ->setRequired(true)
                ->setValue(isset($values['number']) ? $values['number'] : null)
                ->setLabel('project_admin_field_episode_number');

        # number
        $version = $this->createElement('text', 'version')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty', true, array(
                'messages' => array(
                        Zend_Validate_NotEmpty::IS_EMPTY => 'default_form_error_empty_value'
                )))
                ->addValidator('Int', false, array(
                'messages' => array(
                        Zend_Validate_Int::NOT_INT => 'default_form_error_int'
                )
                ))
                ->setRequired(true)
                ->setValue(isset($values['version']) ? $values['version'] : '1')
                ->setLabel('project_admin_field_episode_version');

        # acodec
        $acodec = $this->createElement('text', 'acodec')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty', true, array(
                'messages' => array(
                        Zend_Validate_NotEmpty::IS_EMPTY => 'default_form_error_empty_value'
                )))
                ->addValidator('StringLength', false, array(
                'min' => 3,
                'max' => 10,
                'messages' => array(
                        Zend_Validate_StringLength::TOO_LONG => 'default_form_error_length',
                        Zend_Validate_StringLength::TOO_SHORT => 'default_form_error_length'
                )))
                ->setRequired(true)
                ->setValue(isset($values['acodec']) ? $values['acodec'] : null)
                ->setLabel('project_admin_field_episode_acodec');

        # container
        $container = $this->createElement('text', 'container')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty', true, array(
                'messages' => array(
                        Zend_Validate_NotEmpty::IS_EMPTY => 'default_form_error_empty_value'
                )))
                ->addValidator('StringLength', false, array(
                'min' => 3,
                'max' => 10,
                'messages' => array(
                        Zend_Validate_StringLength::TOO_LONG => 'default_form_error_length',
                        Zend_Validate_StringLength::TOO_SHORT => 'default_form_error_length'
                )))
                ->setRequired(true)
                ->setValue(isset($values['container']) ? $values['container'] : null)
                ->setLabel('project_admin_field_episode_container');

        # vcodec
        $vcodec = $this->createElement('text', 'vcodec')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty', true, array(
                'messages' => array(
                        Zend_Validate_NotEmpty::IS_EMPTY => 'default_form_error_empty_value'
                )))
                ->addValidator('StringLength', false, array(
                'min' => 4,
                'max' => 10,
                'messages' => array(
                        Zend_Validate_StringLength::TOO_LONG => 'default_form_error_length',
                        Zend_Validate_StringLength::TOO_SHORT => 'default_form_error_length'
                )))
                ->setRequired(true)
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
        # release date
        $releaseDate = $this->createElement('text', 'releasedate')
                ->setValue(isset($values['released_at']) ? date("d.m.Y",strtotime($values['released_at'])) : null)
                ->setLabel('project_admin_field_episode_release_date');
        $iso = $this->createElement('hidden', 'isoDate')
                ->setValue(isset($values['released_at']) ? $values['released_at'] : null);
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
                ->addElement($releaseDate)
                ->addElement($iso)
                # edit button
                ->addElement('submit', $insert ? 'add' : 'update', array('label' => $insert ? 'add' : 'update', 'class' => 'button'));
    }

}