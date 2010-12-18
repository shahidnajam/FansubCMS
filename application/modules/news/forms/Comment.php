<?php

class News_Form_Comment extends Zend_Form {

    public function __construct($action, $options=null) {
        parent::__construct($options);

        $this->setName('postcomment')
                ->setAction($action)
                ->setMethod('post')
                ->setAttrib('id', 'postcomment');

        # author
        $author = $this->createElement('text', 'author');
        $author->addValidator('NotEmpty', true, array(
                'messages'=> array(
                Zend_Validate_NotEmpty::IS_EMPTY => 'default_form_error_empty_value'
                )))
            ->addValidator('alnum', true, array(
                    'messages' => array(
                        Zend_Validate_Alnum::NOT_ALNUM => 'news_comment_form_error_author_alnum'
                    )
                ))
                //               ->addValidator('regex', false, array(
                //                   'pattern' => '/^[a-zA-Z]+/',
                //                   'messages' => array(
                //                       Zend_Validate_Regex::NOT_MATCH => 'news_comment_form_error_regex'
                //                   )))
                ->addValidator('stringLength', false, array(
                    'min' => 3,
                    'max' => 32,
                    'messages' => array(
                            Zend_Validate_StringLength::TOO_LONG => 'news_comment_form_error_author_length',
                            Zend_Validate_StringLength::TOO_SHORT => 'news_comment_form_error_author_length'
                        )))
                ->setRequired(true)
                ->setLabel('news_comment_field_author');
        # email
        $email = $this->createElement('text', 'email');
        $email->addValidator('NotEmpty', true, array(
                'messages'=> array(
                    Zend_Validate_NotEmpty::IS_EMPTY => 'default_form_error_empty_value'
                )))
            ->addValidator('EmailAddress', false, array(
                    'messages' => array(
                        Zend_Validate_EmailAddress::DOT_ATOM => 'news_comment_form_error_email_email',
                        Zend_Validate_EmailAddress::INVALID_FORMAT => 'news_comment_form_error_email_email',
                        Zend_Validate_EmailAddress::INVALID_HOSTNAME => 'news_comment_form_error_email_email',
                        Zend_Validate_EmailAddress::INVALID_LOCAL_PART => 'news_comment_form_error_email_email',
                        Zend_Validate_EmailAddress::INVALID_MX_RECORD => 'news_comment_form_error_email_email',
                        Zend_Validate_EmailAddress::INVALID_SEGMENT => 'news_comment_form_error_email_email',
                        Zend_Validate_EmailAddress::LENGTH_EXCEEDED => 'news_comment_form_error_email_email',
                        Zend_Validate_EmailAddress::QUOTED_STRING => 'news_comment_form_error_email_email'
                    )
                ))
                ->setRequired(true)
                ->setLabel('news_comment_field_email');
        # url
        $url = $this->createElement('text', 'url');
        $url->setRequired(false)
                ->setLabel('news_comment_field_website');

        # comment
        $comment = $this->createElement('Textarea', 'comment');
        $comment->setRequired(true)
                ->addValidator('NotEmpty', true, array(
                    'messages'=> array(
                        Zend_Validate_NotEmpty::IS_EMPTY => 'default_form_error_empty_value'
                )))
                ->setAttrib('rows', 15)
                ->setAttrib('cols', 40)
                ->setLabel('news_comment_field_text');

        #captcha
        if (!User::isLoggedIn()) {
            $imgUrl = substr($_SERVER['PHP_SELF'], 0, -9) . '/images/captcha'; // little hack to have the correct baseurl
            $imgUrl = str_replace('//', '/', $imgUrl);
            $captcha = new Zend_Form_Element_Captcha('captcha', array(
                        'label' => 'captcha',
                        'captcha' => array(
                            'captcha' => 'Image',
                            'wordLen' => 6,
                            'timeout' => 300,
                            'height' => 80,
                            'width' => 150,
                            'startImage' => null,
                            'font' => realpath(APPLICATION_PATH . '/data/ttf') . '/captcha.ttf',
                            'imgurl' => $imgUrl,
                        ),
                        'errorMessages'=>array(
                            'default_form_error_captcha_wrong'
                        )
                    ));
            $captcha->setRequired(true);
        }
        # add elements to the form
        $this->addElement($author)
                ->addElement($email)
                ->addElement($url)
                ->addElement($comment);

        if (!User::isLoggedIn())
            $this->addElement($captcha);

        # commit button
        $this->addElement('submit', 'submit', array('label' => 'news_comment_field_submit', 'class' => 'button'));
    }

}
