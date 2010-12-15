<?php
class FansubCMS_Form_Confirmation extends Zend_Form {
    public function  __construct($options=null) {
        parent::__construct($options);
        $this->setName('confirmation')
                ->setAction('#')
                ->setMethod('post');

        # add elements to the form
        $this->addElement('submit', 'yes', array('label' => 'yes_term', 'class'=>'button'))
             ->addElement('submit', 'no', array('label' => 'no_term', 'class'=>'button'));
    }
}