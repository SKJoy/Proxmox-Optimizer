<?php
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\SMTP;

print PHP_EOL;

if($NotificationEmail){ // Notification email process
	$NotificationEmailSendTime = time();
	
	if($NotificationEmailSendTime - ($NotificationEmailSentTime ?? 0) > $NotificationEmailDelay){ //? Defer as configured
		$NotificationEmailSentTime = $NotificationEmailSendTime; //? Keep track of last email send out time
		
		if($OpenAIAPIBaseURL && $OpenAIAPIModel){ // Get email content from AI
			$OpenAIAPIModelPart = explode(":", $OpenAIAPIModel);
			
			$AIModel = new \S\External\OpenAI\Model(
				$API = new \S\External\OpenAI\API(
					$OpenAIAPIBaseURL, 
					$OpenAIAPIKey, 
				), 
				$OpenAIAPIModelPart[0], 
				$OpenAIAPIModelPart[1] ?? null, 
				file_get_contents(__DIR__ . "/ai/prompt/system.md"), 
			);
			
			$AIAnalysis = $AIModel->Chat(
				"" . file_get_contents(__DIR__ . "/ai/prompt/user.md") . "
			
			```
			" . file_get_contents($JSONFile) . "
			```", 
			);
			
			#region Sanitize AI content
			$AIAnalysisReport = $AIAnalysis->Data->choices[0]->message->content ?? "-- NOT AVAILABLE --";
			if(substr($AIAnalysisReport, 0, 7) == "```html")$AIAnalysisReport = substr($AIAnalysisReport, 7);
			$AIAnalysisReportLength = strlen($AIAnalysisReport);
			if(substr($AIAnalysisReport, $AIAnalysisReportLength - 3, 3) == "```")$AIAnalysisReport = substr($AIAnalysisReport, 0, $AIAnalysisReportLength - 3);
			#endregion Sanitize AI content
		}
		else{
			$AIAnalysisReport = null;
		}

		#region Email configuration
		$Mail = new PHPMailer(true);
		$Mail->CharSet = "UTF-8";
		$Mail->isSMTP();											// Send using SMTP
		$Mail->Host	   = $SMTPHost;					 // Set the SMTP server
		$Mail->SMTPAuth   = true;								   // Enable SMTP authentication
		$Mail->Username   = $SMTPUser;					 // SMTP username
		$Mail->Password   = $SMTPPassword;							   // SMTP password
		$Mail->SMTPSecure = strtolower($SMTPSecurity);			// Enable implicit TLS
		$Mail->Port	   = $SMTPPort;									// TCP port
		$Mail->setFrom($SMTPFromEmail, $SMTPFromName);
		
		$Mail->SMTPOptions = [ // Disable SSL verification
			"ssl" => [
				"verify_peer" => false,
				"verify_peer_name" => false,
				"allow_self_signed" => true
			]
		];
			
		// Recipients
		foreach(explode(",", trim(str_replace(" ", "", $NotificationEmail))) as $ThisNotificationEmail)$Mail->addAddress($ThisNotificationEmail);		   // Add recipient
	
		// Content
		$Mail->isHTML(true);										// Set email format to HTML
		$Mail->Subject = "Proxmox Optimizer recommendation";
	
		$Mail->Body = <<<CONTENT
		<div style=\"margin: 0; background-color: Black; padding: 2em; color: White; font-family: Consolas, Verdana, Tahoma, Arial; font-size: 14px;\">
			We have finished scanning and optimizing your Proxmox VE instance for the LXC/VM you have in it. Please find below the detailed report of the process and actions taken.
	
			<pre style=\"padding: 1em;\">
				{$LoadTrendMatrixContent}		
				{$RecommendationContent}
				{$OptimizationMessage}		
			</pre>

			<h2>✨ AI analysis</h2>
			{$AIAnalysisReport}
			<div style=\"margin: 2em; border: 1px Red solid; padding: 1em; color: Grey; font-size: 75%;\"><strong>WARNING</strong>: Do not take AI analysis for granted to make a final call. AI analysis is completely automated and prone to possible error. Be adviced to check against expert human opinion.</div>
	
			<div style=\"margin-top: 1em;\">
				Regards,
				<h1>Proxmox VE Optimizer</h1>
			</div>
		</div>
		CONTENT;
		#endregion Email configuration
		
		try{ //? Send email
			$Mail->send();
			print "✅ 📧 Email Sent: {$NotificationEmail}";
		}
		catch(Exception $Exception){ //! Error sending email
			print  "❌ ERROR / 📧 Mailer: {$Mail->ErrorInfo}";
		}
	}
	else{
		print "✊ 📧 Notification email deferred by configuration";
	}
}
else{ //! No notification email address configured
	print "⭕ 📧 No notification email configured";
}

print PHP_EOL;
?>