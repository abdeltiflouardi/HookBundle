<?php

namespace OS\HookBundle\Twig\Extension;

class HookExtension extends \Twig_Extension
{

    private $configs;

    public function __construct($configs)
    {
        $this->configs = $configs;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'hook' => new \Twig_Function_Method($this, 'hook'),
        );
    }

    public function hook($key) {
        if (array_key_exists($key, $this->configs)) {
            return $this->configs[$key];
        }

        return null;
    }
    
    function getName()
    {
        return 'hook';
    }

}