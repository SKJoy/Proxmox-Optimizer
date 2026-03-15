<?php
$VLucasDotEnv->load();

$CPUHighThreshold = $_ENV["PVEO_RESOURCE_CPU_THRESHOLD_HIGH"] ?? 4;
$CPULowThreshold = $_ENV["PVEO_RESOURCE_CPU_THRESHOLD_LOW"] ?? 2;

$MemoryHighThreshold = $_ENV["PVEO_RESOURCE_MEMORY_THRESHOLD_HIGH"] ?? 67;
$MemoryLowThreshold = $_ENV["PVEO_RESOURCE_MEMORY_THRESHOLD_LOW"] ?? 33;

$StorageHighThreshold = $_ENV["PVEO_RESOURCE_STORAGE_THRESHOLD_HIGH"] ?? 67;
$StorageLowThreshold = $_ENV["PVEO_RESOURCE_STORAGE_THRESHOLD_LOW"] ?? 33;

$CPULimitIncrement = $_ENV["PVEO_RESOURCE_CPU_LIMIT_INCREMENT"] ?? 25;
$CPULimitDecrement = $_ENV["PVEO_RESOURCE_CPU_LIMIT_DECREMENT"] ?? 25;

$MemoryIncrement = $_ENV["PVEO_RESOURCE_MEMORY_INCREMENT"] ?? 25;
$MemoryDecrement = $_ENV["PVEO_RESOURCE_MEMORY_DECREMENT"] ?? 25;

$StorageIncrement = $_ENV["PVEO_RESOURCE_STORAGE_INCREMENT"] ?? 25;
$StorageDecrement = $_ENV["PVEO_RESOURCE_STORAGE_DECREMENT"] ?? 25;

$CPULimitMinimum = $_ENV["PVEO_RESOURCE_CPU_LIMIT_MINIMUM"] ?? 0.25;
$CPULimitMaximum = $_ENV["PVEO_RESOURCE_CPU_LIMIT_MAXIMUM"] ?? 2;

$MemoryMinimum = $_ENV["PVEO_RESOURCE_MEMORY_MINIMUM"] ?? 16;
$MemoryMaximum = $_ENV["PVEO_RESOURCE_MEMORY_MAXIMUM"] ?? 4096;

$StorageMinimum = $_ENV["PVEO_RESOURCE_STORAGE_MINIMUM"] ?? 2048;
$StorageMaximum = $_ENV["PVEO_RESOURCE_STORAGE_MAXIMUM"] ?? 32768;

$CPUReverseLogic = strtoupper(trim($_ENV["PVEO_RESOURCE_CPU_REVERSE_LOGIC"] ?? "true")) == "TRUE";

$SWAPMinimum = $_ENV["PVEO_RESOURCE_SWAP_MINIMUM"] ?? 256;
$SWAPMaximum = $_ENV["PVEO_RESOURCE_SWAP_MAXIMUM"] ?? 8192;

$PassCount = $_ENV["PVEO_PASS_COUNT"] ?? 5;
$PassInterval = $_ENV["PVEO_PASS_INTERVAL"] ?? 15;
$OptimizeCount = $_ENV["PVEO_OPTIMIZE_COUNT"] ?? 1;
$RecommendationOnly = strtoupper(trim($_ENV["PVEO_RECOMMENDATION_ONLY"] ?? "true")) == "TRUE";

# Artificial intelligence
$OpenAIAPIBaseURL = $_ENV["PVEO_OPENAI_API_BASE_URL"];
$OpenAIAPIKey = $_ENV["PVEO_OPENAI_API_KEY"];
$OpenAIAPIModel = $_ENV["PVEO_OPENAI_MODEL"];

$Fork = strtoupper(trim($_ENV["PVEO_FORK"] ?? "false")) == "TRUE";

#region SMTP configuration
$SMTPHost = $_ENV["PVEO_SMTP_HOST"] ?? "localhost";
$SMTPPort = $_ENV["PVEO_SMTP_PORT"] ?? 465;
$SMTPSecurity = $_ENV["PVEO_SMTP_SECURITY"] ?? "SSL";
$SMTPUser = $_ENV["PVEO_SMTP_USER"];
$SMTPPassword = $_ENV["PVEO_SMTP_PASSWORD"];
$SMTPFromName = $_ENV["PVEO_SMTP_FROM_NAME"] ?? "";
$SMTPFromEmail = $_ENV["PVEO_SMTP_FROM_EMAIL"] ?? $SMTPUser;
#endregion SMTP configuration

$NotificationEmail = $_ENV["PVEO_NOTIFICATION_EMAIL"] ?? "";
$NotificationEmailDelay = $_ENV["PVEO_NOTIFICATION_EMAIL_DELAY"] ?? 3600;

#region Proxmox VE API
$PVEAPIBaseURL = $_ENV["PVEO_API_BASE_URL"] ?? "";
$PVEAPIUser = $_ENV["PVEO_API_PVE_USER"] ?? "root@pam";
$PVEAPIID = $_ENV["PVEO_API_ID"] ?? "";
$PVEAPIToken = $_ENV["PVEO_API_TOKEN"] ?? "";
$PVEAPIConnectionTimeout = $_ENV["PVEO_API_CONNECTION_TIMEOUT"] ?? 30;
#endregion Proxmox VE API

$WebHookIdentity = $_ENV["PVEO_WEBHOOK_IDENTITY"] ?? null;
$WebHookBearerAuthorizationKey = $_ENV["PVEO_WEBHOOK_BEARER_AUTHORIZATION_KEY"] ?? null;
$WebHookURL = $_ENV["PVEO_WEBHOOK_URL"] ?? "";

$PHPErrorLogSizeLimit = $_ENV["PVEO_PHP_ERROR_LOG_SIZE_LIMIT"] ?? 10;
?>