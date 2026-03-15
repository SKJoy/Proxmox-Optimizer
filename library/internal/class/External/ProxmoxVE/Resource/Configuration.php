<?php
namespace S\External\ProxmoxVE\Resource;

// require_once __DIR__ . "/API.php";

class Configuration extends \S\Base{
	#region Public constant
	// public const STATUS_STOPPED = "STOPPED";
	#endregion Public constant

	#region Internal
	// private ?object $HTTPConnection = null;
	#endregion Internal

	public function __construct(
		null|int|bool $Privileged = false, 
		?string $Architecture = null, 
		?float $CPULimit = null, // Consider virtually to be the same as Resource->CPU when value is NULL/0
		?int $SWAP = null, 
		?string $OSType = null, 
		null|string|array $RootFileSystem = null, 
		null|string|array $Network = [], 
		?string $Hostname = null, 
		null|string|array $Feature = [], 
		null|int|bool $Autostart = false, 
		?string $Digest = null, 
	){ //var_dump(get_defined_vars()); //exit;
		#region Validate argument
		if(is_null($Privileged))$Privileged = false;
		if(is_null($RootFileSystem))$RootFileSystem = [];
		if(is_null($Network))$Network = [];
		if(is_null($Feature))$Feature = [];
		if(is_null($Autostart))$Autostart = false;

		if(!is_bool($Privileged))$Privileged = (bool)$Privileged;

		if(is_string($RootFileSystem))$RootFileSystem = explode(",", trim($RootFileSystem));
		if(is_string($Network))$Network = explode(",", trim($Network));
		if(is_string($Feature))$Feature = explode(",", trim($Feature));

		if(!is_bool($Autostart))$Autostart = (bool)$Autostart;
		#endregion Validate argument

		#region Additional property
		// $Configuration = (object)[];
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

		// if($this->_Property->API && $this->_Property->Node && $this->_Property->ID)$this->GetConfiguration();

		$Result = true;
		
		#endregion Custom
		
		return $Result;
	}

	public function __destruct(){
		// curl_close($this->ReusablecURLObject);

		$Result = true;

		return $Result;
	}
}
?>