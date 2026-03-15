<?php
namespace S\External\OpenAI;

require_once __DIR__ . "/API.php";
require_once __DIR__ . "/Message.php";

class Model extends \S\Base{
	#region Public constant
	// public const STATUS_STOPPED = "STOPPED";
	#endregion Public constant

	#region Internal
	// private ?object $HTTPConnection = null;
	#endregion Internal

	public function __construct(
		\S\External\OpenAI\API $API, 
		string $ID = null, 
		?string $Tag = null, 
		?string $System = null, 
		null|array|string $History = null, 
		?bool $Stream = false, 
		?bool $Think = false, 
	){ //var_dump(get_defined_vars()); //exit;
		#region Validate argument
		if(is_null($Tag))$Tag = "";

		if(is_null($History))$History = [];
		if(!array($History))$History = [$History];

		if(is_null($Stream))$Stream = false;
		if(is_null($Think))$Think = false;
		#endregion Validate argument

		#region Additional property
		$Name = null;
		#endregion Additional property

		#region Initialize object
		// $this->_InstanceLimit = 1; // Limit instantiation

		//? Convert all arguments and local variables above this point into properties
		parent::__construct(get_defined_vars());

		//* Properties that cannot be changed outside this class
		// $this->_ReadOnlyProperty = "Error, Request, Response, Debug";
		
		#region Properties that get reset by changing other properties
		// $this->_DependentProperty[] = ["ImpactedBy" => "URL, Method, Data, Header, ExpectJSONResponse, UserAgent", "Dependent" => "Error, Request, Response, Debug"];
		#endregion Properties that get reset by changing other properties
		#endregion Initialize object

		#region Custom
		//* Set $Result variable with the value to return; default = TRUE

		// $this->LoadModel();

		$Result = true;
		
		#endregion Custom
		
		return $Result;
	}

	public function __destruct(){
		// curl_close($this->ReusablecURLObject);

		$Result = true;

		return $Result;
	}

	public function Chat(
		string $Message, 
		null|array|string $History = null, 
	):object{
		if(is_null($History))$History = [];
		if(!array($History))$History = [$History];

		$ChatMessage[] = new Chat\Message($this->_Property->System, Chat\Role::System);
		foreach(array_merge($this->_Property->History, $History) as $HistoryItem)$ChatMessage[] = new Chat\Message($HistoryItem->Content, $HistoryItem->Role);
		$ChatMessage[] = new Chat\Message($Message, Chat\Role::User);

		foreach($ChatMessage as $ChatMessageItem)$ChatMessageArray[] = $ChatMessageItem->Array();

		$Result = $this->_Property->API->Call(
			"chat/completions", 
			(object)[
				"model" => implode(":", [$this->_Property->ID, $this->_Property->Tag ?? "", ]), 
				"stream" => $this->_Property->Stream, 
				"think" => $this->_Property->Think, 
				"messages"=> $ChatMessageArray, 

				// Fix for Open WebUI API
				"tool_choice" => "none",
				"tools" => [], 
				"metadata" => [
					"disable_functions" => true, 
				], 
				"meta" => [
					"capabilities" => [
						"file_context" => false,
						"vision" => false,
						"file_upload" => false,
						"web_search" => false,
						"image_generation" => false,
						"code_interpreter" => false,
						"citations" => false,
						"status_updates" => false,
						"builtin_tools" => false
					],
					"builtinTools" => [
						"time" => false,
						"memory" => false,
						"chats" => true,
						"notes" => false,
						"knowledge" => false,
						"channels" => false,
						"web_search" => false,
						"image_generation" => false,
						"code_interpreter" => false
					]
				],
			], 
		);

		return $Result;
	}
}
?>