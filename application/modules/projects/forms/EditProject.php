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
                    ->addValidator('StringLength', false, array(3,255))
                    ->setRequired(true)
                    ->setValue(isset($values['name']) ? $values['name'] : null)
                    ->setLabel('project_admin_field_name');

            # name-jp
            $namejp = $this->createElement('text', 'name_jp')
                    ->addValidator('StringLength', false, array(3,255))
                    ->setRequired(true)
                    ->setValue(isset($values['name_jp']) ? $values['name_jp'] : null)
                    ->setLabel('project_admin_field_name_jp');

            # status
            $status = $this->createElement('select','status')
                    ->setLabel('project_admin_field_status')
                    ->setRequired(true)
                    ->setValue(isset($values['status']) ? $values['status'] : 'planned')
                    ->setMultiOptions(array('planned'=>'project_status_planned',
                                            'progress'=>'project_status_progress',
                                            'pending'=>'project_status_pending',
                                            'completed'=>'project_status_completed',
                                            'dropped'=>'project_status_dropped',
                                            'licensed'=>'project_status_licensed'));


            # desctiption
            $description = $this->createElement('textarea','description');
            $description->setValue(isset($values['description']) ? $values['description'] : null)
                    ->setRequired(true)
                    ->setAttrib('cols',40)
                    ->setAttrib('rows',15)
                    ->setLabel('description');

            $poster = $this->createElement('text', 'poster')
                    ->addValidator('StringLength', false, array(5,255))
                    ->setRequired(true)
                    ->setValue(isset($values['poster']) ? $values['poster'] : null)
                    ->setLabel('project_admin_field_poster');

            $miniposter = $this->createElement('text', 'mini_poster')
                    ->addValidator('StringLength', false, array(5,255))
                    ->setRequired(true)
                    ->setValue(isset($values['mini_poster']) ? $values['mini_poster'] : null)
                    ->setLabel('project_admin_field_mini_poster');

            $private = $this->createElement('radio', 'private')
                    ->setMultiOptions(array(
                        'yes'=>'yes_term',
                        'no'=>'no_term'
                        ))
                    ->setRequired(true)
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
                ->addElement('submit', $insert ? 'add' : 'update', array('label' => $insert ? 'add' : 'update', 'class'=>'button'));
    }
}