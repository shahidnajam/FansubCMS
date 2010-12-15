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
                ->addValidator('StringLength', false, array(3,255))
                ->setRequired(true)
                ->setValue(isset($values['title']) ? $values['title'] : null)
                ->setLabel('project_admin_field_episode_title');

        # crc
        $crc = $this->createElement('text', 'crc')
                ->addFilter('stringToUpper')
                ->addValidator('Hex')
                ->addValidator('StringLength',false,array(8,8))
                ->setRequired(true)
                ->setValue(isset($values['crc']) ? $values['crc'] : null)
                ->setLabel('project_admin_field_episode_crc');

        # number
        $number = $this->createElement('text', 'number')
                ->addValidator('Int')
                ->setRequired(true)
                ->setValue(isset($values['number']) ? $values['number'] : null)
                ->setLabel('project_admin_field_episode_number');

        # number
        $version = $this->createElement('text', 'version')
                ->addValidator('Int')
                ->setRequired(true)
                ->setValue(isset($values['version']) ? $values['version'] : '1')
                ->setLabel('project_admin_field_episode_version');

        # acodec
        $acodec = $this->createElement('text', 'acodec')
                ->addValidator('StringLength', false, array(3,10))
                ->setRequired(true)
                ->setValue(isset($values['acodec']) ? $values['acodec'] : null)
                ->setLabel('project_admin_field_episode_acodec');

        # container
        $container = $this->createElement('text', 'container')
                ->addValidator('StringLength', false, array(3,10))
                ->setRequired(true)
                ->setValue(isset($values['container']) ? $values['container'] : null)
                ->setLabel('project_admin_field_episode_container');

        # vcodec
        $vcodec = $this->createElement('text', 'vcodec')
                ->addValidator('StringLength', false, array(4,10))
                ->setRequired(true)
                ->setValue(isset($values['vcodec']) ? $values['vcodec'] : null)
                ->setLabel('project_admin_field_episode_vcodec');

        # projects
        $table = Doctrine::getTable('Project');
        $project = $this->createElement('select','project')
                ->setRequired(true)
                ->setLabel('project_admin_field_episode_project')
                ->setValue(isset($values['project_id']) ? $values['project_id'] : null)
                ->setMultiOptions($table->getProjects());

        # released
        if(isset($values['released_at'])) {
            if(empty($values['released_at'])) {
                $released = 'no';
            } else {
                $released = 'yes';
            }
        } else {
            $released = 'no';
        }
        $released = $this->createElement('radio', 'released')
                ->setMultiOptions(array(
                'yes'=>'yes_term',
                'no'=>'no_term'
                ))
                ->setRequired(true)
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
                ->addElement('submit', $insert ? 'add' : 'update', array('label' => $insert ? 'add' : 'update', 'class'=>'button'));
    }
}