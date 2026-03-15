<?php
namespace S\External\EvolutionAPI;

require_once __DIR__ . "/../EvolutionAPI.php";

class Instance extends \S\Base{
	#region Public constant
	public const ERROR_MISSING_NUMBER = ["Code" => 99999, "Message" => "Number missing", ];
	public const ERROR_MISSING_MESSAGE = ["Code" => 99998, "Message" => "Message missing", ];
	public const ERROR_MISSING_NAME = ["Code" => 99997, "Message" => "Name missing", ];
	public const ERROR_MISSING_API_KEY = ["Code" => 99996, "Message" => "API key missing", ];
	public const ERROR_MISSING_EVOLUTION_API_API_KEY = ["Code" => 99995, "Message" => "Evolution API API key missing", ];

	public const INTEGRATION_TYPE_BAILEYS = "WHATSAPP-BAILEYS";
	public const INTEGRATION_TYPE_WHATSAPP_BUSINESS = "WHATSAPP-BUSINESS";
	#endregion Public constant

	#region Internal
	// private ?object $cURL = null;
	#endregion Internal

	public function __construct(
		\S\External\EvolutionAPI $EvolutionAPI, 
		string $Name, 
		?string $APIKey = null, 
		?string $Number = null, 
		?string $IntegrationType = null, 
		?bool $RejectCall = null, 
		?string $CallRejectionMessage = null, 
		?bool $IgnoreGroupMessage = null, 
		?bool $AlwaysOnline = null, 
		?bool $SendReadReceipt = null, 
		?bool $GetMessageReadStatus = null, 
		?bool $SyncHistory = null, 
	){
		#region Validate argument
		// - Argument is converted into object property

		if(is_null($IntegrationType))$IntegrationType = self::INTEGRATION_TYPE_BAILEYS;
		if(is_null($RejectCall))$RejectCall = false;
		if(is_null($CallRejectionMessage))$CallRejectionMessage = "";
		if(is_null($IgnoreGroupMessage))$IgnoreGroupMessage = false;
		if(is_null($AlwaysOnline))$AlwaysOnline = false;
		if(is_null($SendReadReceipt))$SendReadReceipt = false;
		if(is_null($GetMessageReadStatus))$GetMessageReadStatus = false;
		if(is_null($SyncHistory))$SyncHistory = false;
		#endregion Validate argument

		#region Additional property
		$ID = null;
		$Status = null;
		$QR = (object)[
			"Code" => null, 
			"PairingCode" => null, 
			"PNGImageBase64" => null, 
			"Attempt" => 0, 
		];
		$WhatsAppVOIPToken = null;
		#endregion Additional property

		#region Initialize object
		// $this->_InstanceLimit = 1; // Limit instantiation

		// Set properties from constructor argument
		// - Any local variable defined above will be treated as object property
		parent::__construct(get_defined_vars());

		$this->_ReadOnlyProperty = "ID, Status, QR, WhatsAppVOIPToken"; // Property name list by array or CSV
		
		#region Dependent property configuration
		// $this->_DependentProperty[] = ["ImpactedBy" => "URL, Method, Data, Header, JSONResponse, UserAgent", "Dependent" => "Error, Request, Response, Time"];
		#endregion Dependent property configuration
		#endregion Initialize object

		#region Custom
		// Set $Result local variable with the value to return; default = TRUE

		#endregion Custom
		
		$Result = true;
		
		return $Result;
	}

	public function __destruct(){
		$Result = true;

		return $Result;
	}

