<?php
namespace S\External\EvolutionAPI;

class User extends \S\Base{
	#region Public constant
	// public const METHOD_HEAD = "HEAD";
	#endregion Public constant

	#region Internal
	// private ?object $cURL = null;
	#endregion Internal

	public function __construct(
		string $Number, 
		?string $Email = null, 
		?string $Password = null, 
		?string $FirstName = null, 
		?string $LastName = null, 
		?string $MiddleName = null, 
		?string $OTP = null, 
	){
		#region Validate argument
		// - Argument is converted into object property

		// if(is_null($UserAgent))$UserAgent = self::USER_AGENT_GENERIC;
		#endregion Validate argument

		#region Additional property
		$FullName = null;
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

		#endregion Custom
		
		$Result = true;
		
		return $Result;
	}

	public function __destruct(){
		// curl_close($this->cURL);
		$Result = true;

		return $Result;
	}

	public function FullName():string{
		if(is_null($this->_Property->FullName))$this->_Property->FullName = implode(" ", array_filter([$this->_Property->FirstName, $this->_Property->MiddleName, $this->_Property->LastName]));

		$Result = $this->_Property->FullName;

		return $Result;
	}
}
?>