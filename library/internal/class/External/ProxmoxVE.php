<?php
namespace S\External;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ini_set("log_errors", 1);
// ini_set("error_log", __DIR__ . "/error.php.log");

require_once __DIR__ . "/ProxmoxVE/Resource.php";

class ProxmoxVE extends \S\Base{
	#region Public constant
	// public const MIME_TYPE_APPLICATION_JSON = "application/json";
	#endregion Public constant

	#region Internal
	// private ?object $HTTPConnection = null;
	#endregion Internal

	public function __construct(
		ProxmoxVE\API $API, 
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

	public function Resource(
		?bool $FailOnError = false, 
	):array{
		if(is_null($FailOnError))$FailOnError = false;

		$API = $this->_Property->API->Call("cluster/resources");
		$Byte2MBDivider = 1024 * 1024;
		$Resource = [];
	
		if($API->Error->Code){
			if($FailOnError)$API->Error->Throw();
		}
		else{
			foreach($API->Data as $ThisResource)$Resource[] = new ProxmoxVE\Resource(
				$this->_Property->API, 
				$ThisResource->node, 
				$ThisResource->id, 
				ProxmoxVE\RESOURCE_TYPE::from(strtoupper($ThisResource->type)), 
				ProxmoxVE\RESOURCE_STATUS::from(strtoupper($ThisResource->status)), 
				$ThisResource->vmid ?? null, 
				$ThisResource->name ?? null, 
				isset($ThisResource->template) ? (bool)$ThisResource->template : null, 
				$ThisResource->tags ?? null, 
				$ThisResource->maxcpu ?? null, 
				isset($ThisResource->maxmem) ? round($ThisResource->maxmem / $Byte2MBDivider, 0) : null, 
				isset($ThisResource->maxdisk) ? round($ThisResource->maxdisk / $Byte2MBDivider, 0) : null, 
				isset($ThisResource->cpu) ? round($ThisResource->cpu * 100, 2) : null, 
				isset($ThisResource->mem) ? round($ThisResource->mem / $Byte2MBDivider, 0) : null, 
				isset($ThisResource->disk) ? round($ThisResource->disk / $Byte2MBDivider, 0) : null, 
				$ThisResource->uptime ?? null, 
				null, 
				null, 
				$ThisResource->lock ?? null, 
			);			
		}

		return $Resource;
	}
}
?>