	public function Create(
		?string $Name = null, 
		?string $APIKey = null, 
		?string $Number = null, 
		?string $IntegrationType = null, 
		?bool $RejectCall = null, 
		?string $CallRejectionMessage = null, 
		?bool $IgnoreGroupMessage = null, 
		?bool $AlwaysOnline = null, 
		?bool $SendReadReceipt = null, 
		?bool $GetMessageReadStatus = null, 
		?bool $SyncHistory = null, 
	):object{
		#region Validate argument
		if(is_null($Name))$Name = $this->_Property->Name;
		if(is_null($APIKey))$APIKey = $this->_Property->APIKey;
		if(is_null($Number))$Number = $this->_Property->Number;
		if(is_null($IntegrationType))$IntegrationType = $this->_Property->IntegrationType;
		if(is_null($RejectCall))$RejectCall = $this->_Property->RejectCall;
		if(is_null($CallRejectionMessage))$CallRejectionMessage = $this->_Property->CallRejectionMessage;
		if(is_null($IgnoreGroupMessage))$IgnoreGroupMessage = $this->_Property->IgnoreGroupMessage;
		if(is_null($AlwaysOnline))$AlwaysOnline = $this->_Property->AlwaysOnline;
		if(is_null($SendReadReceipt))$SendReadReceipt = $this->_Property->SendReadReceipt;
		if(is_null($GetMessageReadStatus))$GetMessageReadStatus = $this->_Property->GetMessageReadStatus;
		if(is_null($SyncHistory))$SyncHistory = $this->_Property->SyncHistory;

		#region Sanitize arguments
		$Name = trim($Name);
		$Number = trim($Number);
		#endregion Sanitize arguments
		#endregion Validate argument

		$Error = ["Code" => 0, "Message" => null, ];
		if(!$Name)$Error = ["Code" => self::ERROR_MISSING_NAME["Code"], "Message" => self::ERROR_MISSING_NAME["Message"], ];
		if(!$Number)$Error = ["Code" => self::ERROR_MISSING_NUMBER["Code"], "Message" => self::ERROR_MISSING_NUMBER["Message"], ];
		if(!$this->_Property->EvolutionAPI->APIKey)$Error = ["Code" => self::ERROR_MISSING_EVOLUTION_API_API_KEY["Code"], "Message" => self::ERROR_MISSING_EVOLUTION_API_API_KEY["Message"], ];

		$Result = $Error["Code"] ? (object)[
			"Error" => new \S\Exception($Error["Code"], $Error["Message"]), 
		] : $this->EvolutionAPI->API(
			"instance/create", 
			$this->_Property->EvolutionAPI->APIKey, 
			[
				"instanceName" => $Name, 
				"apikey" => $APIKey, 
				"number" => $Number, 
				"integration" => $IntegrationType, 
				"rejectCall" => $RejectCall, 
				"msgCall" => $CallRejectionMessage, 
				"groupsIgnore" => $IgnoreGroupMessage, 
				"alwaysOnline" => $AlwaysOnline, 
				"readMessages" => $SendReadReceipt, 
				"readStatus" => $GetMessageReadStatus, 
				"syncFullHistory" => $SyncHistory, 
				"qrcode" => true, 
			]
		);

		if(!$Result->Error->Code){
			$Data = $Result->Response->Data;
			$this->_Property->APIKey = $Data->hash;

			$InstanceData = $Data->instance;
			$this->_Property->ID = $InstanceData->instanceId;
			$this->_Property->Status = $InstanceData->status;
			
			$QRCodeData = $Data->qrcode;
			$this->_Property->QR->PairingCode = $QRCodeData->pairingCode;
			$this->_Property->QR->Code = $QRCodeData->code;
			$this->_Property->QR->PNGImageBase64 = $QRCodeData->base64;
			$this->_Property->QR->Attempt = $QRCodeData->count;

			$SettingData = $Data->settings;
			$this->_Property->RejectCall = $SettingData->rejectCall;
			$this->_Property->CallRejectionMessage = $SettingData->msgCall;
			$this->_Property->IgnoreGroupMessage = $SettingData->groupsIgnore;
			$this->_Property->AlwaysOnline = $SettingData->alwaysOnline;
			$this->_Property->SendReadReceipt = $SettingData->readMessages;
			$this->_Property->GetMessageReadStatus = $SettingData->readStatus;
			$this->_Property->SyncHistory = $SettingData->syncFullHistory;
			$this->_Property->WhatsAppVOIPToken = $SettingData->wavoipToken;
		}

		return $Result;
	}

