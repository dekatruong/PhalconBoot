<?php

namespace PhalconBoot;

use Phalcon\Config;
use Phalcon\Di;
use Phalcon\Di\FactoryDefault as DefaultDi;
use Phalcon\Loader;
use Phalcon\Mvc\Application; 

/**
 * PhalconBoot for  Phalcon\Mvc\Application, use Phalcon\Config and Phalcon\Di
 *
 * @author Deka
 */
abstract class AbstractPhalconBoot {
    
    /**
     * Map Config Inheritance-Level and Environment Name
     * @var array 
     */
    protected $LEVEL_TO_ENV_MAP = [
        1 => 'live',
        2 => 'lab',
        3 => 'local',
    ];

    /**
     * Default: 1-live, 2-lab, 3-local
     * @var string 
     */
    protected $APP_ENV = 'live'; //Default


    /**
     *
     * @var Di 
     */
    protected $di;
    /**
     *
     * @var Config 
     */
    protected $config;
    /**
     *
     * @var Application 
     */
    protected $application;

    /**
     *
     * @var string 
     */
    protected $config_dir;
    
    protected $loader;


    public function __construct($app_env = null) {
        //Phalcon dependency. To do: not new directly
        if(!$app_env){$app_env = $this->LEVEL_TO_ENV_MAP[1];}  //Top level
        $this->APP_ENV      = $app_env;  
        $this->di           = new DefaultDi();       
        $this->loader       = new Loader();
        $this->application  = new Application();
        
        
        //Declare base on sub-class
        $this->config_dir       = $this->declareConfigDir();
        $this->LEVEL_TO_ENV_MAP = $this->declareAppEnv();
    }


    /**
     * 
     * @param array $envOrder [<level1>,<level2>,<level3>]
     * @return \PhalconBoot\AbstractPhalconBoot
     */
    public function setConfigEnvLevel(array $envOrder = ['live', 'lab', 'local']) {
        
        $this->LEVEL_TO_ENV_MAP = $envOrder;
        
        return $this;
    }
    
    public function setAppEnv($app_env) {
        $this->APP_ENV = $app_env;        
        return $this;
    }
    
    protected function declareConfigDir(){
        return ROOT_PATH.'/app/config'; //require declaring ROOT_PATH
    }
    
    protected function declareAppEnv() {
        return [
            1 => 'live',
            2 => 'lab',
            3 => 'local',
        ];
    }

    /**
     * 
     * @param Di $di  refer to $this->di
     * @param Loader $loader refer to $this->loader
     * @return Loader
     */
    protected abstract function registerAutoloaders(Di $di, Loader &$loader);
           
    protected abstract function registerServices(Di &$di);
    
    
    /**
    * recursively load config and merge (overwrite) from high level (top order) to low level (tail order), 
    * until the given level
    * @param int $level 
    * normally, the order is:
    * [
    *  1 => 'live',
    *  2 => 'lab',
    *  3 => 'local'
    * ]
    * @return \Phalcon\Config 
    */
    protected function loadConfigToLevel($level = 1) {    
        if($level <= 1){
            $config =  new \Phalcon\Config(include $this->config_dir.'/'.$this->LEVEL_TO_ENV_MAP[1].'/config.php'); //To do: load all file in folder
        } else {
            $parent_config = $this->loadConfigToLevel($level-1);
            $current_config = new \Phalcon\Config(include $this->config_dir.'/'.$this->LEVEL_TO_ENV_MAP[$level].'/config.php'); //To do: load all file in folder
            //inheritance from parent-config
            $config = ($parent_config) 
                ? $parent_config->merge($current_config) 
                : $current_config; 
        }

        return $config;
    }
    
    public function run() {
          
        /**
         * Read the configuration     
         */
        $config = $this->loadConfigToLevel(array_search($this->APP_ENV, $this->LEVEL_TO_ENV_MAP)); //To do

        
        /**
         * Config to DI
         */
        $this->di->set('config', $config, true);
        
        /**
         * Include loader
         * Note: must after load config
         */
        $this->registerAutoloaders($this->di, $this->loader);    

        /**
         * Include services
         * Note: must after load config
         */
        $di = $this->registerServices($this->di);

        /**
         * Handle the request
         */        
        $this->application->setDI($di);

        //Handle
        echo $this->application->handle()->getContent();
        
    }
}
