<?php
namespace S\External\ProxmoxVE\Resource;

// require_once __DIR__ . "/API.php";

class CurrentStatus extends \S\Base{
	#region Public constant
	// public const STATUS_STOPPED = "STOPPED";
	#endregion Public constant

	#region Internal
	// private ?object $HTTPConnection = null;
	#endregion Internal

	public function __construct(
		?int $PID = null, 
	){ //var_dump(get_defined_vars()); //exit;
		#region Validate argument
		// if(is_null($Autostart))$Autostart = false;
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