	public function Delete(
		?string $Name = null, 
		?string $APIKey = null, 
	):object{
		#region Validate argument
		if(is_null($Name))$Name = $this->_Property->Name;

		if(is_null($APIKey))$APIKey = $this->_Property->APIKey;
		if(is_null($APIKey))$APIKey = $this->_Property->EvolutionAPI->APIKey;

		#region Sanitize arguments
		$Name = trim($Name);
		$APIKey = trim($APIKey);
		#endregion Sanitize arguments
		#endregion Validate argument

		$Error = ["Code" => 0, "Message" => null, ];
		if(!$Name)$Error = ["Code" => self::ERROR_MISSING_NAME["Code"], "Message" => self::ERROR_MISSING_NAME["Message"], ];
		if(!$APIKey)$Error = ["Code" => self::ERROR_MISSING_API_KEY["Code"], "Message" => self::ERROR_MISSING_API_KEY["Message"], ];

		$Result = $Error["Code"] ? (object)[
			"Error" => new \S\Exception($Error["Code"], $Error["Message"]), 
		] : $this->EvolutionAPI->API(
			"instance/delete/{$this->URLInstanceName($Name)}", 
			$APIKey, 
			null, 
			\S\Communication\cURL::METHOD_DELETE
		);

		return $Result;
	}

	public function QRCode(
		?string $Name = null, 
		?string $APIKey = null, 
	):object{
		#region Validate argument
		if(is_null($Name))$Name = $this->_Property->Name;

		if(is_null($APIKey))$APIKey = $this->_Property->APIKey;
		if(is_null($APIKey))$APIKey = $this->_Property->EvolutionAPI->APIKey;

		#region Sanitize arguments
		$Name = trim($Name);
		$APIKey = trim($APIKey);
		#endregion Sanitize arguments
		#endregion Validate argument

		$Error = ["Code" => 0, "Message" => null, ];
		if(!$Name)$Error = ["Code" => self::ERROR_MISSING_NAME["Code"], "Message" => self::ERROR_MISSING_NAME["Message"], ];
		if(!$APIKey)$Error = ["Code" => self::ERROR_MISSING_API_KEY["Code"], "Message" => self::ERROR_MISSING_API_KEY["Message"], ];

		$Result = $Error["Code"] ? (object)[
			"Error" => new \S\Exception($Error["Code"], $Error["Message"]), 
		] : $this->EvolutionAPI->API(
			"instance/connect/{$this->URLInstanceName($Name)}", 
			$APIKey
		);

		if(!$Result->Error->Code){
			$QRCodeData = $Result->Response->Data;
			$this->_Property->QR->PairingCode = $QRCodeData->pairingCode;
			$this->_Property->QR->Code = $QRCodeData->code;
			$this->_Property->QR->PNGImageBase64 = $QRCodeData->base64;
			$this->_Property->QR->Attempt = $QRCodeData->count;
		}

		return $Result;
	}

	public function SendMessage(
		string $Number, 
		string $Text, 
		?string $APIKey = null, 
	):object{
		#region Validate argument
		if(is_null($APIKey))$APIKey = $this->APIKey;
		
		#region Sanitize phone number
		$Number = trim($Number);
		if(substr($Number, 0, 1) == "+")$Number = substr($Number, 1);
		if(substr($Number, 0, 2) == "00")$Number = substr($Number, 2);
		#endregion Sanitize phone number

		$Text = trim($Text); // Sanitize message
		#endregion Validate argument

		$Error = ["Code" => 0, "Message" => null, ];
		if(!$Number)$Error = ["Code" => self::ERROR_MISSING_NUMBER["Code"], "Message" => self::ERROR_MISSING_NUMBER["Message"], ];
		if(!$Text)$Error = ["Code" => self::ERROR_MISSING_MESSAGE["Code"], "Message" => self::ERROR_MISSING_MESSAGE["Message"], ];

		$Result = $Error["Code"] ? (object)[
			"Error" => new \S\Exception($Error["Code"], $Error["Message"]), 
		] : $this->EvolutionAPI->API(
			"message/sendText/{$this->URLInstanceName($this->_Property->Name)}", 
			$APIKey, 
			(object)[
				"number" => $Number, 
				"text" => $Text, 
			]
		);

		return $Result;
	}

	private function URLInstanceName(
		?string $Name = null, 
	):string{
		if(is_null($Name))$Name = $this->_Property->Name;
		$Result = str_replace([" ", "/", ], ["%20", "%2F", ], $Name);

		return $Result;
	}
}
?>