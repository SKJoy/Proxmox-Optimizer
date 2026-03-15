<?php
namespace S\External;

class MailU extends \S\Base{
	#region Public constant
	// public const METHOD_HEAD = "HEAD";
	// public const METHOD_GET = "GET";
	// public const METHOD_POST = "POST";
	// public const METHOD_PUT = "PUT";
	// public const METHOD_PATCH = "PATCH";
	// public const METHOD_DELETE = "DELETE";
	
	// public const USER_AGENT_GENERIC = "sPHP/1.0";
	// public const USER_AGENT_WINDOWS_11_GOOGLE_CHROME = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/XX.0.0.0 Safari/537.36";

	// protected const HTTP_STATUS_MESSAGE_BY_CODE = [
	// 	200 => "OK", 
	// 	401 => "Unauthorized", 
	// 	403 => "Forbidden", 
	// 	404 => "Not found", 
	// 	500 => "Internal server error", 
	// 	501 => "Internal server error", 
	// 	502 => "Internal server error", 
	// 	503 => "Internal server error", 
	// ];

	// public const AUTHORIZATION_TYPE_API_TOKEN = "API_TOKEN";
	// public const AUTHORIZATION_TYPE_BASIC = "BASIC";
	// public const AUTHORIZATION_TYPE_BEARER = "BEARER";
	#endregion Public constant

	#region Internal
	private ?object $Connection = null;
	#endregion Internal

	public function __construct(
		?string $APIBaseURL = null, // https://mailu.tld
		?string $APIKey = null, 
	){
		#region Validate argument
		// - Argument is converted into object property

		// if(is_null($Reconnect))$Reconnect = false;
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

		$this->Connection = new \S\Communication\cURL(null, null, null, null, \S\Communication::AUTHORIZATION_TYPE_API_TOKEN, $this->APIKey, null, null, null, true);

		#endregion Custom
		
		$Result = true;
		
		return $Result;
	}

	public function __destruct(){
		curl_close($this->cURL);
		$Result = true;

		return $Result;
	}

	private function ExecuteAPI(
		string $APIEndpoint, 
		?string $Method = \S\Communication::METHOD_GET, 
		null|array|string $Data = null, 
	){
		#region Validate argument
		if(is_null($Method))$Method = \S\Communication::METHOD_GET;
		if($Data && in_array($Method, [\S\Communication::METHOD_HEAD, \S\Communication::METHOD_GET]))$Method = \S\Communication::METHOD_POST;
		#endregion Validate argument

		$this->Connection->Execute("{$this->APIBaseURL}/{$APIEndpoint}", $Method, $Data);

		$Result = (object)[
			"Error" => $this->Connection->Error, 
			"Response" => $this->Connection->Response, 
		];

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