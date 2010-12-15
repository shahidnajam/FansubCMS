<?php
class News_Form_Comment extends Zend_Form {
    public function __construct($action,$options=null) {
        parent::__construct($options);

        $this->setName('postcomment')
                ->setAction($action)
                ->setMethod('post')
                ->setAttrib('id', 'postcomment');

        # author
        $author = $this->createElement('text', 'author');
        $author->addValidator('alnum')
                ->addValidator('regex', false, array('/^[a-zA-Z]+/'))
                ->addValidator('stringLength', false, array(5, 32))
                ->setRequired(true)
                ->setLabel('news_comment_field_author');

        # email
        $email = $this->createElement('text', 'email');
        $email->addValidator('EmailAddress', false)
                ->setRequired(true)
                ->setLabel('news_comment_field_email');

        # url
        $url = $this->createElement('text', 'url');
        $url->setRequired(false)
                ->setLabel('news_comment_field_website');

        # comment
        $comment = $this->createElement('Textarea','comment');
        $comment->addValidator('NotEmpty')
                ->setRequired(true)
                ->setAttrib('rows',15)
                ->setAttrib('cols',40)
                ->setLabel('news_comment_field_text');

        #captcha
        if(!User::isLoggedIn()) {
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
        }
        # add elements to the form
        $this->addElement($author)
                ->addElement($email)
                ->addElement($url)
                ->addElement($comment);

        if(!User::isLoggedIn())
            $this->addElement($captcha);

        # commit button
        $this->addElement('submit', 'submit', array('label' => 'news_comment_field_submit', 'class'=>'button'));
    }
}
