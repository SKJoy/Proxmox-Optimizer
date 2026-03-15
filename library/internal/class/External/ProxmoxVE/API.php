<?php
namespace S\External\ProxmoxVE;

require_once __DIR__ . "/../../Communication/HTTP.php";

class API extends \S\Base{
	#region Public constant
	// public const MIME_TYPE_APPLICATION_JSON = "application/json";
	#endregion Public constant

	#region Internal
	private ?object $HTTPConnection = null;
	#endregion Internal

	public function __construct(
		string $BaseURL, // https://proxmox.domain.tld
		string $User, // root@pam
		string $TokenID, // Abc123
		string $Token, // 12345678-abcd-1234-efgh-123456abcdef
		?string $HTTPBasicAuthUser = null, // UserName
		?string $HTTPBasicAuthPassword = null, // Password
		?int $ConnectionTimeout = 300, // N seconds
	){
		#region Validate argument
		if(is_null($ConnectionTimeout))$ConnectionTimeout = 300;
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

		$this->HTTPConnection = new \S\Communication\HTTP(
			$this->_Property->BaseURL, 
			true, 
			null, 
			\S\Communication\HTTP_AUTHORIZATION::AUTHORIZATION, 
			"PVEAPIToken={$this->_Property->User}!{$this->_Property->TokenID}={$this->_Property->Token}", 
			true, 
			null, 
			null, 
			$this->_Property->HTTPBasicAuthUser, 
			$this->_Property->HTTPBasicAuthPassword, 
			$this->_Property->ConnectionTimeout, 
			null, 
			\S\Communication\HTTP::USER_AGENT_GENERIC, 
			null, 
			null, 
			null, 
		);

		$Result = true;
		
		#endregion Custom
		
		return $Result;
	}

	public function __destruct(){
		// curl_close($this->ReusablecURLObject);

		$Result = true;

		return $Result;
	}

	public function Call(
		?string $Endpoint = null, // Will be appended to $BaseURL
		null|string|array|object $Data = null, // OBJECT = JSON; ARRAY = POST variable; STRING = Raw data
		?\S\Communication\HTTP_METHOD $Method = null, // Default = GET
		?string $ResponseFile = null, // Save HTTP response to file; does not include header
	):object{
		$this->HTTPConnection->URL = "{$this->_Property->BaseURL}/api2/json/{$Endpoint}";
		$this->HTTPConnection->Data = $Data;
		$this->HTTPConnection->Method = $Method;
		$this->HTTPConnection->ResponseFile = $ResponseFile;

		$Result = $this->HTTPConnection->Call();

		$Result = (object)[
			"Error" => $Result->Error, 
			"Data" => $Result->Error->Code ? $Result->Response->Data->errors ?? [] : $Result->Response->Data->data, 
		];

		return $Result;
	}
}
?>