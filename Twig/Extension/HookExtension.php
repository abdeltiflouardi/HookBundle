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
        $value = null;
        if (array_key_exists($key, $this->configs)) {
            $value = $this->configs[$key];

            if (is_array($value)) {
                $value = $value[array_rand($value)];
            }
        }
        $scheme = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https' : 'http';

        $value = str_replace('{{_scheme}}', $scheme, $value);

        return $value;
    }
    
    function getName()
    {
        return 'hook';
    }

}