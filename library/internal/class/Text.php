<?php
namespace S;

require_once __DIR__ . "/Base.php";
require_once __DIR__ . "/Exception.php";

class Text extends Base{
	#region constant
	#region Public constant
	public const ERROR_INSUFFICIENT_LENGTH = ["Code" => 99999, "Message" => "Insufficient length", ];
	#endregion Public constant
	#endregion constant
	public function __construct(
		?string $Value = null, 
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

	public static function Pad(
		int $Length, 
		?string $Content = null, 
		?string $Pad = " ", 
		?bool $LeftAdjust = true, 
	):string{
		if(strlen($Content) > $Length){
			throw new \ErrorException(self::ERROR_INSUFFICIENT_LENGTH["Message"], self::ERROR_INSUFFICIENT_LENGTH["Code"]);
		}

		if(is_null($Pad))$Pad = " ";

		$Padder = str_repeat($Pad, ceil(($Length - strlen($Content)) / 2));
		$Result = $Padder . $Content . $Padder;
		$ResultLength = strlen($Result);

		if($ResultLength > $Length)$Result = substr($Result, (int)$LeftAdjust, $ResultLength - 1);

		return $Result;
	}

	public static function ReplateWhiteSpace(
		string $Content, 
		?string $Replacement = "", 
	){
		if(is_null($Replacement))$Replacement = "";

		$Result = str_replace(str_split(" \t\r\n"), $Replacement, $Content);

		return $Result;
	}
}
?>