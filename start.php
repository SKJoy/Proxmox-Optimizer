<?php
#region Enable all type of error reporting
ini_set("display_errors", true);
ini_set("display_startup_errors", true);
error_reporting(E_ALL);

ini_set("log_errors", true);
$ErrorLogFile = __DIR__ . "/error.php.log";
ini_set("error_log", $ErrorLogFile);
#endregion Enable all type of error reporting

ExitOnCommandFile(); //? Exit immediately if exit command file is found

#region Load library
$LibraryPath = __DIR__ . "/library/";
require "{$LibraryPath}internal/class/Application.php";
require "{$LibraryPath}internal/class/External/ProxmoxVE.php";
require "{$LibraryPath}internal/class/External/OpenAI.php";
require "{$LibraryPath}external/vendor/autoload.php";
#endregion Load library

//! Prevent multiple instances
if(\S\Application::Running())die(PHP_EOL . "😈 I am already running!" . PHP_EOL . PHP_EOL);

#region Load environment file into variables
use Dotenv\Dotenv;
$EnvironmentFileLoaderScript = "load-environment-variable";
$VLucasDotEnv = Dotenv::createImmutable(__DIR__, ".env"); //* This line here allows reloads
require __DIR__ . "/{$EnvironmentFileLoaderScript}.php";
#endregion Load environment file into variables

// Clean PHP error log if exceeds size limit
if(file_exists($ErrorLogFile) && filesize($ErrorLogFile) > ($PHPErrorLogSizeLimit * 1024))unlink($ErrorLogFile);

$LoadStatus = (object)[
	"Low" => (object)["Title" => "Low", "Symbol" => "🟦", ], 
	"Normal" => (object)["Title" => "Normal", "Symbol" => "🟩", ], 
	"High" => (object)["Title" => "High", "Symbol" => "🟧", ], 
	"OK" => (object)["Title" => "OK", "Symbol" => "✅", ], 
	"Alert" => (object)["Title" => "Alert", "Symbol" => "🟡", ], 
];

$Action = (object)[
	"Decrease" => (object)["Title" => "Decrease", "Symbol" => $LoadStatus->Low->Symbol, ], 
	"Ignore" => (object)["Title" => "Ignore", "Symbol" => $LoadStatus->Normal->Symbol, ], 
	"Increase" => (object)["Title" => "Increase", "Symbol" => $LoadStatus->High->Symbol, ], 
];

$JSONFile = __DIR__ . "/Data.json";
$ProxmoxVE = new \S\External\ProxmoxVE(new \S\External\ProxmoxVE\API($PVEAPIBaseURL, $PVEAPIUser, $PVEAPIID, $PVEAPIToken, null, null, $PVEAPIConnectionTimeout, ), );

$OptimizationData = [
	"Optimization" => [
		"Unit" => [
			"CPU" => [
				"Type" => "Number", 
				"Expression" => "Percentile", 
			], 
			"Memory" => [
				"Type" => "MB", 
				"Expression" => "Percentile", 
			], 
			"Storage" => [
				"Type" => "MB", 
				"Expression" => "Percentile", 
			], 
		], 
		"Threshold" => [
			"CPU" => [
				"Low" => (int)$CPULowThreshold, 
				"High" => (int)$CPUHighThreshold, 
			], 
			"Memory" => [
				"Low" => (int)$MemoryLowThreshold, 
				"High" => (int)$MemoryHighThreshold, 
			], 
			"Storage" => [
				"Low" => (int)$StorageLowThreshold, 
				"High" => (int)$StorageHighThreshold, 
			], 
		], 
		"Logic" => [
			"CPU" => [
				"Simple" => [
					"Rule" => [
						[
							"Status" => "Low", 
							"Action" => "Decrease", 
							"Reason" => "Requires less CPU", 
						], 
						[
							"Status" => "High", 
							"Action" => "Increase", 
							"Reason" => "Requires more CPU", 
						], 
					], 
					"Active" => !$CPUReverseLogic, 
				], 
				"Reverse" => [
					"Rule" => [
						[
							"Status" => "Low", 
							"Action" => "Increase", 
							"Reason" => "Allow faster processing window", 
						], 
						[
							"Status" => "High", 
							"Action" => "Decrease", 
							"Reason" => "Keep CPU usage stable across the infrastructure", 
						], 
					], 
					"Active" => $CPUReverseLogic, 
				], 
			], 
			"Memory" => [
				"Simple" => [
					"Rule" => [
						[
							"Status" => "Low", 
							"Action" => "Decrease", 
							"Reason" => "Requires less memory", 
						], 
						[
							"Status" => "High", 
							"Action" => "Increase", 
							"Reason" => "Requires more memory", 
						], 
					], 
					"Active" => true, 
				], 
			], 
			"Storage" => [
				"Simple" => [
					"Rule" => [
						[
							"Status" => "Low", 
							"Action" => "Decrease", 
							"Reason" => "Requires less storage", 
						], 
						[
							"Status" => "High", 
							"Action" => "Increase", 
							"Reason" => "Requires more storage", 
						], 
					], 
					"Active" => true, 
				], 
			], 
		], 
		"Pass" => [
			"Count" => $PassCount, 
			"Interval" => $PassInterval, 
			"Period" => "Second", 
		], 
	], 
];

for($OptimizeCounter = 1; $OptimizeCounter <= $OptimizeCount; $OptimizeCounter++){ //? Start optimization process
	require __DIR__ . "/{$EnvironmentFileLoaderScript}.php"; //? Reload environment file per optimization process
	// require __DIR__ . "/test.php";
	require __DIR__ . "/scan.php";

	sleep($PassInterval);
}

//* Must remove PIDFile before fork; otherwise fork will exit
$PIDFile = __DIR__ . "/pid"; // \S\Application::Running() automatically creates this file
if(file_exists($PIDFile))unlink($PIDFile);
//* Must ensure process exits after this point

if($Fork){
	sleep($PassInterval); //? Put an interval before next iteration
	exec("php " . __FILE__ . " > /dev/null 2>&1 &");

	print <<<CONTENT
	✨ Forking next instance...
	CONTENT;
}

function ExitOnCommandFile():bool{ //? Exit immediately if exit command file is found
	if(file_exists(__DIR__ . "/stop.command"))die("✋ Exiting due to stop command file");

	$Result = true;

	return $Result;
}
?>