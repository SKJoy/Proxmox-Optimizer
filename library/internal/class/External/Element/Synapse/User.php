<?php
namespace S\External\Matrix\Synapse;

class User extends \S\Base{
	#region Public constant
	// public const METHOD_HEAD = "HEAD";
	#endregion Public constant

	#region Internal
	// private ?object $cURL = null;
	#endregion Internal

	public function __construct(
		\S\External\Matrix\Synapse $Synapse, 
		?string $User = null, 
		?string $Password = null, 
		?string $AuthorizationToken = null, 
		?string $SyncNextBatchToken = null, 
		?int $WaitForMessageMS = 30000, 
		?bool $AcceptRoomInvite = true, 
		?string $RoomJoinMessage = null, 
	){
		#region Validate argument
		// - Argument is converted into object property

		if(is_null($WaitForMessageMS))$WaitForMessageMS = 30000;
		if(is_null($AcceptRoomInvite))$AcceptRoomInvite = true;
		#endregion Validate argument

		#region Additional property
		// $Error = null;
		#endregion Additional property

		#region Initialize object
		// $this->_InstanceLimit = 1; // Limit instantiation

		// Set properties from constructor argument
		// - Any local variable defined above will be treated as object property
		parent::__construct(get_defined_vars());

		// $this->_ReadOnlyProperty = "Error, Request, Response, Time"; // Property name list by array or CSV
		
		#region Dependent property configuration
		// $this->_DependentProperty[] = ["ImpactedBy" => "URL, Method, Data, Header, JSONResponse, UserAgent", "Dependent" => "Error, Request, Response, Time"];
		#endregion Dependent property configuration
		#endregion Initialize object

		#region Custom
		// Set $Result local variable with the value to return; default = TRUE

		// $this->cURL = curl_init(); // Use a persistent cURL object for faster multiple operation

		#endregion Custom
		
		$Result = true;
		
		return $Result;
	}

	public function __destruct(){
		// curl_close($this->cURL);
		$Result = true;

		return $Result;
	}

	public function Sync(
		?string $NextBatchToken = null, 
		?int $WaitForMessageMS = 30000, 
		?bool $AcceptRoomInvite = true, 
		?string $RoomJoinMessage = null, 
	){
		if(is_null($NextBatchToken))$NextBatchToken = $this->SyncNextBatchToken;
		if(is_null($WaitForMessageMS))$WaitForMessageMS = $this->WaitForMessageMS;
		if(is_null($AcceptRoomInvite))$AcceptRoomInvite = $this->AutoAcceptInvite;
		if(is_null($RoomJoinMessage))$RoomJoinMessage = $this->RoomJoinMessage;

		$Result = $this->Synapse->API("sync?" . implode("&", array_filter([
			$NextBatchToken ? "since={$NextBatchToken}" : null, 
			$WaitForMessageMS ? "timeout={$WaitForMessageMS}" : null, 
		])), \S\Communication\cURL::METHOD_GET, $this->AuthorizationToken, null);

		if(!$Result->Error->Code)$this->SyncNextBatchToken = $Result->Response->Data->next_batch; # Fetch newer data next time

		#region Process room invite
		$Result->RoomInvite = [];

		foreach($Result->Response->Data->rooms->invite ?? [] as $RoomID => $RoomInvite){
			$Result->RoomInvite[$RoomID] = $RoomInvite;

			if($AcceptRoomInvite){
				$RoomJoinResult = $this->JoinRoom($RoomID, $RoomJoinMessage);
				$Result->RoomInvite[$RoomID]->Error = (object)["Code" => $RoomJoinResult->Error->Code, "Message" => $RoomJoinResult->Error->Message];
			}
		}
		#endregion Process room invite

		return $Result;
	}

	public function JoinRoom(
		string $RoomID, 
		?string $Message = null, 
	){
		$Result = $this->Synapse->API("join/$RoomID", \S\Communication\cURL::METHOD_POST, $this->AuthorizationToken, null);
		if(!$Result->Error->Code && $Message)$this->SendMessage($RoomID, $Message);

		return $Result;
	}

	public function SendMessage(
		string $RoomID, 
		string $Message, 
	){
		$Result = $this->Synapse->API("rooms/{$RoomID}/send/m.room.message", \S\Communication\cURL::METHOD_POST, $this->AuthorizationToken, [
			"msgtype" => "m.text", 
			"body" =>  strip_tags($Message),
			"format" =>  "org.matrix.custom.html",
			"formatted_body" => $Message, 
		]);

		return $Result;
	}

	private function ExamplePrivateMethod():bool{ //? What does this do?
		// curl_close($this->cURL);
		// $this->cURL = curl_init();

		$Result = true;

		return $Result;
	}
}
?>