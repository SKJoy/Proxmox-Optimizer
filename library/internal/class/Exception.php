<?php
namespace S;

require_once __DIR__ . "/Base.php";

class Exception extends Base{
	#region Public constant
	// public const HTTP_METHOD_HEAD = "HEAD";
	#endregion Public constant

	#region Internal
	// private ?object $cURL = null;
	#endregion Internal

	public function __construct(
		?int $Code = 0, 
		?string $Message = null, 
		?string $File = null, 
		?int $Line = null, 
		?string $Class = null, 
		?string $Type = null, 
		?string $Function = null, 
	){
		#region Validate argument
		if(is_null($Code))$Code = 0;
		#endregion Validate argument

		#region Initialize object
		$this->_InstanceLimit = null; // Limit instantiation
		parent::__construct(get_defined_vars()); // Let parent set properties from constructor argument
		// $this->_ReadOnlyProperty = "Manager"; // Property name list by array or CSV
		
		// Dependent property configuratin
		$this->_DependentProperty[] = ["ImpactedBy" => "Code", "Dependent" => "Message, File, Line"];
		#endregion Initialize object

		#region Custom
		// $this->cURL = curl_init(); // Use a persistent cURL object for faster multiple operation
		#endregion Custom
		
		$Result = true;
		
		return $Result;
	}

	public function __destruct(){
		// $this->ClosecURL();
		$Result = true;

		return $Result;
	}

	public function Throw(
		int $Backstep = 0, 
	){
		$Backtrace = debug_backtrace()[1 + $Backstep];
		
		$this->_Property->File = $Backtrace["file"];
		$this->_Property->Line = $Backtrace["line"];
		$this->_Property->Class = $Backtrace["class"];
		$this->_Property->Type = $Backtrace["type"];
		$this->_Property->Function = $Backtrace["function"];

		throw new \ErrorException(
			$this->_Property->Message, 
			$this->_Property->Code, 
			1, 
			$this->_Property->File, 
			$this->_Property->Line, 
		);

		return $Result;
	}

	private function InternalFunction():bool{
		// curl_close($this->cURL);
		$Result = true;

		return $Result;
	}
}
?>