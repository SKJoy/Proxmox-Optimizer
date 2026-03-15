<?php
namespace S\External\OpenAI\Chat;

require_once __DIR__ . "/Model.php";

enum Role:string{
	case System = "System";
	case User = "User";

	public function Symbol(){
		return match($this){
			self::System => "🤖", 
			self::User => "🙂", 
		};
	}
}

class Message extends \S\Base{
	#region Public constant
	// public const STATUS_STOPPED = "STOPPED";
	#endregion Public constant

	#region Internal
	// private ?object $HTTPConnection = null;
	#endregion Internal

	public function __construct(
		string $Message, 
		?Role $Role = Role::User, 
	){ //var_dump(get_defined_vars()); //exit;
		#region Validate argument
		if(is_null($Role))$Role = Role::User;
		#endregion Validate argument

		#region Additional property
		// $Name = null;
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

		// $this->LoadModel();

		$Result = true;
		
		#endregion Custom
		
		return $Result;
	}

	public function __destruct(){
		// curl_close($this->ReusablecURLObject);

		$Result = true;

		return $Result;
	}

	public function Array(
		?string $Message = null, 
		?Role $Role = null, 
	):array{
		if(is_null($Message))$Message = $this->_Property->Message;
		if(is_null($Role))$Role = $this->_Property->Role;

		$Result = [
			"role" => strtolower($Role->value), 
			"content" => $Message, 
		];

		return $Result;
	}

	public function JSON(
		?string $Message = null, 
		?Role $Role = null, 
	):string{
		$Result = json_encode($this->Array($Message, $Role));

		return $Result;
	}
}
?>