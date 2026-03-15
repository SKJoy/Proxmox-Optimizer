<?php
namespace S\External\ProxmoxVE;

require_once __DIR__ . "/API.php";
require_once __DIR__ . "/Resource/Configuration.php";
require_once __DIR__ . "/Resource/CurrentStatus.php";

enum RESOURCE_TYPE:string{ //* Must exactly match ProxmoxVE reported lowercase value
	case LXC = "LXC";
	case QEMU = "QEMU";
	case SDN = "SDN";
	case STORAGE = "STORAGE";
	case NODE = "NODE";

	public function Symbol(){
		return match($this){
			self::LXC => "📟", 
			self::QEMU => "💻", 
			self::SDN => "📶", 
			self::STORAGE => "💿", 
			self::NODE => "💠", 
		};
	}
}

enum RESOURCE_STATUS:string{ //* Must exactly match ProxmoxVE reported lowercase value
	case RUNNING = "RUNNING";
	case STOPPED = "STOPPED";
	case ONLINE = "ONLINE";
	case AVAILABLE = "AVAILABLE";
	case OK = "OK";
	case UNKNOWN = "UNKNOWN";
}

class Resource extends \S\Base{
	#region Public constant
	// public const STATUS_STOPPED = "STOPPED";
	#endregion Public constant

	#region Internal
	// private ?object $HTTPConnection = null;
	#endregion Internal

	public function __construct(
		\S\External\ProxmoxVE\API $API, 
		?string $Node = null, 
		?string $ID = null, 
		?RESOURCE_TYPE $Type = null, 
		?RESOURCE_STATUS $Status = null, 
		?int $VMID = null, 
		?string $Name = null, 
		?bool $Template = null, 
		null|string|array $Tag = null, 
		?int $CPU = null, 
		?int $Memory = null, 
		?int $Storage = null, // MB
		?float $CPULoad = null, // Percentile
		?int $MemoryConsumption = null, // MB
		?int $StorageConsumption = null, // MB
		?int $Uptime = null, 
		?Resource\Configuration $Configuration = null, 
		?Resource\CurrentStatus $CurrentStatus = null, 
		?string $LockName = null, // Name/reason when the resource is locked in PVE
	){ //var_dump(get_defined_vars()); //exit;
		#region Validate argument
		if(is_null($Tag))$Tag = [];
		if(is_string($Tag))$Tag = explode(" ", trim($Tag));
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

		if(is_null($this->_Property->Configuration) && $this->_Property->Node && $this->_Property->ID){
			$this->LoadConfiguration();
			$this->CurrentStatus();
		}

		$Result = true;
		
		#endregion Custom
		
		return $Result;
	}

	public function __destruct(){
		// curl_close($this->ReusablecURLObject);

		$Result = true;

		return $Result;
	}

	public function LoadConfiguration(){
		$Information = $this->_Property->API->Call("nodes/{$this->_Property->Node}/{$this->_Property->ID}/config");
		
		if(!$Information->Error->Code)$this->_Property->Configuration = new Resource\Configuration(
			isset($Information->Data->unprivileged) ? !(bool)$Information->Data->unprivileged : null, 
			$Information->Data->arch ?? null, 
			$Information->Data->cpulimit ?? null, // Consider virtually to be the same as Resource->CPU when value is NULL/0
			$Information->Data->swap ?? null, 
			$Information->Data->ostype ?? null, 
			$Information->Data->rootfs ?? null, 
			$Information->Data->net0 ?? null, 
			$Information->Data->hostname ?? null, 
			$Information->Data->features ?? null, 
			$Information->Data->onboot ?? null, 
			$Information->Data->digest ?? null, 
		);

		$Result = (object)[
			"Error" => $Information->Error, 
			"Configuration" => $this->_Property->Configuration, 
		];

		return $Result;
	}

	public function CurrentStatus(){
		$Information = $this->_Property->API->Call("nodes/{$this->_Property->Node}/{$this->_Property->ID}/status/current");

		$Result = (object)[
			"Error" => $Information->Error, 
			"CurrentStatus" => new Resource\CurrentStatus(
				$Information->Data->pid ?? null, 
			), 
		];

		return $Result;
	}

	public function SetConfiguration(
		float $CPULimit, 
		int $Memory, // MB
		int $SWAP, // MB
		// int $Storage, //! Not implemented
	){
		$Result = $this->_Property->API->Call(
			"nodes/{$this->_Property->Node}/{$this->_Property->ID}/config", 
			[
				"cpulimit" => $CPULimit, 
				"memory" => $Memory, 
				"swap" => $SWAP, 
			], 
			\S\Communication\HTTP_METHOD::PUT
		);

		return $Result;
	}
}
?>