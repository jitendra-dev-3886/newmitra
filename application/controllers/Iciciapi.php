<?php
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('display_errors', 0);
date_default_timezone_set('Asia/Kolkata');

class Iciciapi extends CI_Controller {
    
    public function __construct()
     {
	    parent::__construct();
	    $this->load->helper('form');
	    $this->load->helper('url');
   		$this -> load -> model('Iciciapi_model');
   		$this->load->library(array('session'));
   		$this->file_path = realpath(APPPATH . '../assets/csv');
  	}
  	
	public function index()
	{
        $CURLOPT_URL="https://api.icicibank.com:8443/api/Corporate/CIB/v1/Registration";
        $values=array("AGGRNAME"=>"REALPAY","AGGRID"=>"OTOE0061","CORPID"=>"PRACHICIB1","USERID"=>"USER3","URN"=>"SR193802374");
        // { "Response": "SUCCESS", "Message": "User details are saved successfully and pending for self approval.", "CORP_ID": "PRACHICIB1", "USER_ID": "USER3", "AGGR_ID": "BAAS0021", "AGGR_NAME":"SATMAT", "URN":"SR189932540" }
        // 		{ "Response": "Failure", "Message": "REGISTRATION FAILED.CORPORATE ALREADY REGISTERED." }
		$this->Welcome_model->register_api($CURLOPT_URL,$values);
	}
	public function registration_status()
	{
        $CURLOPT_URL="https://apigwuat.icicibank.com:8443/api/Corporate/CIB/v1/RegistrationStatus";
		$values=array("AGGRNAME"=>"REALPAY","AGGRID"=>"OTOE0061","CORPID"=>"PRACHICIB1","USERID"=>"USER3","URN"=>"SR193802374","ALIASID"=>"SATMATGROUP");
		// { "Status": "Pending for Self Approval", "ResponseCode": "0000", "RESPONSE": "Success" }
        // { "Status": "Registered", "ResponseCode": "0000", "RESPONSE": "Success" }
		$this->Welcome_model->register_api($CURLOPT_URL,$values);
	}
	public function transaction()
	{
        $CURLOPT_URL="https://apigwuat.icicibank.com:8443/api/Corporate/CIB/v1/Transaction";
        $values=array("AGGRID"=>"BAAS0021","AGGRNAME"=>"SATMAT","CORPID"=>"570075344","USERID"=>"USER1","URN"=>"SR189932540","UNIQUEID"=>"13579086543213","DEBITACC"=>"000451000301","CREDITACC"=>"000405002777","IFSC"=>"ICIC0000011","AMOUNT"=>"11.00","CURRENCY"=>"INR","TXNTYPE"=>"OWN","PAYEENAME"=>"SATMAT");
        // { "REQID": "341584", "STATUS": "SUCCESS", "UNIQUEID": "13579086543213", "UTRNUMBER":"056870221", "RESPONSE": "SUCCESS", "URN":"SR189932540" }
		$this->Welcome_model->register_api($CURLOPT_URL,$values);
	}
	public function transaction_inquiry()
	{
        $CURLOPT_URL="https://apigwuat.icicibank.com:8443/api/Corporate/CIB/v1/TransactionInquiry";
		$values=array("AGGRID"=>"BAAS0021","CORPID"=>"570075344","USERID"=>"USER1","UNIQUEID"=>"13579086543213","URN"=>"SR189932540");
        // { "RESPONSE": "SUCCESS", "STATUS": "SUCCESS", "URN": "SR189932540", "UNIQUEID": "13579086543213", "UTRNUMBER": "056870221" }
		$this->Welcome_model->register_api($CURLOPT_URL,$values);
	}
	public function account_statement()
	{
        $CURLOPT_URL="https://apigwuat.icicibank.com:8443/api/Corporate/CIB/v1/AccountStatement";
		$values=array("AGGRID"=>"BAAS0021","CORPID"=>"570075344","USERID"=>"USER1","ACCOUNTNO"=>"000451000301","FROMDATE"=>"01-01-2016","TODATE"=>"31-12-2020","URN"=>"SR189932540");
		$this->Welcome_model->register_api($CURLOPT_URL,$values);
	}
	public function balance_inquiry()
	{
        $CURLOPT_URL="https://apigwuat.icicibank.com:8443/api/Corporate/CIB/v1/BalanceInquiry";
		$values=array("AGGRID"=>"BAAS0021","CORPID"=>"570075344","USERID"=>"USER1","URN"=>"SR189932540","ACCOUNTNO"=>"000451000301");
        // { "RESPONSE": "SUCCESS", "AGGR_ID": "BAAS0021", "CORP_ID": "PRACHICIB1", "USER_ID": "USER3", "URN":"SR189932540", "ACCOUNTNO": "000451000301", "DATE":"10/12/20 10:45:59", "EFFECTIVEBAL":"64421550.37", "CURRENCY":"INR" }
        $this->Welcome_model->register_api($CURLOPT_URL,$values);
	}
	public function deregistration()
	{
        $CURLOPT_URL="https://apigwuat.icicibank.com:8443/api/Corporate/CIB/v1/Deregistration";
		$values=array("AGGRNAME"=>"SATMAT","AGGRID"=>"BAAS0021","CORPID"=>"570075344","USERID"=>"USER1","URN"=>"SR189932540");
		// {"Response":"SUCCESS","URN":"SR189932540","AGGR_NAME":"SATMAT","Message":"Request Cancelled Successfully","AGGR_ID":"BAAS0021","CORP_ID":"PRACHICIB1","USER_ID":"USER3"}
		$this->Welcome_model->register_api($CURLOPT_URL,$values);
	}
}
