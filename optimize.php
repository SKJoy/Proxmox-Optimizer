<?php
$OptimizationMessage = <<<CONTENT

✨ Optimizing
───────────────────────────────────────────────────────────────────────
CONTENT;

if(!count($OptimizationResource))$OptimizationMessage .= PHP_EOL . "* No item fond to optimize"; //! No item for optimization

foreach($OptimizationResource as $OptimizationResourceItem){
	$SWAP = $OptimizationResourceItem["Memory"];
	if($SWAP < $SWAPMinimum)$SWAP = $SWAPMinimum;
	if($SWAP > $SWAPMaximum)$SWAP = $SWAPMaximum;

	$ProxmoxResourceOptimization = $OptimizationResourceItem["Resource"]->SetConfiguration(
		$OptimizationResourceItem["CPULimit"], 
		$OptimizationResourceItem["Memory"], 
		$SWAP, 
		$OptimizationResourceItem["Storage"], 
	);

	$OptimizationResourceTitle = "{$OptimizationResourceItem["Resource"]->Type->Symbol()} {$OptimizationResourceItem["Resource"]->Type->value} 💠 {$OptimizationResourceItem["Resource"]->Node} ⚙️ {$OptimizationResourceItem["Resource"]->VMID} 🔖 {$OptimizationResourceItem["Resource"]->Name}";
	
	if($ProxmoxResourceOptimization->Error->Code){
		$OptimizationMessage .= PHP_EOL . "❌ {$OptimizationResourceTitle}";
		$OptimizationMessage .= PHP_EOL . "    ⛔ {$ProxmoxResourceOptimization->Error->Message}";
		foreach($ProxmoxResourceOptimization->Data as $ErrorKey => $ErrorMessage)$OptimizationMessage .= PHP_EOL . "    ⛔ {$ErrorKey}: {$ErrorMessage}";
	}
	else{
		$OptimizationMessage .= PHP_EOL . "✅ {$OptimizationResourceTitle}";
	}

	$OptimizationData["Node"][$OptimizationResourceItem["Resource"]->Node]["Resource"][$OptimizationResourceItem["Resource"]->ID]["Recommendation"]["CPU"]["Applied"] = true;
	$OptimizationData["Node"][$OptimizationResourceItem["Resource"]->Node]["Resource"][$OptimizationResourceItem["Resource"]->ID]["Recommendation"]["Memory"]["Applied"] = true;
	// $OptimizationData["Node"][$OptimizationResourceItem["Resource"]->Node]["Resource"][$OptimizationResourceItem["Resource"]->ID]["Recommendation"]["Storage"]["Applied"] = true;
}

print $OptimizationMessage . PHP_EOL;

print <<<CONTENT

📌 Tasks
───────────────────────────────────────────────────────────────────────
CONTENT;

file_put_contents($JSONFile, json_encode($OptimizationData));

require __DIR__ . "/send-email-notification.php";
require __DIR__ . "/call-webhook.php";
?>