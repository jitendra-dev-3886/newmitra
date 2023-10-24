
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.debug.js" integrity="sha384-NaWTHo/8YCBYJ59830LTz/P4aQZK1sS0SneOgAvhsIl3zBu8r9RevNg5lHCHAuQ/" crossorigin="anonymous"></script>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.min.js"></script>
<script type="text/javascript" src="https://html2canvas.hertzen.com/dist/html2canvas.js"></script>

<?php 

    require_once('tcpdf/tcpdf.php'); 

    // create new PDF document
    $pdf = new TCPDF("P", "mm", array(150,100) , true, 'UTF-8', false);

    // set document information
    $pdf->SetCreator(PDF_CREATOR);

    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    // set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    //set margins
    $pdf->SetMargins('1', '1', '0');
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    //set auto page breaks
    $pdf->SetAutoPageBreak(TRUE,0);

    //set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    //set some language-dependent strings
    // $pdf->setLanguageArray($l);

    // ---------------------------------------------------------

    // set font
    $pdf->SetFont('helvetica', '', 10);

    // add a page
    $pdf->AddPage();
    // $pdf->AddPage('L', '', false, false);
    
    $html = '
    <!-- EXAMPLE OF CSS STYLE -->
    <style>
        h1 {
            color: navy;
            font-family: times;
            font-size: 13pt;
            color:black
        }
        table.first {
        
            color: #003300;
            font-family: helvetica;
            font-size: 8pt;
            background-color: white;
            width: 100%;
            text-align:center;
        }
        td {
            border: none;
        }
    </style>';
    $name='statement';
    $path=base_url().'assets/logo.png';
    if(is_array($data)){foreach($data as $rec){ 
    // $allbarcode[$i]
    $type='';
    if($rec->transactionType=='CW'){$type='Cash Withdrawal';}else{$type='Aadhar Pay';}
$html .='<br /><br /><br /><br /><br /><br />
<table class="first" cellpadding="4" cellspacing="6">
 <tr><td width="100"></td><td width="100" align="center"><img src="'.$path.'"/></td><td width="100"></td></tr><br /><br />
  <tr><td width="100"></td><td width="150" align="left"><h4>'.$type.' Receipt</h4></td><td width="50" align="left"></td></tr>
 <tr><td width="50"></td><td width="100" align="left">Aadhar Number:</td><td width="150" align="left">'.$rec->aadhar_number.'</td></tr>
 <tr><td width="50"></td><td width="100" align="left">Amount:</td><td width="150" align="left">Rs. '.$rec->amount.'/-</td></tr>
 <tr><td width="50"></td><td width="100" align="left">Bank Name:</td><td width="150" align="left">'.$rec->bankName.'</td></tr>
 <tr><td width="50"></td><td width="100" align="left">Date:</td><td width="150" align="left">'.$rec->aeps_date_time.'</td></tr>
 <tr><td width="50"></td><td width="100" align="left">Bank UTR No.:</td><td width="150" align="left">'.$rec->utr.'</td></tr>
 <tr><td width="50"></td><td width="100" align="left">Outlet Name:</td><td width="150" align="left">'.$rec->cus_name.'</td></tr>
 <tr><td width="50"></td><td width="100" align="left">Outlet Mobile:</td><td width="150" align="left">'.$this->encryption_model->decode($rec->cus_mobile).'</td></tr>
 <br /><br /><br /><br />
 <tr><td width="300" align="center">Powered By: ICICI Bank</td></tr>
</table>
';
}}

$pdf->writeHTML($html, true, false, true, false, '');
ob_end_clean(); 
$pdf->Output($name.'.pdf', 'I');
?>                    
