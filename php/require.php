<?php
error_reporting(0);
$mail = new PHPMailer;

$_email = "";
$_password = "";

function GetTimeCarrisSend($StopId){
global $mail;

$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
$mail->SMTPAuth = true;

$mail->Username = $_email;                 // SMTP username
$mail->Password = $_password;                           // SMTP password
$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
$mail->Port = 587;        

$mail->SMTPOptions = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);                            // TCP port to connect to

$mail->setFrom($_email, 'Above WebDev');
$mail->addAddress('sms@carris.pt');
$mail->Subject = 'C ' . $StopId;
$mail->isHTML(true);
$mail->Body = date('Y-m-d');

if(!$mail->send()) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    //echo 'Message has been sent';
}

}

function GetTimeCarrisReceive($StopId, $requestTime){

    $DOM = new DomDocument;

    /* connect to gmail */
$hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
$username = $_email;
$password = $_password;

/* try to connect */
$inbox = imap_open($hostname,$username,$password) or die('Cannot connect to Gmail: ' . imap_last_error());

/* grab emails */
$emails = imap_search($inbox,'TEXT "'.$requestTime.'"');

/* if emails are returned, cycle through each... */
if($emails) {
	/* begin output var */
	$output = '';
	/* put the newest emails on top */
	rsort($emails);
    $cont = 0;
    foreach($emails as $email_number){
        /* get information specific to this email */
        if($cont == 0){
            $cont++;
            $bodyText = imap_fetchbody($inbox,$email_number,1.2);
            if(!strlen($bodyText)>0){
            $bodyText = imap_fetchbody($inbox,$email_number,1);
            }

                $DOM->loadHTML($bodyText);
                $tags = $DOM->getElementsByTagName('th');

                //#Get header name of the table
                foreach($tags as $NodeHeader) 
                {
                $aDataTableHeaderHTML[] = trim($NodeHeader->textContent);
                }

                unset($aDataTableHeaderHTML[count($aDataTableHeaderHTML)-1]);
                unset($aDataTableHeaderHTML[0]);
                unset($aDataTableHeaderHTML[1]); 
                unset($aDataTableHeaderHTML[2]); 
                unset($aDataTableHeaderHTML[3]);
                 // remove item at index 0
                $arrayCarris = array_values($aDataTableHeaderHTML); // 'reindex' array

                $arrayApi= array();

                $numberOfResults = count($arrayCarris) / 4;



                for($i = 0; $i < $numberOfResults; $i++){

                    array_push($arrayApi, array(
                        "Bus Number" => clean($arrayCarris[0]),
                        "Stop Name" => clean($arrayCarris[1]),
                        "ETA" => $arrayCarris[2],
                        "Time To Arrival" => clean($arrayCarris[3])
                    ));

                    unset($arrayCarris[0]);
                    unset($arrayCarris[1]); 
                    unset($arrayCarris[2]); 
                    unset($arrayCarris[3]);
                    $arrayCarris = array_values($arrayCarris);
                }

                if(count($arrayApi) > 3){
                    return json_encode($arrayApi, JSON_PRETTY_PRINT);
                }else{
                    return false;
                }
        }


    }
		

        
		
	
} 

/* close the connection */
imap_close($inbox);
}




function clean($string) {
   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
   $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.

   return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
}


 



?>