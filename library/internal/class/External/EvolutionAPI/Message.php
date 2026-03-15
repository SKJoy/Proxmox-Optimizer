<?php
namespace S\External\EvolutionAPI;

require_once __DIR__ . "/Instance.php";
require_once __DIR__ . "/User.php";

class Message extends \S\Base{
	#region Public constant
	public const ERROR_MISSING_NUMBER = ["Code" => 99999, "Message" => "Number missing", ];
	public const ERROR_MISSING_MESSAGE = ["Code" => 99998, "Message" => "Message missing", ];
	#endregion Public constant

	#region Internal
	// private ?object $cURL = null;
	#endregion Internal

	public function __construct(
		\S\External\EvolutionAPI\Instance $Instance, 
		?\S\External\EvolutionAPI\User $User = null, 
		?string $Text = null, 
	){
		#region Validate argument
		// - Argument is converted into object property

		// if(is_null($UserAgent))$UserAgent = self::USER_AGENT_GENERIC;
		#endregion Validate argument

		#region Additional property
		// $Error = null;
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
		$Result = true;

		return $Result;
	}

	public function Send(
		?\S\External\EvolutionAPI\User $User = null, 
		?string $Text = null, 
	):object{
		#region Validate argument
		if(is_null($User))$User = $this->User;
		if(is_null($Text))$Text = $this->Text;
		#endregion Validate argument

		$Result = $this->Instance->SendMessage(
			$User->Number, 
			str_replace([
				"%Email%", 
				"%Password%", 
				"%FirstName%", 
				"%MiddleName%", 
				"%LastName%", 
				"%FullName%", 
				"%OTP%", 
			], [
				$User->Email, 
				$User->Password, 
				$User->FirstName, 
				$User->MiddleName, 
				$User->LastName, 
				$User->FullName, 
				$User->OTP, 
			], $Text)
		);

		return $Result;
	}
}
?>