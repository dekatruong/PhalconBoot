<?php

use PhalconBoot\AbstractPhalconBoot;

class Bootstrap extends AbstractPhalconBoot {
    
    protected function declareConfigDir() {
        return ROOT_PATH.'/app/config';
    }
    
    protected function declareAppEnv() {
        return [1 => 'local'];
    }
    
    protected function registerAutoloaders(\Phalcon\Di $di, \Phalcon\Loader &$loader) {
        //Load config       
        $config = $di->getShared('config');
        
        /**
         * Register a set of directories taken from the configuration file
         */
        $loader->registerNamespaces([
            'Application\Controllers'   => $config->application->controllersDir,
        ])->register();

        
        return $loader;
    }

    protected function registerServices(\Phalcon\Di &$di) {
        
        //Load config       
        $config = $di->getShared('config');
        
        /**
         * Setting up the view component
         */
        $di->set('view', function() use ($config) {
            $view = new \Phalcon\Mvc\View();
            $view->setViewsDir($config->application->viewsDir);
            $view->registerEngines(array(
                '.php' => 'Phalcon\Mvc\View\Engine\Php',
                '.html' => 'Phalcon\Mvc\View\Engine\Php',
            ));
            return $view;
        }, true);
        
        //Setup dispatcher
        $di->set('dispatcher', function() {
            $dispatcher = new \Phalcon\Mvc\Dispatcher();
            $dispatcher->setDefaultNamespace('Application\Controllers\\');
            return $dispatcher;
        }, true);
        
        return $di;
    }

}
