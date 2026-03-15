<?php
namespace Test;

require __DIR__ . "/Message.php";

$EvolutionAPI = new \S\External\EvolutionAPI("https://evolution-api.piti.cloud", "HJPJADT4CI3DW9BMUYPSMBMZH3RNZNIQ");
$Instance = new \S\External\EvolutionAPI\Instance($EvolutionAPI, "Not Found VN 1", "2B00EE0BA694-404E-88C6-FA5B4B8788E8");
$User = new \S\External\EvolutionAPI\User("8801552601833", null, null, "Broken", "Arrow", null, null);
$Message = new \S\External\EvolutionAPI\Message($Instance, $User, "Welcome %FullName% to the new API world!\n\n-- " . date("r") . "");

$TestInstance = new \S\External\EvolutionAPI\Instance($EvolutionAPI, "Remove Me / 1", null, "8801732703732");

var_dump(
	// $EvolutionAPI, 
	$TestInstance->Delete(), sleep(2), 
	$TestInstance->Create(), 
	// $TestInstance->APIKey, 
	// $TestInstance->ID, 
	// $TestInstance->Status, 
	// $TestInstance->Setting, 
	// $TestInstance->QRCode(), 
	// $TestInstance->QR, 
	// $TestInstance->CallRejectionMessage, 
	// $Message->Send(), 
);
?>