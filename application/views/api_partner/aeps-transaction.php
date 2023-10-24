<?php

/*$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://www.instantpay.in/ws/aepsweb/aeps/transaction",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS =>"{\r\n  \"token\" : \"cb9a100a3975475048f91f314ff48fbd\",\r\n  \"request\": {\r\n    \"outlet_id\": \"10\",\r\n    \"amount\": \"amount\",\r\n    \"aadhaar_uid\": \"92637******2\",\r\n    \"bankiin\": \"607091\",\r\n    \"latitude\": \"28.5257\",\r\n    \"longitude\": \"77.2899\",\r\n    \"mobile\": \"9999999999\",\r\n    \"agent_id\": \"1534933119\",\r\n    \"sp_key\": \"BAP\",\r\n    \"pidDataType\": \"X\",\r\n    \"pidData\": \"MjAxOCtIIu********AazQZQiWN+Pll7K4wo0=\",\r\n    \"ci\": \"20***30\",\r\n    \"dc\": \"340b9cf0-*******c9e4f90712af\",\r\n    \"dpId\": \"MAN***IPL\",\r\n    \"errCode\": \"0\",\r\n    \"errInfo\": \"Success\",\r\n    \"fCount\": \"1\",\r\n    \"tType\": null,\r\n    \"hmac\": \"teMH4q2jfX2T*****wcutb\",\r\n    \"iCount\": \"0\",\r\n    \"mc\": \"MIIEFjCC******D6W8Zi1gkb7uv\",\r\n    \"mi\": \"MFS100\",\r\n    \"nmPoints\": \"49\",\r\n    \"pCount\": \"0\",\r\n    \"pType\": \"\",\r\n    \"qScore\": \"65\",\r\n    \"rdsId\": \"MANTRA.WIN.001\",\r\n    \"rdsVer\": \"1.0.0\",\r\n    \"sessionKey\": \"fqymu4qG7ivX****z3kRUz8exussQ==\",\r\n    \"srno\": \"12**23\"\r\n    \r\n  },\r\n  \"user_agent\": \"Mozilla/5.0 (W******Safari/537.36\"\r\n}",
  CURLOPT_HTTPHEADER => array(
    "Content-Type: application/json"
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;*/

?>

function discoverAvdm()
{

	var SuccessFlag=0;
    var primaryUrl = "http://127.0.0.1:";

	try {
		 var protocol = window.location.href;
		 if (protocol.indexOf("https") >= 0) {
			primaryUrl = "http://127.0.0.1:";
		}
	 } catch (e){ }

    url = "";

	//alert("Please wait while discovering port from 11100 to 11120.\nThis will take some time.");
	
    for (var i = 11100; i <= 11120; i++)
    {
		if(primaryUrl=="https://127.0.0.1:" && OldPort==true)
		{
		   i="8005";
		}

		var verb = "RDSERVICE";
        var err = "";
		SuccessFlag=0;
		var res;
		$.support.cors = true;
		var httpStaus = false;
		var jsonstr="";
		var data = new Object();
		var obj = new Object();

		$.ajax({

		type: "RDSERVICE",
		async: false,
		crossDomain: true,
		url: primaryUrl + i.toString(),
		contentType: "text/xml; charset=utf-8",
		processData: false,
		cache: false,
		crossDomain:true,

		success: function (data) {

			httpStaus = true;
			res = { httpStaus: httpStaus, data: data };
		    //alert(data);
			finalUrl = primaryUrl + i.toString();
			var $doc = $.parseXML(data);
			var CmbData1 =  $($doc).find('RDService').attr('status');
			var CmbData2 =  $($doc).find('RDService').attr('info');
			if(RegExp('\\b'+ 'Mantra' +'\\b').test(CmbData2)==true)
			{
			    closeNav();
				$("#txtDeviceInfo").val(data);

				if($($doc).find('Interface').eq(0).attr('path')=="/rd/capture")
				{
				  MethodCapture=$($doc).find('Interface').eq(0).attr('path');
				}
				if($($doc).find('Interface').eq(1).attr('path')=="/rd/capture")
				{
				  MethodCapture=$($doc).find('Interface').eq(1).attr('path');
				}
				if($($doc).find('Interface').eq(0).attr('path')=="/rd/info")
				{
				  MethodInfo=$($doc).find('Interface').eq(0).attr('path');
				}
				if($($doc).find('Interface').eq(1).attr('path')=="/rd/info")
				{
				  MethodInfo=$($doc).find('Interface').eq(1).attr('path');
				}

				$("#ddlAVDM").append('<option value='+i.toString()+'>(' + CmbData1 +')'+CmbData2+'</option>')
				SuccessFlag=1;
				alert("RDSERVICE Discover Successfully");
				return;

			}

			//alert(CmbData1);
			//alert(CmbData2);

		},
		error: function (jqXHR, ajaxOptions, thrownError) {
		if(i=="8005" && OldPort==true)
		{
			OldPort=false;
			i="11099";
		}
		$('#txtDeviceInfo').val("");
		//alert(thrownError);

			//res = { httpStaus: httpStaus, err: getHttpError(jqXHR) };
		},

	});

    	if(SuccessFlag==1)
    	{
    	  break;
    	}
    
    	//$("#ddlAVDM").val("0");
    
    }

	if(SuccessFlag==0)
	{
	 alert("Connection failed Please try again.");
	}


	return res;
}