<?php
class News_Form_EditNews extends Zend_Form {
    public function __construct($values=array(),$insert=false,$options=null) {
        parent::__construct($options);

        $this->setName($insert ? 'addnews' : 'editnews')
                ->setAction('#')
                ->setMethod('post')
                ->setAttrib('id', $insert ? 'addnews' : 'editnews');
        
        $title = $this->createElement('text', 'title')
                    ->setRequired(true)
                    ->setLabel('news_admin_add_field_title')
                    ->setValue(isset($values['title']) ? $values['title'] : null);

        $text = $this->createElement('textarea', 'text')
                    ->setRequired(true)
                    ->setAttrib('rows',15)
                    ->setAttrib('cols',40)
                    ->setLabel('news_admin_add_field_text')
                    ->setValue(isset($values['text']) ? $values['text'] : null);

        $public = $this->createElement('radio', 'public')
                    ->setMultiOptions(array(
                        'yes'=>'yes_term',
                        'no'=>'no_term'
                        ))
                    ->setRequired(true)
                    ->setLabel('news_admin_add_field_public')
                    ->setValue(isset($values['public']) ? $values['public'] : 'no');
       

        # add elements to the form
        $this->addElement($title)
                ->addElement($text)
                ->addElement($public);

        # commit button
        $this->addElement('submit', 'submit', array('label' => $insert ? 'field_add' : 'field_edit', 'class' => 'button'));
    }
}