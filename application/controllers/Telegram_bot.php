<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
date_default_timezone_set('Asia/Kolkata');
class Telegram_bot extends CI_Controller

{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Rec_model');
		$this->load->model('db_model');
		$this->load->model('encryption_model');
		$this->load->model('email_model');
		$this->load->model('msg_model');
	}
    
    public function bot() {

        // print_r($this->encryption_model->decode('9915c64472ff03a172a0cdc667bba280'));echo "<br>";
        // print_r($this->encryption_model->decode('16a519ceb04071167faff7359c03493e'));exit;
        $input = file_get_contents('php://input');
        $data = json_decode($input);
        $chat_id = $data->message->chat->id;
        $text = $data->message->text;
        $name = $data->message->chat->first_name;

        $text_count1 = explode(' ', $text);
        $text_count = substr_count($text, " ") + 1;

        if ($text == "/start") {

            $text1 = "Welcome to Easypayall %0APlease enter your Mobile No. and password%0A Eg. 8989877656 1234password";
        }   elseif (preg_match("/^[0-9]/i", $text) && strlen($text) >= 10) {
            $recharge_details = explode(' ', $text);
            $mobile = $recharge_details[0];
            $pass = $recharge_details[1];
            // $result = $this->db->insert('images', $chat_id);
            $mobile1 = $this->encryption_model->encode($mobile);
            $pass1 = $this->encryption_model->encode($pass);
            $query = $this->db->query("select * from customers where cus_mobile = '$mobile1' and cus_pass = '$pass1'")->result();
            if ($query) {
                $query1 = $this->db->query("UPDATE customers SET chat_id = '$chat_id' WHERE cus_mobile = '$mobile1'");
                $text1 = 'Welcome to Easypayall%0AHow can i help you,%0A1 Mobile recharge%0A2 DTH recharge%0A3 Electricity bill payment%0A4 Postpaid Bill paymnet%0A5 Check account balance%0A6 Add Money%0A7 Account history%0A8 Main Menu%0A9 Logout %0APlease type the option number to proceed (Ex.3)';
            } else {
                $text1 = "Mobile Number and password does not match";
            }
        }   elseif ($text == 1) {
            $query_db = $this->db->query("select * from customers where chat_id= '$chat_id'")->result();
            if ($query_db) {
                $text1 = "Please enter your operator name, mobile no. and amount %0A%0A e.g. RC Airtel 9988776655 199%0A e.g. RC Jio 9988776655 199%0Ae.g. RC Vodafone 9988776655 199%0A e.g. RC BSNL 9988776655 199%0A e.g. RC Idea 9988776655 199";
            } else {
                $text1 = "Please login to use services";
            }
        }   elseif ($text_count1[0] == 'RC') {

            $query_db = $this->db->query("select * from customers where chat_id= '$chat_id'")->result();
            if ($query_db) {

                $recharge_details = explode(' ', $text);

                if (preg_match("/^[a-zA-Z]/i", $recharge_details[1]) && preg_match("/^[0-9]{10}$/", $recharge_details[2]) && preg_match("/^[0-9]/i", $recharge_details[3])) {
                    $query = $this->db->query("select opcodenew from operator where operatorname = '$recharge_details[1]' and opsertype='mobile'")->result();
                    foreach ($query as $q) {
                        $opcodenew = $q->opcodenew;
                    }

                    $query1 = $this->db->query("select cus_id,cus_type from customers where chat_id = '$chat_id'")->result();
                    foreach ($query1 as $q1) {
                        $cus_id = $q1->cus_id;
                        $cus_type = $q1->cus_type;
                    }

                    $mode = 'Telegram';

                    $complete = $this->Rec_model->recharge_ot(array('cus_id' => $cus_id, 'amount' => $recharge_details[3], 'number' => $recharge_details[2], 'billing' => $billing, 'process' => $process, 'operator' => $opcodenew, 'mode' => $mode, 'city' => $city, 'type' => $cus_type, 'promo' => $promo));
                    if ($complete['type'] == '1') {
                        $msg = $complete['mess'];
                        $rcid = $complete['rcid'];
                        
                        if($msg=="Your Recharge is Success"){
                            $data = $this->db->query("select * from exr_rechrgreqexr_rechrgreq_fch  where recid= '$rcid'")->result();
                            $tran_id = $data[0]->wellborn_trans_no;
                            $mobileno = $data[0]->mobileno;
                            $amt = $data[0]->amount;
                            $text1 = $msg.'%0ATransaction id='.$tran_id.'%0AAmount='.$amt.'%0AMobile No='.$mobileno;
                        }elseif($msg=="Low Balance."){
                            $text1="Your recharge is failed due to insufficient balance.";
                        }else{
                            $text1="Your recharge is failed please try after some time";
                        }
                        
                    } else {
                        echo "recharge not succefull";
                    }
                } else {
                    $text1 = "please enter correct details";
                }
            } else {
                $text1 = "Please login to use services";
            }
        }   elseif ($text == 2) {
            $query_db = $this->db->query("select * from customers where chat_id= '$chat_id'")->result();
            if ($query_db) {
                $text1 = "Please enter your DTH name, DTH no and amount %0A%0A e.g. DTH Tatasky 65646576 200%0A e.g. DTH Airteldth 65646576 200%0A e.g. DTH Dishtv 65646576 200%0A e.g. DTH Sundirect 65646576 200%0A e.g. DTH TataSky 65646576 200%0A e.g. DTH Videocon 65646576 200";
            } else {
                $text1 = "Please login to use services";
            }
        }   elseif ($text_count1[0] == 'DTH') {
            $query_db = $this->db->query("select * from customers where chat_id= '$chat_id'")->result();
            if ($query_db) {
                $recharge_details = explode(' ', $text);

                if (preg_match("/^[a-zA-Z]/i", $recharge_details[1]) && preg_match("/^[0-9]/i", $recharge_details[2]) && preg_match("/^[0-9]/i", $recharge_details[3])) {
                    $query = $this->db->query("select opcodenew from operator where operatorname = '$recharge_details[1]' and opsertype='dth'")->result();
                    foreach ($query as $q) {
                        $opcodenew = $q->opcodenew;
                    }

                    $query1 = $this->db->query("select cus_id,cus_type from customers where chat_id = $chat_id")->result();
                    foreach ($query1 as $q1) {
                        $cus_id = $q1->cus_id;
                        $cus_type = $q1->cus_type;
                    }

                    $mode = 'Telegram';

                    $complete = $this->Rec_model->recharge_ot(array('cus_id' => $cus_id, 'amount' => $recharge_details[3], 'number' => $recharge_details[2], 'billing' => $billing, 'process' => $process, 'operator' => $opcodenew, 'mode' => $mode, 'city' => $city, 'type' => $cus_type, 'promo' => $promo));
                    if ($complete['type'] == '1') {
                        $msg = $complete['mess'];
                        $rcid = $complete['rcid'];
                        if($msg=="Low Balance."){
                            $text1="Your DTH recharge is failed due to insufficient balance.";
                        }elseif($msg=="Your Recharge is Success"){
                            $data = $this->db->query("select * from exr_rechrgreqexr_rechrgreq_fch  where recid= '$rcid'")->result();
                            $tran_id = $data[0]->wellborn_trans_no;
                            $mobileno = $data[0]->mobileno;
                            $amt = $data[0]->amount;
                            $text1 = $msg.'%0ATransaction id='.$tran_id.'%0AAmount='.$amt.'%0ADTH No='.$mobileno;
                        }else{
                            $text1="Your recharge is failed please try after some time";
                        }
                    } else {
                        echo "DTH recharge not succefull";
                    }
                } else {
                    $text1 = "please enter correct details dth";
                }
            } else {
                $text1 = "Please login to use services";
            }
        }   elseif ($text == 3) {
            $query_db = $this->db->query("select * from customers where chat_id= '$chat_id'")->result();
            if ($query_db) {
                $text1 = "Please enter your consumer no, biling unit and amount %0A%0Ae.g. ELC E101 65646576 200%0A%0AOperator cods for Electricity%0AE101- Mahavitaran-Maharashtra State Electricity Distribu... %0AE102- Goa Electricity Department %0AE103- Tata Power-Mumbai%0AE104- Torrent Power Ltd.%0AE105- Bangalore Electricity Supply Company Ltd. (BESCOM)%0AE106- Gulbarga Electricity Supply Company Ltd. (GESCOM)%0AE107- Adani Electricity%0AE108- BEST Mumbai%0AE109- SNDL Nagpur%0AE110- India Power Corporation Ltd. (Asansol)";
            } else {
                $text1 = "Please login to use services";
            }
        }   elseif ($text_count1[0] == 'ELC') {
            $query_db = $this->db->query("select * from customers where chat_id= '$chat_id'")->result();
            if ($query_db) {
                $recharge_details = explode(' ', $text);

                if (preg_match("/^[A-Z0-9]/i", $recharge_details[1]) && is_numeric($recharge_details[2]) && is_numeric($recharge_details[3])) {
                    if($recharge_details[1] == "E101"){
                        $operatorname = "Mahavitaran-Maharashtra State Electricity Distribution ";
                    }   elseif($recharge_details[1] == "E102"){
                        $operatorname = "Goa Electricity Department ";
                    }   elseif($recharge_details[1] == "E103"){
                        $operatorname = "Tata Power - Mumbai";
                    }   elseif($recharge_details[1] == "E104"){
                        $operatorname = "Torrent Power Ltd.";
                    }   elseif($recharge_details[1] == "E105"){
                        $operatorname = "Bangalore Electricity Supply Company Ltd. (BESCOM)";
                    }   elseif($recharge_details[1] == "E106"){
                        $operatorname = "Gulbarga Electricity Supply Company Ltd. (GESCOM)";
                    }   elseif($recharge_details[1] == "E107"){
                        $operatorname = "Adani Electricity";
                    }   elseif($recharge_details[1] == "E108"){
                        $operatorname = "BEST Mumbai";
                    }   elseif($recharge_details[1] == "E109"){
                        $operatorname = "SNDL Nagpur";
                    }   elseif($recharge_details[1] == "E110"){
                        $operatorname = "India Power Corporation Ltd. (Asansol)";
                    }   else{
                        $operatorname = "invalid";
                    }
                    
                    $query = $this->db->query("select opcodenew from operator where operatorname = '$operatorname' and opsertype='ELECTRICITY'")->result();
                    foreach ($query as $q) {
                        $opcodenew = $q->opcodenew;
                    }

                    $query1 = $this->db->query("select cus_id,cus_type from customers where chat_id = $chat_id")->result();
                    foreach ($query1 as $q1) {
                        $cus_id = $q1->cus_id;
                        $cus_type = $q1->cus_type;
                    }

                    $mode = 'Telegram';

                    $complete = $this->Rec_model->recharge_ot(array('cus_id' => $cus_id, 'amount' => $recharge_details[3], 'number' => $recharge_details[2], 'billing' => $billing, 'process' => $process, 'operator' => $opcodenew, 'mode' => $mode, 'city' => $city, 'type' => $cus_type, 'promo' => $promo));
                    if ($complete['type'] == '1') {
                        $msg = $complete['mess'];
                        $rcid = $complete['rcid'];
                        if($msg=="Low Balance."){
                            $text1="Your Electricity bill is failed due to insufficient balance.";
                        }elseif($msg=="Your Recharge is Success"){
                            $data = $this->db->query("select * from exr_rechrgreqexr_rechrgreq_fch  where recid= '$rcid'")->result();
                            $tran_id = $data[0]->wellborn_trans_no;
                            $mobileno = $data[0]->mobileno;
                            $amt = $data[0]->amount;
                            $text1 = 'Your Electricity bill is success%0ATransaction id='.$tran_id.'%0AAmount='.$amt.'%0AConsumer No='.$mobileno;
                        }else{
                            $text1="Your recharge is failed please try after some time";
                        }
                    } else {
                        echo "Electricity bill not succefull";
                    }
                } else {
                    $text1 = "please enter correct details dth";
                }
            } else {
                $text1 = "Please login to use services";
            }
        }   elseif ($text == 4) {
            $query_db = $this->db->query("select * from customers where chat_id= '$chat_id'")->result();
            if ($query_db) {
                $text1 = "Please enter your Mobile no, Operator and amount %0A%0Ae.g. POST P1 9988776655 199%0A%0AOperator Codes%0A P1 = BSNL Postpaid%0A P2= Airtel Postpaid%0A P3 = Idea Postpaid%0A P4 = Vodafone Postpaid%0A P5 = JIO Postpaid";
            } else {
                $text1 = "Please login to use services";
            }
        }   elseif ($text_count1[0] == 'POST') {
            $query_db = $this->db->query("select * from customers where chat_id= '$chat_id'")->result();
            if ($query_db) {

                $recharge_details = explode(' ', $text);

                if (preg_match("/^[a-zA-Z]/i", $recharge_details[1]) && preg_match("/^[0-9]{10}$/", $recharge_details[2]) && preg_match("/^[0-9]/i", $recharge_details[3])) {
                    if($recharge_details[1] == "P1"){
                        $operatorname = "BSNL Postpaid";
                    }   elseif($recharge_details[1] == "P2"){
                        $operatorname = "Airtel Postpaid";
                    }   elseif($recharge_details[1] == "P3"){
                        $operatorname = "Idea Postpaid";
                    }   elseif($recharge_details[1] == "P4"){
                        $operatorname = "Vodafone Postpaid";
                    }   elseif($recharge_details[1] == "P5"){
                        $operatorname = "JIO Postpaid";
                    }   else{
                        $operatorname = "invalid";
                    }
                    
                    
                    $query = $this->db->query("select opcodenew from operator where operatorname = '$operatorname' and opsertype='postpaid'")->result();
                    foreach ($query as $q) {
                        $opcodenew = $q->opcodenew;
                    }

                    $query1 = $this->db->query("select cus_id,cus_type from customers where chat_id = '$chat_id'")->result();
                    foreach ($query1 as $q1) {
                        $cus_id = $q1->cus_id;
                        $cus_type = $q1->cus_type;
                    }

                    $mode = 'Telegram';

                    $complete = $this->Rec_model->recharge_ot(array('cus_id' => $cus_id, 'amount' => $recharge_details[3], 'number' => $recharge_details[2], 'billing' => $billing, 'process' => $process, 'operator' => $opcodenew, 'mode' => $mode, 'city' => $city, 'type' => $cus_type, 'promo' => $promo));
                    if ($complete['type'] == '1') {
                        $msg = $complete['mess'];
                        $rcid = $complete['rcid'];
                        
                        if($msg=="Your Recharge is Success"){
                            $data = $this->db->query("select * from exr_rechrgreqexr_rechrgreq_fch  where recid= '$rcid'")->result();
                            $tran_id = $data[0]->wellborn_trans_no;
                            $mobileno = $data[0]->mobileno;
                            $amt = $data[0]->amount;
                            $text1 = 'Your postpaid bill is success%0ATransaction id='.$tran_id.'%0AAmount='.$amt.'%0AMobile No='.$mobileno;
                        }elseif($msg=="Low Balance."){
                            $text1="Your recharge is failed due to insufficient balance.";
                        }else{
                            $text1="Your recharge is failed please try after some time";
                        }
                        
                    } else {
                        echo "recharge not succefull";
                    }
                } else {
                    $text1 = "please enter correct details";
                }
            } else {
                $text1 = "Please login to use services";
            }
        }   elseif ($text == 5) {
            $query_db = $this->db->query("select * from customers where chat_id= '$chat_id'")->result();
            if ($query_db) {


                $query = $this->db->query("select cus_id from customers where chat_id = '$chat_id'")->result();
                foreach ($query as $q) {
                    $cus_id = $q->cus_id;
                }

                $query1 = $this->db->query("select txn_clbal from exr_trnx where txn_agentid = '$cus_id' order by txn_id desc limit 1")->result();
                if ($query1) {
                    foreach ($query1 as $q1) {
                        $txn_clbal = round($q1->txn_clbal, 2);
                    }
                } else {
                    $txn_clbal = 0;
                }

                $text1 = "Your availble balance in account is Rs ". $txn_clbal;
            } else {
                $text1 = "Please login to use services";
            }
        }   elseif ($text == 6) {
            $query_db = $this->db->query("select * from customers where chat_id= '$chat_id'")->result();
            if ($query_db) {
                $text1 = "In Working";
            } else {
                $text1 = "Please login to use services";
            }
        }   elseif ($text == 7) {
            $query_db = $this->db->query("select * from customers where chat_id= '$chat_id'")->result();
            if ($query_db) {
                $query = $this->db->query("select cus_id from customers where chat_id = '$chat_id'")->result();
                foreach ($query as $q) {
                    $cus_id = $q->cus_id;
                }
                $query1 = $this->db->query("select * from exr_trnx where txn_agentid = '$cus_id' order by txn_id limit 50")->result();
                $text1 = 'TRANSACTION DETAILS %0A%0A ID || TXN_TYPE || TXN_DATE || OP_BAL || CRDT || DBT || CL_BAL %0A%0A';
                foreach ($query1 as $q1) {
                    $txn_id = $q1->txn_id;
                    $txn_type = $q1->txn_type;
                    $txn_date = $q1->txn_date;
                    $txn_clbal = round($q1->txn_clbal, 2);
                    $txn_crdt = round($q1->txn_crdt, 2);
                    $txn_dbdt = round($q1->txn_dbdt, 2);
                    $txn_opbal = round($q1->txn_opbal, 2);
                    $text1 .= "$txn_id || $txn_type || $txn_date || $txn_opbal || $txn_crdt || $txn_dbdt || $txn_clbal %0A%0A";
                }
            } else {
                $text1 = "Please login to use services";
            }
        }   elseif ($text == 8) {
            $query_db = $this->db->query("select * from customers where chat_id= '$chat_id'")->result();
            if ($query_db) {
                $text1 = 'Welcome to Easypayall%0AHow can i help you,%0A1 Mobile recharge%0A2 DTH recharge%0A3 Electricity bill payment%0A4 Postpaid Bill paymnet%0A5 Check account balance%0A6 Add Money%0A7 Account history%0A8 Main Menu%0A9 Logout %0APlease type the option number to proceed (Ex.3)';
            } else {
                $text1 = "Please login to use services";
            }
        }   elseif ($text == "9") {
            $query_db = $this->db->query("select * from customers where chat_id= '$chat_id'")->result();
            if ($query_db) {
                $query = $this->db->query("UPDATE customers SET chat_id = '' WHERE chat_id = '$chat_id'");
                $text1 = 'logout Succefully';
            } else {
                $text1 = "Please login to use services";
            }
        }   elseif ($text_count > 1) {
            $query_db = $this->db->query("select * from customers where chat_id= '$chat_id'")->result();
            if ($query_db) {
                $text1 = "Please enter proper syntax";
            } else {
                $text1 = "Please login to use services";
            }
        } else {
            $query_db = $this->db->query("select * from customers where chat_id= '$chat_id'")->result();
            if ($query_db) {
                $text1 = "Please enter valid mobile no.";
            } else {
                $text1 = "Please login to use services ";
            }
        }


        $token = "2078190689:AAEX8rDasbXLr491cy9s_DGgnFVREbdFq9Q";
        $url = "https://api.telegram.org/bot$token/sendMessage?text=$text1&chat_id=$chat_id";

        file_get_contents($url);
    }
    
    
    public function whatsbot() {
        $uri = $_SERVER["REQUEST_URI"];
        $uriArray = explode('?', $uri);
        $page_url = $uriArray[1];

        $uriArray1 = explode('&', $page_url);
        $u1 = $uriArray1[0];
        $u2 = $uriArray1[1];
        $u3 = $uriArray1[2];

        $uri1 = explode('=', $u1);
        $uri2 = explode('=', $u2);
        $uri3 = explode('=', $u3);
        echo $uri1[1];
        echo $uri2[1];
        echo $uri3[1];
        $mobile_no = $uri1[1];
        $message = $uri2[1];
        
        $text_count1 = explode('+', $message);
        $text_count = substr_count($message, " ") + 1;

        $mob = substr($mobile_no, 2);
        $mobile_no1 = $this->encryption_model->encode($mob);
        $query_db = $this->db->query("select * from customers where cus_mobile= '$mobile_no1'")->result();
        foreach ($query_db as $qd) {
                        $cus_name = strtoupper($qd->cus_name);
                    }
        
        if ($query_db) {
            if ($message == "1") {
                $text1 = "Please+enter+your+operator+name,+mobile+no.+and+amount%0D%0A%0A+e.g.+RC+airtel+9988776655+199%0A+e.g.+RC+jio+9988776655+199%0A+e.g.+RC+VI+9988776655+199%0A+e.g.+RC+BSNL+9988776655+199";
            } elseif ($text_count1[0] == 'RC') {
                $recharge_details = explode('+', $message);
                
                if (preg_match("/^[a-zA-Z]/i", $recharge_details[1]) && preg_match("/^[0-9]{10}$/", $recharge_details[2]) && preg_match("/^[0-9]/i", $recharge_details[3])) {
                    $query = $this->db->query("select opcodenew from operator where operatorname = '$recharge_details[1]' and opsertype='mobile'")->result();
                    foreach ($query as $q) {
                        $opcodenew = $q->opcodenew;
                    }

                    $query1 = $this->db->query("select cus_id,cus_type from customers where cus_mobile= '$mobile_no1'")->result();
                    foreach ($query1 as $q1) {
                        $cus_id = $q1->cus_id;
                        $cus_type = $q1->cus_type;
                    }

                    $mode = 'whatsapp';

                    $complete = $this->rec_model->recharge_ot(array('cus_id' => $cus_id, 'amount' => $recharge_details[3], 'number' => $recharge_details[2], 'billing' => $billing, 'process' => $process, 'operator' => $opcodenew, 'mode' => $mode, 'city' => $city, 'type' => $cus_type, 'promo' => $promo));
                    if ($complete['type'] == '1') {
                        $msg = $complete['mess'];
                        $rcid = $complete['rcid'];
                        if($msg=="Low Balance."){
                            $text1="Your+recharge+is+failed+due+to+insufficient+balance.";
                        }elseif($msg=="Your Recharge is Success"){
                            $data = $this->db->query("select * from exr_rechrgreqexr_rechrgreq_fch  where recid= '$rcid'")->result();
                            $tran_id = $data[0]->wellborn_trans_no;
                            $mobileno = $data[0]->mobileno;
                            $amt = $data[0]->amount;
                            $text1 = "Your+recharge+is+succefull%0AMobile+no-+$mobileno%0AAmount-+$amt%0ATransaction+ID-$tran_id";
                        }else{
                            $text1="Your+recharge+is+failed+please+try+after+some+time";
                        }
                    } else {
                        $text1 = "recharge+not+succefull";
                    }
                } else {
                    $text1 = "please+enter+correct+details%0D%0A+e.g.+RC+airtel+9988776655+199";
                }
            } elseif ($message == 2) {
                $text1 = "Please+enter+your+DTH+name,+DTH+no+and+amount+%0A%0A+e.g.+DTH+Tatasky+65646576+200";
            }   elseif ($text_count1[0] == 'DTH') {
                $recharge_details = explode('+', $message);

                if (preg_match("/^[a-zA-Z]/i", $recharge_details[1]) && preg_match("/^[0-9]/i", $recharge_details[2]) && preg_match("/^[0-9]/i", $recharge_details[3])) {
                    $query = $this->db->query("select opcodenew from operator where operatorname = '$recharge_details[1]' and opsertype='dth'")->result();
                    foreach ($query as $q) {
                        $opcodenew = $q->opcodenew;
                    }

                    $query1 = $this->db->query("select cus_id,cus_type from customers where cus_mobile= '$mobile_no1'")->result();
                    foreach ($query1 as $q1) {
                        $cus_id = $q1->cus_id;
                        $cus_type = $q1->cus_type;
                    }

                    $mode = 'whatsapp';

                    $complete = $this->rec_model->recharge_ot(array('cus_id' => $cus_id, 'amount' => $recharge_details[3], 'number' => $recharge_details[2], 'billing' => $billing, 'process' => $process, 'operator' => $opcodenew, 'mode' => $mode, 'city' => $city, 'type' => $cus_type, 'promo' => $promo));
                    if ($complete['type'] == '1') {
                        $msg = $complete['mess'];
                        $rcid = $complete['rcid'];
                        if($msg=="Low Balance."){
                            $text1="Your+recharge+is+failed+due+to+insufficient+balance.";
                        }elseif($msg=="Your Recharge is Success"){
                            $data = $this->db->query("select * from exr_rechrgreqexr_rechrgreq_fch  where recid= '$rcid'")->result();
                            $tran_id = $data[0]->wellborn_trans_no;
                            $mobileno = $data[0]->mobileno;
                            $amt = $data[0]->amount;
                            $text1 = "Your+DTH+recharge+is+succefull%0ADTH+no-+$mobileno%0AAmount-+$amt%0ATransaction+ID-+$tran_id";
                        }else{
                            $text1="Your+recharge+is+failed+please+try+after+some+time";
                        }
                    } else {
                        echo "DTH+recharge+not+succefull";
                    }
                } else {
                    $text1 = "please+enter+correct+details+dth%0A%0A+e.g.+DTH+Tatasky+65646576+200";
                }
        }    elseif ($message == 3) {
                $text1 = "Please+enter+your+operator+code,+consumer+no+and+amount+%0A%0A+e.g.+ELC+E101+65646576+200%0A%0ACodes+for+operator%0D%0AE101=+Mahavitaran-Maharashtra+State+Electricity+Distribu%0D%0AE102=+India+Power+Corporation+Ltd.+(Asansol)%0D%0AE103=+SNDL+NAGPUR%0D%0AE104=+Adani+Electricity%0D%0AE105=+Tata+Power+-Mumbai%0D%0AE106=+Brihan+Mumbai+Electric+Supply+and+Transport+Undertaking";
            }   elseif ($text_count1[0] == 'ELC') {
                
                $recharge_details = explode('+', $message);

                if (preg_match("/^[A-Z0-9]/i", $recharge_details[1]) && is_numeric($recharge_details[2]) && is_numeric($recharge_details[3])) {
                    if($recharge_details[1] == "E101"){
                        $operatorname = "Mahavitaran-Maharashtra State Electricity Distribution ";
                    }   elseif($recharge_details[1] == "E102"){
                        $operatorname = "India Power Corporation Ltd. (Asansol)";
                    }   elseif($recharge_details[1] == "E103"){
                        $operatorname = "SNDL NAGPUR";
                    }   elseif($recharge_details[1] == "E104"){
                        $operatorname = "Adani Electricity";
                    }   elseif($recharge_details[1] == "E105"){
                        $operatorname = "Tata Power -Mumbai";
                    }   elseif($recharge_details[1] == "E106"){
                        $operatorname = "Brihan Mumbai Electric Supply and Transport Undertaking ";
                    }   else{
                        $operatorname = "invalid";
                    }
                    
                    $query = $this->db->query("select opcodenew from operator where operatorname = '$operatorname' and opsertype='ELECTRICITY'")->result();
                    foreach ($query as $q) {
                        $opcodenew = $q->opcodenew;
                    }

                    $query1 = $this->db->query("select cus_id,cus_type from customers where cus_mobile= '$mobile_no1'")->result();
                    foreach ($query1 as $q1) {
                        $cus_id = $q1->cus_id;
                        $cus_type = $q1->cus_type;
                    }

                    $mode = 'whatsapp';

                    $complete = $this->rec_model->recharge_ot(array('cus_id' => $cus_id, 'amount' => $recharge_details[3], 'number' => $recharge_details[2], 'billing' => $billing, 'process' => $process, 'operator' => $opcodenew, 'mode' => $mode, 'city' => $city, 'type' => $cus_type, 'promo' => $promo));
                    if ($complete['type'] == '1') {
                        $msg = $complete['mess'];
                        if($msg=="Low Balance."){
                            $text1="Your+electricity+Bill+is+failed+due+to+insufficient+balance.";
                        }elseif($msg=="Your Recharge is Success"){
                            $data = $this->db->query("select * from exr_rechrgreqexr_rechrgreq_fch  where recid= '$rcid'")->result();
                            $tran_id = $data[0]->wellborn_trans_no;
                            $mobileno = $data[0]->mobileno;
                            $amt = $data[0]->amount;
                            $text1 = "Your+electricity+Bill+is+succefull%0AConsumer+no-+$mobileno%0AAmount-+$amt%0ATransaction+ID-$tran_id";
                        }else{
                            $text1="Your+electricity+Bill+is+failed+please+try+after+some+time";
                        }
                    } else {
                        $text1 = "Electricity+bill+not+succefull";
                    }
                } else {
                    $text1 = "please+enter+correct+details+of+dth";
                }
            }   elseif ($message == 4) {
                $text1 = "Please+enter+your+Mobile+no,+Operator+and+amount+%0A%0Ae.g.+POST+P1+9988776655+199%0A%0AOperator+Codes%0A+P1+=+BSNL+Postpaid%0A+P2=+Airtel+Postpaid%0A+P3+=+Idea+Postpaid%0A+P4+=+Vodafone+Postpaid%0AP+5+=+JIO+Postpaid";
            }   elseif ($text_count1[0] == 'POST') {

                $recharge_details = explode('+', $text);

                if (preg_match("/^[a-zA-Z]/i", $recharge_details[1]) && preg_match("/^[0-9]{10}$/", $recharge_details[2]) && preg_match("/^[0-9]/i", $recharge_details[3])) {
                    if($recharge_details[1] == "P1"){
                        $operatorname = "BSNL Postpaid";
                    }   elseif($recharge_details[1] == "P2"){
                        $operatorname = "Airtel Postpaid";
                    }   elseif($recharge_details[1] == "P3"){
                        $operatorname = "Idea Postpaid";
                    }   elseif($recharge_details[1] == "P4"){
                        $operatorname = "Vodafone Postpaid";
                    }   elseif($recharge_details[1] == "P5"){
                        $operatorname = "JIO Postpaid";
                    }   else{
                        $operatorname = "invalid";
                    }
                    
                    $query = $this->db->query("select opcodenew from operator where operatorname = '$operatorname' and opsertype='postpaid'")->result();
                    foreach ($query as $q) {
                        $opcodenew = $q->opcodenew;
                    }

                    $query1 = $this->db->query("select cus_id,cus_type from customers where cus_mobile = '$mobile_no1'")->result();
                    foreach ($query1 as $q1) {
                        $cus_id = $q1->cus_id;
                        $cus_type = $q1->cus_type;
                    }

                    $mode = 'Telegram';

                    $complete = $this->Rec_model->recharge_ot(array('cus_id' => $cus_id, 'amount' => $recharge_details[3], 'number' => $recharge_details[2], 'billing' => $billing, 'process' => $process, 'operator' => $opcodenew, 'mode' => $mode, 'city' => $city, 'type' => $cus_type, 'promo' => $promo));
                    if ($complete['type'] == '1') {
                        $msg = $complete['mess'];
                        $rcid = $complete['rcid'];
                        
                        if($msg=="Your Recharge is Success"){
                            $data = $this->db->query("select * from exr_rechrgreqexr_rechrgreq_fch  where recid= '$rcid'")->result();
                            $tran_id = $data[0]->wellborn_trans_no;
                            $mobileno = $data[0]->mobileno;
                            $amt = $data[0]->amount;
                            $text1 = 'Your+postpaid+bill+is+success%0ATransaction+id='.$tran_id.'%0AAmount='.$amt.'%0AMobile No='.$mobileno;
                        }elseif($msg=="Low Balance."){
                            $text1="Your+recharge+is+failed+due+to+insufficient+balance.";
                        }else{
                            $text1="Your+recharge+is+failed+please+try+after+some+time";
                        }
                        
                    } else {
                        echo "recharge+not+succefull";
                    }
                } else {
                    $text1 = "please+enter+correct+details";
                }
             
        }   elseif ($message == 5) {
                $query = $this->db->query("select cus_id from customers where cus_mobile= '$mobile_no1'")->result();
                foreach ($query as $q) {
                    $cus_id = $q->cus_id;
                }

                $query1 = $this->db->query("select txn_clbal from exr_trnx where txn_agentid = '$cus_id' order by txn_id desc limit 1")->result();
                if ($query1) {
                    foreach ($query1 as $q1) {
                        $txn_clbal = round($q1->txn_clbal, 2);
                    }
                } else {
                    $txn_clbal = 0;
                }

                $text1 = "Your+current+account+balance+is+". $txn_clbal;
            }   elseif ($message == 6) {
                    $text1 = "Scan+and+pay+on+below+QR+code+to+add+money+in+your+wallet";
            }   elseif ($message == 7) {
                $query = $this->db->query("select cus_id from customers where cus_mobile= '$mobile_no1'")->result();
                foreach ($query as $q) {
                    $cus_id = $q->cus_id;
                }
                $query1 = $this->db->query("select * from exr_trnx where txn_agentid = '$cus_id' order by txn_id limit 50")->result();
                $text1 = "TRANSACTION+DETAILS%0D%0AID+||+TXN_TYPE+||+TXN_DATE+||+OP_BAL+||+CRDT+||+DBT+||+CL_BAL%0D%0A";
                foreach ($query1 as $q1) {
                    $txn_id = $q1->txn_id;
                    $txn_type = $q1->txn_type;
                    $txn_date = $q1->txn_date;
                    $txn_clbal = round($q1->txn_clbal, 2);
                    $txn_crdt = round($q1->txn_crdt, 2);
                    $txn_dbdt = round($q1->txn_dbdt, 2);
                    $txn_opbal = round($q1->txn_opbal, 2);
                    $text1 .= "$txn_id+||+$txn_type+||+$txn_date+||+$txn_opbal+||+$txn_crdt+||+$txn_dbdt+||+$txn_clbal%0D%0A";
                }
            }   elseif ($text == 8) {
                $text1 = 'Welcome+to+Easypayall+%0D%0AHow+can+I+help+you+today,+$cus_name+%0A%0A1+Mobile+Recharge%0D%0A2+DTH+Recharge%0D%0A3+Electricity+bill+payment%0D%0A4+Postpaid+Bill+Payment%0D%0A5+Check+Account+Balance%0D%0A6+Add+Money%0D%0A7+Account+History%0A8+Main+Menu%0A%0APlease+enter+the+option+number+to+proceed+(Ex.3)';
            }   else {
                $text1 = "Welcome+to+Easypayall+%0D%0AHow+can+I+help+you+today,+$cus_name+%0A%0A1+Mobile+Recharge%0D%0A2+DTH+Recharge%0D%0A3+Electricity+bill+payment%0D%0A4+Postpaid+Bill+Payment%0D%0A5+Check+Account+Balance%0D%0A6+Add+Money%0D%0A7+Account+History%0A8+Main+Menu%0A%0APlease+enter+the+option+number+to+proceed+(Ex.3)";
            }
        } else {
            $text1 = "Welcome+to+Easypayall%0A%0AYou+are+not+registered+user+of+Easypayall%0A%0ARegister+on+below+link+to+use+services%0A%0Ahttps://easypayall.in";
        }
        $url = "http://whatsbot.tech/api/send_sms?api_token=f4b3ab9f-599a-4b50-95da-c711159e5941&mobile=$mobile_no&message=$text1";
        $url = "https://whatschats.in/wapp/api/send?apikey=f097567ecdda476bab8225601ca90729&mobile=$mobile_no&msg=$text1";

        $response = file_get_contents($url);
        // print_r($response);
    }
    
    
    public function check_operator()
	{
	    $mobile = $this->input->post('mobile');
		$request ="";
		$param['apikey'] = '57814711b8193ea873059df35549ec93';
		$param['tel'] = $mobile;
		foreach($param as $key=>$val) 
		{ 
			$request.= $key."=".urlencode($val); 
			$request.= "&"; 
		}
        $url = "http://operatorcheck.mplan.in/api/operatorinfo.php?".$request;  
        //echo $url;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch,CURLOPT_TIMEOUT,9000);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        $content = curl_exec($ch);
        curl_close($ch); 
        // $data2=json_decode($content);
        $data2=json_decode($content, true);
	    $a = $data2['records'];
	    $opname = $a['Operator'];
	    if($opname=='Vodafone'){
	        $opname = "VI";
	    }
        $d = $this->Db_model->getAlldata("select * from operator where operatorname='$opname'");
        $dd = array_merge($data2,$d);
        // print_r($dd);exit;
        return $dd;
        
	}
    

}	   
