<?php
class Projects_Form_EditProjectScreenshot extends Zend_Form {
    public function __construct(array $values = array(), $insert = false, $options = array()) {
        parent::__construct($options);

        $this->setName($insert ? 'addprojectscreenshot' : 'editprojectscreenshot')
                ->setAction('#')
                ->setMethod('post')
                ->setAttrib('id', $insert ? 'addprojectscreenshot' : 'editprojectscreenshot');

        # screen
        if(!is_dir(realpath(UPLOAD_PATH.'/screenshots/'))) {
                if(!mkdir(realpath(UPLOAD_PATH).DIRECTORY_SEPARATOR.'screenshots',0777)) throw new Zend_Exception('Could not create screenshot directory.');
        }
        $screen = $this->createElement('file','screen')
                ->setRequired($insert ? true : false)
                ->setDestination(realpath(UPLOAD_PATH.'/screenshots'))
              //  ->addValidator('Count', false, 1)
                ->addValidator('IsImage', false, 'jpeg,jpg,png,gif')
                ->setLabel('project_admin_field_screenshot_screen');

        # projects
        $table = Doctrine::getTable('Project');
        $project = $this->createElement('select','project')
                ->setRequired(true)
                ->setLabel('project_admin_field_screenshot_project')
                ->setValue(isset($values['project_id']) ? $values['project_id'] : null)
                ->setMultiOptions($table->getProjects());

        # add elements to the form
        $this->addElement($screen)
                ->addElement($project)
                # edit button
                ->addElement('submit', $insert ? 'add' : 'update', array('label' => $insert ? 'add' : 'update', 'class'=>'button'));
    }

    public function getUniqueCode($length = "") {
		$code = md5(uniqid(rand(), true));
		if ($length != "")
                       return substr($code, 0, $length);
		else
                       return $code;
    }

    public function isValid($data) {
        if($this->screen instanceof Zend_Form_Element_File) {
            $oldname = pathinfo($this->screen->getFileName());
            $newname = $this->getUniqueCode(32) . '.' . $oldname['extension'];
            $this->screen->addFilter('Rename',$newname);
        }
        return parent::isValid($data);
    }
}