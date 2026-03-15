<?php
namespace S\System;

require_once __DIR__ . "/../Text.php";

enum PROCESS_STATE:string{
    case UNKNOWN = "UNKNOWN";
    case STOPPED = "STOPPED";
    case RUNNING = "RUNNING";
    case SLEEPING = "SLEEPING";

	public function Caption(){
		return match($this){
			self::UNKNOWN => "Unknown", 
			self::STOPPED => "Stopped", 
			self::RUNNING => "Running", 
			self::SLEEPING => "Sleeping", 
		};
	}

	public function Symbol(){
		return match($this){
            self::UNKNOWN => "💠", 
            self::STOPPED => "💠", 
			self::RUNNING => "📟", 
			self::SLEEPING => "📟", 
		};
	}
}

class Process extends \S\Base{
	#region constant
	#region Public constant
	public const ERROR_INVALID_PROCESS_ID = ["Code" => 99999, "Message" => "Invalid process ID", ];
	public const ERROR_PROCESS_NOT_FOUND = ["Code" => 99998, "Message" => "Process not found", ];
	#endregion Public constant
	#endregion constant
	public function __construct(
		int $ID, 
	){
		#region Validate argument
		// if(is_null($ReuseConnection))$ReuseConnection = true;
		#endregion Validate argument

		#region Additional property
        $Command = null;
        $Executable = null;
        $Path = null;
        $State = null;
        $UserID = null;
        $GroupID = null;
		#endregion Additional property

		#region Initialize object
		// $this->_InstanceLimit = 1; // Limit instantiation

		//? Convert all arguments and local variables above this point into properties
		parent::__construct(get_defined_vars());

		//* Properties that cannot be changed outside this class
		// $this->_ReadOnlyProperty = "Error, Request, Response, Debug";
		
		#region Properties that get reset by changing other properties
		$this->_DependentProperty[] = ["ImpactedBy" => "ID, SomeOtherProperty", "Dependent" => "Command, Executable, Path, State, UserID, GroupID"];
		#endregion Properties that get reset by changing other properties
		#endregion Initialize object

		#region Custom
		//* Set $Result variable with the value to return; default = TRUE

        #region Load process information
		$Information = self::Information($this->_Property->ID);
        $this->Command = $Information->Command;
        $this->Executable = $Information->Executable;
        $this->Path = $Information->Path;
        $this->State = $Information->State;
        $this->UserID = $Information->UserID;
        $this->GroupID = $Information->GroupID;
        #endregion Load process information
		
		$Result = true;

		#endregion Custom
		
		return $Result;
	}

	public static function Information(
		?int $ID = null, // NULL = Own process ID
	):object{
        if(is_null($ID))$ID = getmypid();
        
        $Path = "/proc/{$ID}/";
        $Executable = "{$Path}exe";
        $WorkingDirectory = "{$Path}cwd";

        if(file_exists("{$Path}status")){
            foreach(explode(PHP_EOL, file_get_contents("{$Path}status")) as $Line){
                $Status = explode(":", $Line);
                $Information[trim($Status[0])] = trim($Status[1] ?? "");
            }
    
            if($Information["State"] == "R (running)"){
                $State = PROCESS_STATE::RUNNING;
            }
            elseif($Information["State"] == "S (sleeping)"){
                $State = PROCESS_STATE::SLEEPING;
            }
            else{
                $State = PROCESS_STATE::UNKNOWN;
            }
    
            $Result = (object)[
                "Error" => new \S\Exception(self::ERROR_NO_ERROR["Code"], self::ERROR_NO_ERROR["Message"], ), 
                "ID" => $ID, 
                "Command" => file_get_contents("{$Path}cmdline"), 
                "Executable" => is_link($Executable) ? readlink($Executable) : null, 
                "Path" => is_link($WorkingDirectory) ? readlink($WorkingDirectory) : null, 
                "State" => $State, 
                "UserID" => (int)\S\Text::ReplateWhiteSpace($Information["Uid"]), 
                "GroupID" => (int)\S\Text::ReplateWhiteSpace($Information["Gid"]), 
            ];
        }
        else{
            $Result = (object)[
                "Error" => new \S\Exception(self::ERROR_PROCESS_NOT_FOUND["Code"], self::ERROR_PROCESS_NOT_FOUND["Message"], ), 
            ];
        }        
        
		return $Result;
	}
}
?>