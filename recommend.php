<?php
#region Detect trends if the full series matches
$ResourceLoad = []; //? Clean between optimization process to avoid existing invalid resource
$OptimizationResource = []; //? Clean between optimization process to avoid existing invalid resource

foreach($Resource as $ResourceItem){ // Only if trend series is available
	foreach($LoadTrend[$ResourceItem->ID]["CPU"] as $LoadTrendIndex => $LoadTrendItem)$ResourceLoad[$ResourceItem->ID]["CPU"] = $LoadTrendIndex == 0 ? $LoadTrendItem : ($ResourceLoad[$ResourceItem->ID]["CPU"] == $LoadTrendItem ? $LoadTrendItem : null);
	foreach($LoadTrend[$ResourceItem->ID]["Memory"] as $LoadTrendIndex => $LoadTrendItem)$ResourceLoad[$ResourceItem->ID]["Memory"] = $LoadTrendIndex == 0 ? $LoadTrendItem : ($ResourceLoad[$ResourceItem->ID]["Memory"] == $LoadTrendItem ? $LoadTrendItem : null);
	foreach($LoadTrend[$ResourceItem->ID]["Storage"] as $LoadTrendIndex => $LoadTrendItem)$ResourceLoad[$ResourceItem->ID]["Storage"] = $LoadTrendIndex == 0 ? $LoadTrendItem : ($ResourceLoad[$ResourceItem->ID]["Storage"] == $LoadTrendItem ? $LoadTrendItem : null);

	$OptimizationData["Node"][$ResourceItem->Node]["Resource"][$ResourceItem->ID]["Load"]["Status"] = [
		"CPU" => $ResourceLoad[$ResourceItem->ID]["CPU"]->Title ?? $LoadStatus->Unknown->Title, 
		"Memory" => $ResourceLoad[$ResourceItem->ID]["CPU"]->Title ?? $LoadStatus->Unknown->Title, 
		"Storage" => $ResourceLoad[$ResourceItem->ID]["CPU"]->Title ?? $LoadStatus->Unknown->Title, 
	];
}
#endregion Detect trends if the full series matches

$CPUTrendMatrixColumnHeader = str_repeat("🧠", $PassCount);
$MemoryTrendMatrixColumnHeader = str_repeat("☕", $PassCount);
$StorageTrendMatrixColumnHeader = str_repeat("📁", $PassCount);

$LoadTrendMatrixContent = <<<CONTENT

🎲 Trend matrix
─────────────────────────────────────────────────────────────────────────────────
 Type      💠 Node         ⚙️ ID 🔖 Name           {$CPUTrendMatrixColumnHeader}{$MemoryTrendMatrixColumnHeader}{$StorageTrendMatrixColumnHeader}
─────────────────────────────────────────────────────────────────────────────────

CONTENT;

$RecommendationContent = <<<CONTENT

💡 Recommendation | {$LoadStatus->Normal->Symbol} OK {$LoadStatus->High->Symbol} INCREASE {$LoadStatus->Low->Symbol} DECREASE
─────────────────────────────────────────────────────────────────────────────────────────────────────────────────
    🧠 CPU             ☕ Memory             📁 Storage           Type      💠 Node         ⚙️ ID 🔖 Name
─────────────────────────────────────────────────────────────────────────────────────────────────────────────────

CONTENT;

