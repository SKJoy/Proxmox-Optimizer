<?php
if($WebHookURL){
    exec(sprintf(
        "curl -X POST %s -H \"Content-Type: application/json\" -H \"Authorization: Bearer {$WebHookBearerAuthorizationKey}\" --connect-timeout 3 -d %s > /dev/null 2>&1 &",
        escapeshellarg($WebHookURL),
        escapeshellarg(json_encode([
            "Identity" => $WebHookIdentity, 
            "Optimization" => $OptimizationData, 
        ]))
    ));

    print "✅ 🔗 WebHook executed: {$WebHookURL}";
}
else{
    print "⭕ 🔗 No WebHook defined";
}

print PHP_EOL;
?>