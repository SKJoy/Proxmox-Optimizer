<?php
namespace S\External;

require_once __DIR__ . "/../Communication/cURL.php";

class EvolutionAPI extends \S\Base{
	#region Public constant
	// public const METHOD_HEAD = "HEAD";
	#endregion Public constant

	#region Internal
	private ?object $cURL = null;
	#endregion Internal

	public function __construct(
		string $APIBaseURL, 
		?string $APIKey = null, 
	){
		#region Validate argument
		// - Argument is converted into object property

		// if(is_null($UserAgent))$UserAgent = self::USER_AGENT_GENERIC;

		$APIKey = trim($APIKey);
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

		$this->cURL = new \S\Communication\cURL(
			$APIBaseURL, 
			null, 
			null, 
			null, 
			\S\Communication\cURL::AUTHORIZATION_TYPE_API_KEY, 
			null, 
			null, 
			null, 
			null, 
			true
		);

		#endregion Custom
		
		$Result = true;
		
		return $Result;
	}

	public function __destruct(){
		$Result = true;

		return $Result;
	}

	public function API(
		string $Endpoint, 
		string $APIKey, 
		null|string|array|object $Data = null, 
		?string $Method = \S\Communication\cURL::METHOD_GET, 
	):object{
		#region Validate argument
		// if(is_null($Method))$Method = $this->cURL->Method;
		#endregion Validate argument

		$this->cURL->URL = "{$this->APIBaseURL}/{$Endpoint}";
		$this->cURL->Method = $Method;
		$this->cURL->AuthorizationToken = $APIKey;
		$this->cURL->Data = $Data;

		$this->cURL->Header = [
			"content-type" => \S\Communication\cURL::MIME_TYPE_APPLICATION_JSON, 
		];

		$this->cURL->Execute();

		$Result = (object)[
			"Error" => $this->cURL->Error, 
			"Response" => $this->cURL->Response, 
			"Request" => $this->cURL->Request, 
			"Time" => $this->cURL->Time, 
		];
		
		if($Result->Response->Data->error ?? false)$Result->Error->Message = $Result->Response->Data->response->message[0];

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