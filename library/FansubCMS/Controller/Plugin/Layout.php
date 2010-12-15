<?php
class FansubCMS_Controller_Plugin_Layout extends Zend_Controller_Plugin_Abstract {

    /**
     * Array of layout paths associating modules with layouts
     */
    protected $_moduleLayouts;

    /**
     * Registers a module layout.
     * This layout will be rendered when the specified module is called.
     * If there is no layout registered for the current module, the default layout as specified
     * in Zend_Layout will be rendered
     *
     * @param String $name		The name of the module/controller
     * @param String $layoutPath	The path to the layout
     * @param String $layout		The name of the layout to render
     */
    public function registerAdminLayout($name, $layoutPath, $layout=null) {
        $this->_moduleLayouts[$name] = array(
                'layoutPath' => $layoutPath,
                'layout' => $layout
        );
    }

    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        if(isset($this->_moduleLayouts[$request->getModuleName()])) {
            $config = $this->_moduleLayouts[$request->getModuleName()];

            $layout = Zend_Layout::getMvcInstance();
            if($layout->getMvcEnabled()) {
                $layout->setLayoutPath($config['layoutPath']);

                if($config['layout'] !== null) {
                    $layout->setLayout($config['layout']);
                }
            }
        }
        if(isset($this->_moduleLayouts[$request->getControllerName()])) {
            $config = $this->_moduleLayouts[$request->getControllerName()];

            $layout = Zend_Layout::getMvcInstance();
            if($layout->getMvcEnabled()) {
                $layout->setLayoutPath($config['layoutPath']);

                if($config['layout'] !== null) {
                    $layout->setLayout($config['layout']);
                }
            }
        }
    }
}