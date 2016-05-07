<?php

namespace Application\Controllers;

/**
 * @author Deka Truong <dekatruong@gmail.com>
 */
abstract class ControllerBase extends \Phalcon\Mvc\Controller {

    public function beforeExecuteRoute($dispatcher) {
       
//        //Check authencation
//        $user_identity = $this->session->get('user-identity');
//        if (!$user_identity) {
//            //Case has not auth yet: forward to login           
//            //  Method 1: to other controller action process this request
//            $this->dispatcher->forward(array(
//                    "controller" => "index",
//                    "action"     => "login"
//                ));
//            //  Method 2: new request
//            //$this->response->redirect('index/login');
//        }       
    }

   
    /**
     * Action support function
     * 
     * @param array $data_array
     */
    protected function sendJson($data_array){
        //Response setting
        $this->response->setContentType('application/json', 'UTF-8');
        
        $out = json_encode($data_array, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        
        $this->response->appendContent($out); //Note: use setConent if response this json only
        
        $this->response->send();
        
        return; 
    }
}
