<?php 
if(!defined('BASEPATH')) exit('No Direct Scripts Allowed');

    class Operator_info extends CI_Controller{
    
    public function __construct(){
        parent:: __construct();
        $this->load->model(array('Appapi_model','Encryption_model'));
    }
        
    public function getoperator(){

        $mobile = $this->post("mobile");

        if(empty($mobile)){
            
            $this->response([
                'status' => FALSE,
                'result' => 'Missing parameters.'
            ]);

        }else{
        $data = $this->Appapi_model->check_operator($mobile);
        if($data)
        {
            $this->response([
                'status' => TRUE,
                'result' => $data
            ], REST_Controller::HTTP_OK);
        }
        else
        {
            $this->response([
                'status' => FALSE,
                'result' => 'No data found.'
            ]);
        }
     }
    }
    
    }
    ?>