<?php

namespace OS\HookBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent,
    Symfony\Bundle\TwigBundle\TwigEngine;

class HookListener
{

    protected $templating;
    protected $configs;

    public function __construct(TwigEngine $templating, $configs)
    {
        $this->templating = $templating;
        $this->configs = $configs;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        // Exclude this listener in this excludes paths
        if (key_exists('excludes', $this->configs)) {
            $excludes = $this->configs['excludes'];
            foreach ($excludes as $exclude) {
                if (preg_match(sprintf('|%s|', $exclude), $event->getRequest()->getPathInfo())) {
                    return;
                }
            }

            unset($this->configs['excludes']);
        }

        $scheme = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https' : 'http';

        foreach ($this->configs as $key => $config) {
            if (is_array($config)) {
                foreach ($config as $k => $v) {
                    $this->configs[$key][$k] = str_replace('{{_scheme}}', $scheme, $v);
                }
            } else {
                $this->configs[$key] = str_replace('{{_scheme}}', $scheme, $config);
            }
        }        

        $response = $event->getResponse();

        if (function_exists('mb_stripos')) {
            $posrFunction = 'mb_strripos';
        } else {
            $posrFunction = 'strripos';
        }

        $content = $response->getContent();

        foreach ($this->configs as $search => $replace) {
            if (false !== $pos = $posrFunction($content, $search)) {
                $replaceContent = $replace;
                if (is_array($replace)) {
                    $replaceContent = $replace[array_rand($replace)];
                }

                $content = preg_replace(sprintf('/%s/', $search), $replaceContent, $content);
                $response->setContent($content);
            }
        }
    }

}