foreach($Resource as $ResourceItem)if(isset($ResourceLoad[$ResourceItem->ID])){ //* Only if trend is available
	#region Determine recommendation nature
	$Recommendation[$ResourceItem->ID]["CPULimit"] = $Action->Ignore;
	if($ResourceLoad[$ResourceItem->ID]["CPU"] == $LoadStatus->High)$Recommendation[$ResourceItem->ID]["CPULimit"] = $Action->Increase;
	if($ResourceLoad[$ResourceItem->ID]["CPU"] == $LoadStatus->Low)$Recommendation[$ResourceItem->ID]["CPULimit"] = $Action->Decrease;

	if($CPUReverseLogic){ // Reverse CPU logic; do the opposite
		if($Recommendation[$ResourceItem->ID]["CPULimit"] == $Action->Increase){
			$Recommendation[$ResourceItem->ID]["CPULimit"] = $Action->Decrease;
		}
		elseif($Recommendation[$ResourceItem->ID]["CPULimit"] == $Action->Decrease){
			$Recommendation[$ResourceItem->ID]["CPULimit"] = $Action->Increase;
		}else{}
	}

	if($Recommendation[$ResourceItem->ID]["CPULimit"] == $Action->Decrease && $ResourceItem->Configuration->CPULimit == $CPULimitMinimum)$Recommendation[$ResourceItem->ID]["CPULimit"] = $Action->Ignore;
	if($Recommendation[$ResourceItem->ID]["CPULimit"] == $Action->Increase && $ResourceItem->Configuration->CPULimit == $CPULimitMaximum)$Recommendation[$ResourceItem->ID]["CPULimit"] = $Action->Ignore;
	
	$Recommendation[$ResourceItem->ID]["Memory"] = $Action->Ignore;
	if($ResourceLoad[$ResourceItem->ID]["Memory"] == $LoadStatus->High)$Recommendation[$ResourceItem->ID]["Memory"] = $Action->Increase;
	if($ResourceLoad[$ResourceItem->ID]["Memory"] == $LoadStatus->Low)$Recommendation[$ResourceItem->ID]["Memory"] = $Action->Decrease;
	if($Recommendation[$ResourceItem->ID]["Memory"] == $Action->Decrease && $ResourceItem->Memory == $MemoryMinimum)$Recommendation[$ResourceItem->ID]["Memory"] = $Action->Ignore;
	if($Recommendation[$ResourceItem->ID]["Memory"] == $Action->Increase && $ResourceItem->Memory == $MemoryMaximum)$Recommendation[$ResourceItem->ID]["Memory"] = $Action->Ignore;
	
	$Recommendation[$ResourceItem->ID]["Storage"] = $Action->Ignore;
	if($ResourceLoad[$ResourceItem->ID]["Storage"] == $LoadStatus->High)$Recommendation[$ResourceItem->ID]["Storage"] = $Action->Increase;
	if($ResourceLoad[$ResourceItem->ID]["Storage"] == $LoadStatus->Low)$Recommendation[$ResourceItem->ID]["Storage"] = $Action->Decrease;
	if($Recommendation[$ResourceItem->ID]["Storage"] == $Action->Decrease && $ResourceItem->Storage == $StorageMinimum)$Recommendation[$ResourceItem->ID]["Storage"] = $Action->Ignore;
	if($Recommendation[$ResourceItem->ID]["Storage"] == $Action->Increase && $ResourceItem->Storage == $StorageMaximum)$Recommendation[$ResourceItem->ID]["Storage"] = $Action->Ignore;
	#endregion Determine recommendation nature

	#region Determine new resource values
	if(!$ResourceItem->Configuration->CPULimit)$ResourceItem->Configuration->CPULimit = $CPULimitMaximum;
	
	$RecommendationValue[$ResourceItem->ID]["CPULimit"] = $ResourceItem->Configuration->CPULimit;
	if($Recommendation[$ResourceItem->ID]["CPULimit"] == $Action->Increase)$RecommendationValue[$ResourceItem->ID]["CPULimit"] = $ResourceItem->Configuration->CPULimit + ($ResourceItem->Configuration->CPULimit * $CPULimitIncrement / 100);
	if($Recommendation[$ResourceItem->ID]["CPULimit"] == $Action->Decrease)$RecommendationValue[$ResourceItem->ID]["CPULimit"] = $ResourceItem->Configuration->CPULimit - ($ResourceItem->Configuration->CPULimit * $CPULimitDecrement / 100);
	if($RecommendationValue[$ResourceItem->ID]["CPULimit"] > $CPULimitMaximum)$RecommendationValue[$ResourceItem->ID]["CPULimit"] = $CPULimitMaximum;
	if($RecommendationValue[$ResourceItem->ID]["CPULimit"] < $CPULimitMinimum)$RecommendationValue[$ResourceItem->ID]["CPULimit"] = $CPULimitMinimum;
	$RecommendationValue[$ResourceItem->ID]["CPULimit"] = round($RecommendationValue[$ResourceItem->ID]["CPULimit"], 2);
	
	$RecommendationValue[$ResourceItem->ID]["Memory"] = $ResourceItem->Memory;
	if($Recommendation[$ResourceItem->ID]["Memory"] == $Action->Increase)$RecommendationValue[$ResourceItem->ID]["Memory"] = $ResourceItem->Memory + ($ResourceItem->Memory * $MemoryIncrement / 100);
	if($Recommendation[$ResourceItem->ID]["Memory"] == $Action->Decrease)$RecommendationValue[$ResourceItem->ID]["Memory"] = $ResourceItem->Memory - ($ResourceItem->Memory * $MemoryDecrement / 100);
	if($RecommendationValue[$ResourceItem->ID]["Memory"] > $MemoryMaximum)$RecommendationValue[$ResourceItem->ID]["Memory"] = $MemoryMaximum;
	if($RecommendationValue[$ResourceItem->ID]["Memory"] < $MemoryMinimum)$RecommendationValue[$ResourceItem->ID]["Memory"] = $MemoryMinimum;
	$RecommendationValue[$ResourceItem->ID]["Memory"] = round($RecommendationValue[$ResourceItem->ID]["Memory"], 0);
	
	$RecommendationValue[$ResourceItem->ID]["Storage"] = $ResourceItem->Storage;
	if($Recommendation[$ResourceItem->ID]["Storage"] == $Action->Increase)$RecommendationValue[$ResourceItem->ID]["Storage"] = $ResourceItem->Storage + ($ResourceItem->Storage * $StorageIncrement / 100);
	if($Recommendation[$ResourceItem->ID]["Storage"] == $Action->Decrease)$RecommendationValue[$ResourceItem->ID]["Storage"] = $ResourceItem->Storage - ($ResourceItem->Storage * $StorageDecrement / 100);
	if($RecommendationValue[$ResourceItem->ID]["Storage"] > $StorageMaximum)$RecommendationValue[$ResourceItem->ID]["Storage"] = $StorageMaximum;
	if($RecommendationValue[$ResourceItem->ID]["Storage"] < $StorageMinimum)$RecommendationValue[$ResourceItem->ID]["Storage"] = $StorageMinimum;
	$RecommendationValue[$ResourceItem->ID]["Storage"] = round($RecommendationValue[$ResourceItem->ID]["Storage"], 0);
	#endregion Determine new resource values
	
	#region Display values
	$CPUTrendSymbol = "";
	$MemoryTrendSymbol = "";
	$StorageTrendSymbol = "";

	foreach($LoadTrend[$ResourceItem->ID]["CPU"] as $LoadTrendItem)$CPUTrendSymbol .= $LoadTrendItem->Symbol;
	foreach($LoadTrend[$ResourceItem->ID]["Memory"] as $LoadTrendItem)$MemoryTrendSymbol .= $LoadTrendItem->Symbol;
	foreach($LoadTrend[$ResourceItem->ID]["Storage"] as $LoadTrendItem)$StorageTrendSymbol .= $LoadTrendItem->Symbol;

	$ResourceItemTypeFormatted = str_pad($ResourceItem->Type->value, 7, " ", STR_PAD_RIGHT);
	$ResourceItemNodeFormatted = str_pad($ResourceItem->Node, 15, " ", STR_PAD_RIGHT);
	$ResourceItemVMIDFormatted = str_pad($ResourceItem->VMID, 5, " ", STR_PAD_LEFT);
	$ResourceItemNameFormatted = str_pad($ResourceItem->Name, 15, " ", STR_PAD_RIGHT);
	#endregion Display values

	// Show trend matrix for all availablle resources; not only effective; for better understanding of internals
	$LoadTrendMatrixContent .= <<<CONTENT
	{$ResourceItem->Type->Symbol()} {$ResourceItemTypeFormatted} {$ResourceItemNodeFormatted} {$ResourceItemVMIDFormatted} {$ResourceItemNameFormatted} │ {$CPUTrendSymbol}{$MemoryTrendSymbol}{$StorageTrendSymbol}

	CONTENT;

	if( // Process effective resources only
		// true ||
		$Recommendation[$ResourceItem->ID]["CPULimit"] != $Action->Ignore || 
		$Recommendation[$ResourceItem->ID]["Memory"] != $Action->Ignore || 
		$Recommendation[$ResourceItem->ID]["Storage"] != $Action->Ignore
	){
		#region Display values
		$CPULimitRecommendationStatus = str_pad(number_format($ResourceItem->Configuration->CPULimit, 2), 6, " ", STR_PAD_LEFT) . " {$Recommendation[$ResourceItem->ID]["CPULimit"]->Symbol} " . str_pad(number_format($RecommendationValue[$ResourceItem->ID]["CPULimit"], 2), 6, " ", STR_PAD_RIGHT);
		$MemoryRecommendationStatus = str_pad(number_format($ResourceItem->Memory, 0), 7, " ", STR_PAD_LEFT) . " {$Recommendation[$ResourceItem->ID]["Memory"]->Symbol} " . str_pad(number_format($RecommendationValue[$ResourceItem->ID]["Memory"], 0), 7, " ", STR_PAD_RIGHT);
		$StorageRecommendationStatus = str_pad(number_format($ResourceItem->Storage, 0), 9, " ", STR_PAD_LEFT) . " {$Recommendation[$ResourceItem->ID]["Storage"]->Symbol} " . str_pad(number_format($RecommendationValue[$ResourceItem->ID]["Storage"], 0), 9, " ", STR_PAD_RIGHT);
		#endregion Display values
	
		$RecommendationContent .= <<<CONTENT
		{$CPULimitRecommendationStatus} │ {$MemoryRecommendationStatus} │ {$StorageRecommendationStatus} │ {$ResourceItem->Type->Symbol()} {$ResourceItemTypeFormatted} {$ResourceItemNodeFormatted} {$ResourceItemVMIDFormatted} {$ResourceItemNameFormatted}
	
		CONTENT;

		$OptimizationData["Node"][$ResourceItem->Node]["Resource"][$ResourceItem->ID]["Recommendation"] = [
			"CPU" => [
				"Action" => $Recommendation[$ResourceItem->ID]["CPULimit"]->Title, 
				"Value" => $RecommendationValue[$ResourceItem->ID]["CPULimit"], 
				"Applied" => false, 
				"Method" => "Automatic", 
			], 
			"Memory" => [
				"Action" => $Recommendation[$ResourceItem->ID]["Memory"]->Title, 
				"Value" => $RecommendationValue[$ResourceItem->ID]["Memory"], 
				"Applied" => false, 
				"Method" => "Automatic", 
			], 
			"Storage" => [
				"Action" => $Recommendation[$ResourceItem->ID]["Storage"]->Title, 
				"Value" => $RecommendationValue[$ResourceItem->ID]["Storage"], 
				"Applied" => false, 
				"Method" => "Manual", 
			], 
		];

		if( // Further filter because storage optimization is not in effect
			// true ||
			$Recommendation[$ResourceItem->ID]["CPULimit"] != $Action->Ignore || 
			$Recommendation[$ResourceItem->ID]["Memory"] != $Action->Ignore
			// $Recommendation[$ResourceItem->ID]["Storage"] != $Action->Ignore
		){
			$OptimizationResource[$ResourceItem->ID]["Resource"] = $ResourceItem;
			$OptimizationResource[$ResourceItem->ID]["CPULimit"] = $RecommendationValue[$ResourceItem->ID]["CPULimit"];
			$OptimizationResource[$ResourceItem->ID]["Memory"] = $RecommendationValue[$ResourceItem->ID]["Memory"];
			$OptimizationResource[$ResourceItem->ID]["Storage"] = $RecommendationValue[$ResourceItem->ID]["Storage"];
		}
	}
}

$LoadTrendMatrixContent .= <<<CONTENT
─────────────────────────────────────────────────────────────────────────────────

CONTENT;

$RecommendationContent .= <<<CONTENT
─────────────────────────────────────────────────────────────────────────────────────────────────────────────────
* Storage optimization is not automatic

CONTENT;

print $LoadTrendMatrixContent;
print $RecommendationContent;

file_put_contents($JSONFile, json_encode($OptimizationData));

if(!$RecommendationOnly)require __DIR__ . "/optimize.php";
?>