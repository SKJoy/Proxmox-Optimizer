<?php
namespace S;

abstract class Base{
	#region Constant
	public const ERROR_NO_ERROR = ["Code" => 0, "Message" => null];
	public const ERROR_UNSUPPORTED_ENVIRONMENT = ["Code" => 99999, "Message" => "Unsupported environment", ];
	public const ERROR_INSTANCE_LIMIT_REACHED = ["Code" => 99989, "Message" => "Maximum object instance limit reached"];
	public const ERROR_PROPERTY_NEW_NOT_ALLOWED = ["Code" => 99988, "Message" => "New property not allowed"];
	public const ERROR_PROPERTY_NOT_FOUND = ["Code" => 99987, "Message" => "Property not found"];
	public const ERROR_PROPERTY_READ_ONLY = ["Code" => 99986, "Message" => "Property is read only"];
	#endregion Constant

	#region Object attribute
	protected ?int $_InstanceLimit = null;
	protected ?object $_Property = null;
	protected bool $_AllowNewProperty = true;
	protected array|string $_ReadOnlyProperty = []; // Property name list by array or CSV

	protected array $_DependentProperty = [];
	/* Example:
	[
		["ImpactedBy" => PropertyListArray, "Dependent" => PropertyListArray], 
		["ImpactedBy" => PropertyListArray, "Dependent" => PropertyListArray], 
		...
	]
	*/
	#endregion Object attribute

	#region Internal variable
	private static int $InstanceCount = 0;
	private string $ClassName = __CLASS__;
	#endregion Internal variable

	public function __construct(
		?array $Argument = [], // Carries arguments with names passed to the instantiated object
	){
		$this->ClassName = get_class($this);
		$this->_InstanceLimit = (int)$this->_InstanceLimit;

		if(!$this->_InstanceLimit || self::$InstanceCount < $this->_InstanceLimit){ // Maximum instance limit reached for this object //! Error
			self::$InstanceCount++; // Keep track of instance count
			$Result = true;
		}
		else{
			throw new \ErrorException(self::ERROR_INSTANCE_LIMIT_REACHED["Message"] . ": {$this->ClassName}", self::ERROR_INSTANCE_LIMIT_REACHED["Code"], E_ERROR);
			$Result = false;
		}

		$this->_Property = (object)[]; //? Convert internal property store into an object
		foreach($Argument as $Key => $Value)$this->_Property->$Key = $Value; //? Set initial property values from arguments

		return $Result;
	}

	public function __get( //? Get property value
		string $PropertyName, 
	){
		if(method_exists($this, $PropertyName)){ // Call the method with the property's same name
			$Result = $this->$PropertyName($PropertyName);
		}
		else{ // Process from internal property store
			if(isset($this->_Property->$PropertyName)){ // Property exists
				$Result = $this->_Property->$PropertyName;
			}
			else{
				if($this->_AllowNewProperty){ // Allow creating new property
					$this->_Property->$PropertyName = null;
					$Result = $this->_Property->$PropertyName;
				}
				else{ // Property does not exist //! Error
					$Result = null;
					throw new \ErrorException(self::ERROR_PROPERTY_NOT_FOUND["Message"] . ": {$PropertyName}", self::ERROR_PROPERTY_NOT_FOUND["Code"], E_ERROR);
				}

			}
		}

		return $Result;
	}

	public function __set( //? Set property value
		string $PropertyName, 
		$Value, 
	){		
		if(method_exists($this, $PropertyName)){ // Call the method with the property's same name
			$Result = $this->$PropertyName($PropertyName, $Value);
		}
		else{ // Process from internal property store
			if(
				isset($this->_Property->$PropertyName) || // Property exists
				$this->_AllowNewProperty // Allow creating new property
			){
				if(!is_array($this->_ReadOnlyProperty))$this->_ReadOnlyProperty = array_filter(explode(",", str_replace(" ", "", $this->_ReadOnlyProperty)));;

				if(in_array($PropertyName, $this->_ReadOnlyProperty)){ // Property is read only //! Error
					$Result = false;
					throw new \ErrorException(self::ERROR_PROPERTY_READ_ONLY["Message"] . ": {$this->ClassName}->{$PropertyName}", self::ERROR_PROPERTY_READ_ONLY["Code"], E_ERROR);
				}
				else{
					$this->_Property->$PropertyName = $Value;
					$this->_ProcessDependentProperty($PropertyName, null);
					$Result = true;
				}
			}
			else{ // New property //! Error
				$Result = false;
				throw new \ErrorException(self::ERROR_PROPERTY_NEW_NOT_ALLOWED["Message"] . ": {$this->ClassName}->{$PropertyName}", self::ERROR_PROPERTY_NEW_NOT_ALLOWED["Code"], E_ERROR);
			}
		}

		return $Result;
	}

	public function __call( //? Get/Set property value with procedural/function call
		string $PropertyName, 
		array $Argument = [], 
	){
		$Result = count($Argument) ? $this->__set($PropertyName, $Argument[0]) : $this->__get($PropertyName);
		return $Result;
	}

	public function __destruct(){
		$Result = true;
		return $Result;
	}

	protected function _ProcessDependentProperty( //? Reset dependent property value if impacting property is set/reset
		string $PropertyName, // Property that impacts the dependent properties
		$ResetValue = null, // Value to reset with
	):bool{
		foreach($this->_DependentProperty as $RowIndex => $CurrentRow){
			if(!is_array($CurrentRow["ImpactedBy"]))$this->_DependentProperty[$RowIndex]["ImpactedBy"] = array_filter(explode(",", str_replace(" ", "", $CurrentRow["ImpactedBy"])));
			if(!is_array($CurrentRow["Dependent"]))$this->_DependentProperty[$RowIndex]["Dependent"] = array_filter(explode(",", str_replace(" ", "", $CurrentRow["Dependent"])));

			if(in_array($PropertyName, $this->_DependentProperty[$RowIndex]["ImpactedBy"]))foreach($this->_DependentProperty[$RowIndex]["Dependent"] as $DependentProperty)$this->_Property->$DependentProperty = null;
		}

		$Result = true;

		return $Result;
	}

	public static function StringToArray( //? Parse text as a CSV line
		string $Data, 
		?string $Separator = ",", 
		?bool $IgnoreNull = true, 
		?bool $Trim = true, 
		?string $Enclosure = "\"", 
		?string $EscapeCharacter = "\\", 
	):array{
		#region Validate argument
		if(is_null($Separator))$Separator = ",";
		if(is_null($IgnoreNull))$IgnoreNull = true;
		if(is_null($Trim))$Trim = true;
		if(is_null($Enclosure))$Enclosure = "\"";
		if(is_null($EscapeCharacter))$EscapeCharacter = "\\";
		#endregion Validate argument

		$Result = str_getcsv($Data, $Separator, $Enclosure, $EscapeCharacter); // Parse line into CSV

		// Apply conditional processing
		if($IgnoreNull)$Result = array_filter($Result);
		if($Trim)foreach($Result as $Key => $Value)$Result[$Key] = trim($Value);

		return $Result; // Return result
	}
}
?>