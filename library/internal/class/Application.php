<?php
namespace S;

require_once __DIR__ . "/System/Process.php";

class Application extends Base{
	#region constant
	#region Public constant
	// public const ERROR_LINUX_REQUIRED = ["Code" => 99999, "Message" => "Linux required", ];
	#endregion Public constant
	#endregion constant
	public function __construct(
		// ?string $URL = null, 
	){
		#region Validate argument
		// if(is_null($ReuseConnection))$ReuseConnection = true;
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

		// $this->ReusablecURLObject = curl_init(); // Use a persistent cURL object for faster multiple operation
		
		$Result = true;

		#endregion Custom
		
		return $Result;
	}

	public static function Running(
		?string $PIDFile = null, 
	):bool{
		if(is_null($PIDFile)){ // Determine default PID file
			$Backtrace = debug_backtrace();
			$PIDFile = dirname($Backtrace[count($Backtrace) - 1]["file"]) . "/pid"; //die($PIDFile);
		}

		$IAmRunning = System\PROCESS_STATE::STOPPED; // Assume not running
		
		if(file_exists($PIDFile)){
			$ExistingProcessID = (int)file_get_contents($PIDFile);
		
			if($ExistingProcessID){
				$ExistingProcessInformation = System\Process::Information($ExistingProcessID);
		
				if(!$ExistingProcessInformation->Error->Code){
					$MyProcessInformation = System\Process::Information();
					
					if(
						(
							$ExistingProcessInformation->State == System\PROCESS_STATE::RUNNING ||
							$ExistingProcessInformation->State == System\PROCESS_STATE::SLEEPING
						) && 
						$ExistingProcessInformation->Command == $MyProcessInformation->Command &&
						$ExistingProcessInformation->Executable == $MyProcessInformation->Executable &&
						$ExistingProcessInformation->Path == $MyProcessInformation->Path
					)$IAmRunning = System\PROCESS_STATE::RUNNING;
				}		
			}
		}
		
		$Result = $IAmRunning == System\PROCESS_STATE::RUNNING;
		if(!$Result)file_put_contents($PIDFile, getmypid());

		return $Result;
	}
}
?>