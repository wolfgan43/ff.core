<?php

function process_mail($arrRequest) {
	//$res = array();
	$res = false;
	foreach ($arrRequest as $request) {
		$mail = new PHPMailer();
		$mail->CharSet = 'UTF-8';
		$mail->IsSMTP();
		$mail->Host 		= $request["email_struct"]["mail"]["smtp"]["host"];
		$mail->SMTPAuth     = $request["email_struct"]["mail"]["smtp"]["auth"];
		$mail->Username     = $request["email_struct"]["mail"]["smtp"]["username"];
		$mail->Password     = $request["email_struct"]["mail"]["smtp"]["password"];
		$mail->From         = $request["from"]["mail"];
		$mail->FromName     = $request["from"]["name"];
		$mail->Subject		= $request["subject"];
		$mail->Body			= $request["body"];
		$mail->isHTML(true);
		foreach ($request["to"] as $toAddress) {
			$mail->AddAddress($toAddress["mail"], $toAddress["name"]);
			//die("->".$toAddress["mail"]."-".$toAddress["name"]);
		}
		foreach ($request["bcc"] as $ccAddress) {
			$mail->AddCC($ccAddress["mail"], $ccAddress["name"]);
		}
		
		//$mail->AddCC("diella.daniele@gmail.com", "Daniele Diella");
		$mail->AddCC('notifiche@xoduslab.com', "Supporto");
		//$res[] = $mail->Send();
		//die(var_dump($arrRequest));
		$res = $mail->Send();
		//die("-".var_dump($res));
	}
	return $res;
}