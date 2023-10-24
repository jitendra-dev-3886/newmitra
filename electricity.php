<?php 
    
    $url_key = $_POST['key'];
    $key = "]^[kM3FaXD%;gL";
    if($url_key == $key)
    {
        if($_POST['mobile']!="" && $_POST['operator']!=""){
            $request ="";
        	$param['apikey'] = '57814711b8193ea873059df35549ec93';
        	$param['offer'] = 'roffer';
        	$param['tel'] = $_POST['mobile'];
        	$param['operator'] = $_POST['operator'];
        	foreach($param as $key=>$val) 
        	{ 
        		$request.= $key."=".urlencode($val); 
        		$request.= "&"; 
        	}
            $url = "https://www.mplan.in/api/electricinfo.php?".$request;                           
        	$ch = curl_init();
        	curl_setopt($ch, CURLOPT_URL, $url);
        	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        	//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        	curl_setopt($ch,CURLOPT_TIMEOUT,9000);
        	curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
            $content = curl_exec($ch);
            curl_close($ch); 
            echo $content;
            /*$data2=json_decode($content);
            print_r( $data2);*/
            
            //echo $data2;
        }else{
            
            echo json_encode(array('status'=>FALSE,'message'=>'Missing Parameter'));
            
        }
        
    }else{
        
        echo json_encode(array('status'=>FALSE,'message'=>'YOU CANNOT ACCESS THIS PAGE'));
        
    }
    
?>