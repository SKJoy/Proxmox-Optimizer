<?php
namespace S\External;

require_once __DIR__ . "/OpenAI/Model.php";

class OpenAI extends \S\Base{
	#region Public constant
	// public const MIME_TYPE_APPLICATION_JSON = "application/json";
	#endregion Public constant

	#region Internal
	// private ?object $HTTPConnection = null;
	#endregion Internal

	public function __construct(
		OpenAI\API $API, 
	){
		#region Validate argument
		// if(is_null($ConnectionTimeout))$ConnectionTimeout = 300;
		#endregion Validate argument

		#region Additional property
		// $Request = null;
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

		// $this->HTTPConnection = new \S\Communication\HTTP();

		$Result = true;
		
		#endregion Custom
		
		return $Result;
	}

	public function __destruct(){
		// curl_close($this->ReusablecURLObject);

		$Result = true;

		return $Result;
	}

	public function Model(){
		$APIResult = $this->_Property->API->Call("models");
		
		if($APIResult->Error->Code){
			$Model = [];
		}
		else{
			foreach($APIResult->Data->data as $Data){
				$ID = explode(":", $Data->id);

				$Model[] = new OpenAI\Model(
					$this->_Property->API, 
					$ID[0], 
					$ID[1] ?? null, 
					$Data->name, 
				);
			}
		}

		$Result = (object)[
			"Error" => $APIResult->Error, 
			"Model" => $Model, 
		];

		return $Result;
	}
}
?>