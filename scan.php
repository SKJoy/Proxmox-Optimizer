<?php
// Reset data array per optimization to avoid invalid items
$Resource = [];
$LoadTrend = [];

$PageRowCount = 20;
$ProgressTotal = $OptimizeCount * $PassCount;

$TopTitleLine = <<<CONTENT
────────────────────────────────────────────────────────────────────────────────────────
      Type      💠 Node         ⚙️ ID 🔖 Name              🧠 CPU  ☕ Memory  📁 Storage
────────────────────────────────────────────────────────────────────────────────────────

CONTENT;

$ProxmoxVE = new \S\External\ProxmoxVE(
	new \S\External\ProxmoxVE\API(
		$PVEAPIBaseURL, 
		$PVEAPIUser, 
		$PVEAPIID, 
		$PVEAPIToken, 
		null, 
		null, 
		$PVEAPIConnectionTimeout, 
	), 
);

for($PassCounter = 1; $PassCounter <= $PassCount; $PassCounter++){
	ExitOnCommandFile(); //? Exit immediately if exit command file is found

	#region Generate operation progress bar
	$ProgressPercentile = (int)(((($OptimizeCounter - 1) * $PassCount) + $PassCounter) * 100 / $ProgressTotal);	
	$ProgressBarLength = 29;
	$ProgressBar = mb_str_pad(str_repeat("▓", floor($ProgressPercentile * $ProgressBarLength / 100)), $ProgressBarLength, "░", STR_PAD_RIGHT, "UTF-8");
	$ProgressCaption = mb_str_pad("{$PassCounter}/{$PassCount}/{$OptimizeCounter}/{$OptimizeCount} {$ProgressBar} {$ProgressPercentile}%", 48, " ", STR_PAD_RIGHT, "UTF-8");
	#endregion Generate operation progress bar

	print <<<CONTENT

	⏳ Pass {$ProgressCaption} | {$LoadStatus->Normal->Symbol} OK {$LoadStatus->High->Symbol} HIGH {$LoadStatus->Low->Symbol} LOW {$LoadStatus->Alert->Symbol} ALERT
	{$TopTitleLine}
	CONTENT;

	$ResourceItemCounter = 0;
	$ProxmoxVEResource = $ProxmoxVE->Resource();

	if(!$ProxmoxVEResource){ //! No Proxmox VE resource found
		sleep($PassInterval); //? Put an interval before next optimization process
		break; // Abort current pass if no resource found
	}
	
	foreach($ProxmoxVEResource as $ResourceItem)if(
		(
			$ResourceItem->Type == \S\External\ProxmoxVE\RESOURCE_TYPE::LXC ||
			$ResourceItem->Type == \S\External\ProxmoxVE\RESOURCE_TYPE::QEMU
		) &&
		$ResourceItem->Status == \S\External\ProxmoxVE\RESOURCE_STATUS::RUNNING &&
		!$ResourceItem->LockName // Resource is not locked in PVE
	){
		$ResourceItemCounter++;
		$Resource[$ResourceItem->ID] = $ResourceItem; // Store latest resource for API call later
	
		#region Detect loads		
		$CPULoadStatus = $LoadStatus->Normal;
		$CPULoadPercentille = round($ResourceItem->CPULoad, 2);
		if($CPULoadPercentille <= $CPULowThreshold)$CPULoadStatus = $LoadStatus->Low;
		if($CPULoadPercentille >= $CPUHighThreshold)$CPULoadStatus = $LoadStatus->High;
		
		$MemoryLoadStatus = $LoadStatus->Normal;
		$MemoryLoadPercentille = round($ResourceItem->MemoryConsumption / $ResourceItem->Memory * 100, 2);
		if($MemoryLoadPercentille <= $MemoryLowThreshold)$MemoryLoadStatus = $LoadStatus->Low;
		if($MemoryLoadPercentille >= $MemoryHighThreshold)$MemoryLoadStatus = $LoadStatus->High;
		
		$StorageLoadStatus = $LoadStatus->Normal;
		$StorageLoadPercentille = round($ResourceItem->StorageConsumption / $ResourceItem->Storage * 100, 2);
		if($StorageLoadPercentille <= $StorageLowThreshold)$StorageLoadStatus = $LoadStatus->Low;
		if($StorageLoadPercentille >= $StorageHighThreshold)$StorageLoadStatus = $LoadStatus->High;
		#endregion Detect loads

		#region Display items
		$TitleLine = $ResourceItemCounter % $PageRowCount == 0 ? $TopTitleLine : null;
	
		$ResourceItemTypeFormatted = str_pad($ResourceItem->Type->value, 7, " ", STR_PAD_RIGHT);
		$NodeFormatted = str_pad($ResourceItem->Node, 15, " ", STR_PAD_RIGHT);
		$NameFormatted = str_pad($ResourceItem->Name, 15, " ", STR_PAD_RIGHT);
		$VMIDFormatted = str_pad($ResourceItem->VMID, 5, " ", STR_PAD_LEFT);
	
		$CPULoadFormatted = str_pad(number_format($CPULoadPercentille, 2, "."), 6, " ", STR_PAD_LEFT);
		$MemoryLoadFormatted = str_pad(number_format($MemoryLoadPercentille, 2, "."), 6, " ", STR_PAD_LEFT);
		$StorageLoadFormatted = str_pad(number_format($StorageLoadPercentille, 2, "."), 6, " ", STR_PAD_LEFT);
	
		$RourceLoadStatus = 
			$CPULoadStatus == $LoadStatus->Normal && 
			$MemoryLoadStatus == $LoadStatus->Normal && 
			$StorageLoadStatus == $LoadStatus->Normal 
		? $LoadStatus->OK : $LoadStatus->Alert;
		#endregion Display items
		
		print <<<CONTENT
		{$RourceLoadStatus->Symbol} │ {$ResourceItem->Type->Symbol()} {$ResourceItemTypeFormatted} {$NodeFormatted} {$VMIDFormatted} {$NameFormatted} │ {$CPULoadFormatted}%{$CPULoadStatus->Symbol}  {$MemoryLoadFormatted}%{$MemoryLoadStatus->Symbol}   {$StorageLoadFormatted}%{$StorageLoadStatus->Symbol}
		{$TitleLine}
		CONTENT;
		
		#region Keep history of trend
		$LoadTrend[$ResourceItem->ID]["CPU"][] = $CPULoadStatus;
		$LoadTrend[$ResourceItem->ID]["Memory"][] = $MemoryLoadStatus;
		$LoadTrend[$ResourceItem->ID]["Storage"][] = $StorageLoadStatus;

		$OptimizationPassData[$ResourceItem->ID]["Data"]["CPU"][$PassCounter] = ["Value" => $CPULoadPercentille, "Status" => $CPULoadStatus->Title, ];
		$OptimizationPassData[$ResourceItem->ID]["Data"]["Memory"][$PassCounter] = ["Value" => $MemoryLoadPercentille, "Status" => $MemoryLoadStatus->Title, ];
		$OptimizationPassData[$ResourceItem->ID]["Data"]["Storage"][$PassCounter] = ["Value" => $StorageLoadPercentille, "Status" => $StorageLoadStatus->Title, ];
		
		$OptimizationPassData[$ResourceItem->ID]["Trend"]["CPU"][$PassCounter] = $CPULoadStatus->Title;
		$OptimizationPassData[$ResourceItem->ID]["Trend"]["Memory"][$PassCounter] = $MemoryLoadStatus->Title;
		$OptimizationPassData[$ResourceItem->ID]["Trend"]["Storage"][$PassCounter] = $StorageLoadStatus->Title;
		#endregion Keep history of trend
	}
	
	print <<<CONTENT
	────────────────────────────────────────────────────────────────────────────────────────
	
	CONTENT;

	if($PassCounter < $PassCount)sleep($PassInterval); //? Put an interval before next scan
}

foreach($Resource as $ResourceItem){
	$OptimizationData["Node"][$ResourceItem->Node]["Resource"][$ResourceItem->ID]["ID"] = $ResourceItem->ID;
	$OptimizationData["Node"][$ResourceItem->Node]["Resource"][$ResourceItem->ID]["Type"] = $ResourceItem->Type->value;
	$OptimizationData["Node"][$ResourceItem->Node]["Resource"][$ResourceItem->ID]["VMID"] = $ResourceItem->VMID;
	$OptimizationData["Node"][$ResourceItem->Node]["Resource"][$ResourceItem->ID]["Name"] = $ResourceItem->Name;

	$OptimizationData["Node"][$ResourceItem->Node]["Resource"][$ResourceItem->ID]["Load"] = [
		"Pass" => $OptimizationPassData[$ResourceItem->ID]["Data"], 
		"Trend" => $OptimizationPassData[$ResourceItem->ID]["Trend"], 
	];
}

file_put_contents($JSONFile, json_encode($OptimizationData));

//* Proceed to next step only if all the passes completed
if($PassCounter > $PassCount)require __DIR__ . "/recommend.php";
?>