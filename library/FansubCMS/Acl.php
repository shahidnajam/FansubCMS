<?php
class FansubCMS_Acl extends Zend_Acl {
    public function __construct( $options = null ) {
        if (is_array($options)) {
            $this->setOptions($options);
        } elseif ($options instanceof Zend_Config) {
            $this->setConfig($options);
        }
    }

    public function setConfig( Zend_Config $config ) {
        $this->setOptions($config->toArray());
    }


    public function setOptions( array $options ) {
        if ( isset($options['resources']) ) {
            $this->addResources( $options['resources'] );
            unset( $options['resources'] );
        }

        if( isset($options['roles']) ) {
            $this->addRoles( $options['roles'] );
            unset( $options['roles'] );
        }

        $allowed = array(
                'Allow', 'Deny'
        );

        foreach ( $options as $key => $value ) {
            $normalized = ucfirst($key);
            if ( !in_array( $normalized, $allowed ) ) {
                continue;
            }

            $method = 'set' . $normalized;
            if (method_exists($this, $method)) {

                $this->$method($value);
            }
        }
    }

    public function addResources( array $resources ) {
        foreach( $resources as $source ) {
            $resource = $source['resource'];
            $parent   = isset($source['parent']) ? $source['parent'] : null;
            $this->addResource( new Zend_Acl_Resource($resource), $parent );
        }
    }

    public function addRoles( array $roles ) {
        foreach( $roles as $source) {
            $role    = $source['role'];
            $inherit = isset($source['inherit']) ? $source['inherit'] : null;
            $this->addRole(new Zend_Acl_Role($role), $inherit);
        }
    }

    public function setAllow( array $allow ) {
        foreach( $allow as $source ) {
            $role       = (array) $source['role'];
            $resource   = isset($source['resource']) ? (array)$source['resource'] : null;
            $privileges = array_key_exists('privileges', $source) ? (array) $source['privileges'] : null;

            $class = null;
            if( array_key_exists('assert', $source) ) {
                if( $class  = $source['assert'] ) {
                    if( !class_exists( $assert ) ) {
                        throw new Zend_Acl_Exception(sprintf('Class %s does not exist', $class));
                    }
                }
            }
            
            $assert = $class ? new $class : $class;
            $this->allow($role, $resource, $privileges, $assert);
        }
    }

    public function setDeny( array $deny ) {
        foreach( $deny as $source ) {
            $role       = (array) $source['role'];
            $resource   = $source['resource'] ? (array) $source['resource'] : null;
            $privileges = array_key_exists('privileges', $source) ? (array) $source['privileges'] : null;

            $class = null;
            if( array_key_exists('assert', $source) ) {
                if( $class  = $source['assert'] ) {
                    if( !class_exists( $assert ) ) {
                        throw new Zend_Acl_Exception(sprintf('Class %s does not exist', $class));
                    }
                }
            }
            
            $assert = $class ? new $class : $class;
            $this->deny($role, $resource, $privileges, $assert);
        }
    }
}