<?php
class Group_Form_Contact extends Zend_Form {
    public function __construct($action,$options=null) {
        parent::__construct($options);

        $this->setName('contact')
        ->setAction($action)
        ->setMethod('post')
        ->setAttrib('id', 'contact_form');

        # author
        $author = $this->createElement('text', 'author');
        $author->addValidator('stringLength', false, array(5, 32))
        ->setRequired(true)
        ->setLabel('contact_name');
            
        # email
        $email = $this->createElement('text', 'email');
        $email->addValidator('EmailAddress', false)
        ->setRequired(true)
        ->setLabel('contact_email');

        # content
        $comment = $this->createElement('Textarea','content');
        $comment->addValidator('NotEmpty')
        ->setRequired(true)
        ->setAttrib('rows',15)
        ->setAttrib('cols',40)
        ->setLabel('contact_content');

        #captcha
        $imgUrl = substr($_SERVER['PHP_SELF'],0,-9).'/images/captcha'; // little hack to have the correct baseurl
        $imgUrl = str_replace('//','/',$imgUrl);
            $captcha = new Zend_Form_Element_Captcha('captcha', array(
                'label' => 'captcha',
                'captcha' => array(
                    'captcha' => 'Image',
                    'wordLen' => 4,
                    'timeout' => 300,
                    'height' => 80,
                    'width' => 150,
                    'startImage' => null,
                    'font' => realpath(APPLICATION_PATH.'/data/ttf').'/captcha.ttf',
                    'imgurl' => $imgUrl
            ),
            ));
        $captcha->setRequired(true);
            
        # add elements to the form
        $this->addElement($author)
        ->addElement($email)
        ->addElement($comment)
        ->addElement($captcha);

        # commit button
        $this->addElement('submit', 'submit', array('label' => 'group_contact_submit','class'=>'button'));
    }
}