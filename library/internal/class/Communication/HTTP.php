<?php
namespace S\Communication;

require_once __DIR__ . "/../Exception.php";

enum HTTP_METHOD:string{
	case HEAD = "HEAD";
	case GET = "GET";
	case POST = "POST";
	case PUT = "PUT";
	case PATCH = "PATCH";
	case DELETE = "DELETE";
}

enum HTTP_AUTHORIZATION:string{
	case API_KEY = "API_KEY";
	case AUTHORIZATION = "AUTHORIZATION";
	case BEARER = "BEARER";
}

class HTTP extends \S\Base{
	#region Public constant
	public const MIME_TYPE_APPLICATION_JSON = "application/json";
	
	public const USER_AGENT_GENERIC = "sPHP/1.0";
	public const USER_AGENT_WINDOWS_11_GOOGLE_CHROME = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/XX.0.0.0 Safari/537.36";
	#endregion Public constant

	protected const STATUS_MESSAGE_BY_CODE = [
		200 => "OK", 
		201 => "Created", 
		202 => "Accepted", 
		203 => "Non authoritative information", 
		204 => "No content", 
		205 => "Reset content", 
		206 => "Partial content", 
		207 => "Multi status", 
		208 => "Already reported", 
		226 => "IM used", 
		300 => "Multiple choices", 
		301 => "Moved parmanently", 
		302 => "Found", 
		303 => "See other", 
		304 => "Not modified", 
		305 => "Use proxy", 
		306 => "Switch proxy", 
		307 => "Temporary redirect", 
		308 => "Permanent redirect", 
		400 => "Bad request", 
		401 => "Unauthorized", 
		402 => "Payment required", 
		403 => "Forbidden", 
		404 => "Not found", 
		405 => "Method not allowed", 
		406 => "Not acceptable", 
		407 => "Proxy authentication required", 
		408 => "Request timeout", 
		409 => "Conflict", 
		410 => "Gone", 
		411 => "Length required", 
		412 => "Precondition failed", 
		413 => "Payload too large", 
		414 => "URI too long", 
		415 => "Unsupported media type", 
		416 => "Range not satisfiable", 
		417 => "Expectation failed", 
		418 => "I am a teapot", 
		421 => "Misdirected request", 
		422 => "Unprocessable content", 
		423 => "Locked", 
		424 => "Failed dependency", 
		425 => "Too early", 
		426 => "Upgrade required", 
		428 => "Precondition required", 
		429 => "Too many requests", 
		431 => "Request header fields too large", 
		451 => "Unavailable for legal reasons", 
		500 => "Internal server error", 
		501 => "Not implemented", 
		502 => "Bad gateway", 
		503 => "Service unavailable", 
		504 => "Gateway timeout", 
		505 => "HTTP version ot supported", 
		506 => "Variant also negotiates", 
		507 => "Insufficient storage", 
		508 => "Loop detected", 
		510 => "Not extended", 
		511 => "Network authentication required", 
	];

	#region Internal
	private ?\CurlHandle $ReusablecURLObject = null; //* Cannot set object here; set it inside constructor
	#endregion Internal

	public function __construct(
		?string $URL = null, 
		?bool $ReuseConnection = true, // Reuse existing connection for faster response
		null|string|array|object $Data = null, // OBJECT = JSON; ARRAY = POST variable; STRING = Raw data
		?HTTP_AUTHORIZATION $Authorization = null, 
		?string $AuthorizationToken = null, 
		?bool $ExpectJSONResponse = false, 
		?array $Header = [], 
		?HTTP_METHOD $Method = HTTP_METHOD::GET, 
		?string $User = null, 
		?string $Password = null, 
		?int $ConnectionTimeout = 300, 
		?string $Referer = null, 
		?string $UserAgent = self::USER_AGENT_GENERIC, 
		?string $COOKIEFile = null, // File to send cookies from and save cookies into
		?array $COOKIE = [], // Manually send cookies; associative array; ["Name" => "Value", ...]
		?string $ResponseFile = null, // Save HTTP response to file; does not include header
	){
		#region Validate argument
		if(is_null($ReuseConnection))$ReuseConnection = true;
		if(is_null($ExpectJSONResponse))$ExpectJSONResponse = false;
		if(is_null($Header))$Header = [];
		if(is_null($Method))$Method = HTTP_METHOD::GET;
		if(is_null($ConnectionTimeout))$ConnectionTimeout = 300;
		if(is_null($COOKIE))$COOKIE = [];
		#endregion Validate argument

		#region Additional property
		$Error = null;
		$Request = null;
		$Response = null;
		$Debug = null;
		#endregion Additional property

		#region Initialize object
		// $this->_InstanceLimit = 1; // Limit instantiation

		//? Convert all arguments and local variables above this point into properties
		parent::__construct(get_defined_vars());

		//* Properties that cannot be changed outside this class
		$this->_ReadOnlyProperty = "Error, Request, Response, Debug";
		
		#region Properties that get reset by changing other properties
		$this->_DependentProperty[] = ["ImpactedBy" => "URL, Method, Data, Header, ExpectJSONResponse, UserAgent", "Dependent" => "Error, Request, Response, Debug"];
		#endregion Properties that get reset by changing other properties
		#endregion Initialize object

		#region Custom
		//* Set $Result variable with the value to return; default = TRUE

		$this->ReusablecURLObject = curl_init(); // Use a persistent cURL object for faster multiple operation
		
		$Result = true;

		#endregion Custom
		
		return $Result;
	}

	public function __destruct(){
		curl_close($this->ReusablecURLObject);

		$Result = true;

		return $Result;
	}

	public function Call(
		?string $URL = null, 
		?bool $ReuseConnection = null, // Reuse existing connection for faster response
		null|string|array|object $Data = null, // OBJECT = JSON; ARRAY = POST variable; STRING = Raw data
		?HTTP_AUTHORIZATION $Authorization = null, 
		?string $AuthorizationToken = null, 
		?bool $ExpectJSONResponse = null, 
		?array $Header = null, 
		?HTTP_METHOD $Method = null, 
		?string $User = null, 
		?string $Password = null, 
		?int $ConnectionTimeout = null, 
		?string $Referer = null, 
		?string $UserAgent = null, 
		?string $COOKIEFile = null, // File to send cookies from and save cookies into
		?array $COOKIE = null, // Manually send cookies; associative array; ["Name" => "Value", ...]
		?string $ResponseFile = null, // Save HTTP response to file; does not include header
	):object{
		#region Set NULL arguments to existing property values
		if(is_null($URL))$URL = $this->_Property->URL;
		if(is_null($ReuseConnection))$ReuseConnection = $this->_Property->ReuseConnection;
		if(is_null($Data))$Data = $this->_Property->Data;
		if(is_null($Authorization))$Authorization = $this->_Property->Authorization;
		if(is_null($AuthorizationToken))$AuthorizationToken = $this->_Property->AuthorizationToken;
		if(is_null($ExpectJSONResponse))$ExpectJSONResponse = $this->_Property->ExpectJSONResponse;
		if(is_null($Header))$Header = $this->_Property->Header;
		if(is_null($Method))$Method = $this->_Property->Method;
		if(is_null($User))$User = $this->_Property->User;
		if(is_null($Password))$Password = $this->_Property->Password;
		if(is_null($ConnectionTimeout))$ConnectionTimeout = $this->_Property->ConnectionTimeout;
		if(is_null($Referer))$Referer = $this->_Property->Referer;
		if(is_null($UserAgent))$UserAgent = $this->_Property->UserAgent;
		if(is_null($COOKIEFile))$COOKIEFile = $this->_Property->COOKIEFile;
		if(is_null($COOKIE))$COOKIE = $this->_Property->COOKIE;
		if(is_null($ResponseFile))$ResponseFile = $this->_Property->ResponseFile;
		#endregion Set NULL arguments to existing property values

		//! Use $Header until $EffectiveHeader is set later

		if(is_object($Data)){ // Object data
			$Data = json_encode($Data); //? Convert object to JSON
			$Header["CONTENT-TYPE"] = self::MIME_TYPE_APPLICATION_JSON; //? Set appropriate request header
		}

		#region Create authorization header
		if($Authorization == HTTP_AUTHORIZATION::API_KEY)$Header["APIKEY"] = trim($AuthorizationToken);
		if($Authorization == HTTP_AUTHORIZATION::AUTHORIZATION)$Header["AUTHORIZATION"] = trim($AuthorizationToken);
		if($Authorization == HTTP_AUTHORIZATION::BEARER)$Header["AUTHORIZATION"] = "Bearer " . trim($AuthorizationToken);
		#endregion Create authorization header

		if(is_null($ExpectJSONResponse))$ExpectJSONResponse = false;
		if($ExpectJSONResponse)$Header["ACCEPT"] = self::MIME_TYPE_APPLICATION_JSON;

		//? Transform argument header to sanitize values with UPPERCASE keys
		//* Use $EffectiveHeader this point forward
		foreach($Header ?? [] as $HeaderKey => $HeaderValue)$EffectiveHeader[strtoupper(trim($HeaderKey))] = trim($HeaderValue);
		
		#region Automatically determine HTTP method to use
		if(is_null($Method))$Method = HTTP_METHOD::GET;
		$HTTPPOSTMethod = [HTTP_METHOD::POST, HTTP_METHOD::PUT, HTTP_METHOD::PATCH, ];
		if(!in_array($Method, $HTTPPOSTMethod) && !is_null($Data))$Method = HTTP_METHOD::POST;

		if(!isset($EffectiveHeader["CONTENT-TYPE"])){
			// if($Method == HTTP_METHOD::POST)$EffectiveHeader["CONTENT-TYPE"] = "multipart/form-data";

			if($Method == HTTP_METHOD::PUT){
				$EffectiveHeader["CONTENT-TYPE"] = "application/x-www-form-urlencoded";
				$Data = http_build_query($Data);
			}
		}
		#endregion Automatically determine HTTP method to use

		//? Generate manual COOKIE string pieces
		foreach($COOKIE ?? [] as $COOKIEKey => $COOKIEValue)$COOKIEString[] = urlencode(trim($COOKIEKey)) . "=" . urlencode(trim($COOKIEValue));

		#region Generate request headers
		//* Do not manipulate any request header after this point
		$RequestHeader = [];
		foreach($EffectiveHeader ?? [] as $HeaderKey => $HeaderValue)$RequestHeader[] = "{$HeaderKey}: {$HeaderValue}";
		#endregion Generate request headers
		
		$cURL = $ReuseConnection ? $this->ReusablecURLObject : curl_init(); // Determine to reuse existing connection or create new
		$ResponseHeader = []; //* Array to store response headers

		curl_setopt_array($cURL, [ // Filter out NULL values from cURL request
			CURLOPT_URL => $URL, 
			CURLOPT_CUSTOMREQUEST => $Method->value, 
			CURLOPT_HTTPHEADER => $RequestHeader, 
			CURLOPT_CONNECTTIMEOUT => $ConnectionTimeout, 
			CURLOPT_USERPWD => $User ? "{$User}:{$Password}" : null, 
			CURLOPT_POSTFIELDS => $Data, 
			CURLOPT_REFERER => $Referer, 
			CURLOPT_USERAGENT => $UserAgent, 
			CURLOPT_COOKIEJAR => $COOKIEFile, // Send existing from file
			CURLOPT_COOKIEFILE => $COOKIEFile, // Save received into file
			CURLOPT_COOKIE => implode("; ", $COOKIEString ?? []), 
			CURLOPT_HEADER => false, // Do not inlcude header to response data; we are dealing it the other way
			CURLOPT_ENCODING => "", // Enable automatic transfer compression
			CURLOPT_FOLLOWLOCATION => true, 
			CURLOPT_RETURNTRANSFER => true, 
			CURLOPT_SSL_VERIFYPEER => false, 
			// CURLOPT_SSL_VERIFYHOST => false, 
			// CURLOPT_SSL_VERIFYSTATUS => false, 
			CURLOPT_HEADERFUNCTION => function($cURL, $Header) use (&$ResponseHeader){ //? Extract response header to local variable
				$Part = explode(":", $Header);
				$Key = strtoupper($Part[0]);

				$Value = trim($Part[1] ?? "");
				if($Value)$ResponseHeader[$Key] = $Value;

				return strlen($Header);
			}, 
		]);

		$cURLExecutionTimeBegin = microtime(true);
		$Response = curl_exec($cURL); //? Execute cURL request and get response
		$cURLExecutionDuration = microtime(true) - $cURLExecutionTimeBegin; // Detect cURL execution duration

		// Get HTTP response status
		$ResponseStatusCode = curl_getinfo($cURL, CURLINFO_HTTP_CODE);
		$ResponseStatusMessage = self::STATUS_MESSAGE_BY_CODE[$ResponseStatusCode] ?? "UNKNOWN";

		//* Detect connection error
		$cURLErrorCode = curl_errno($cURL);
		$cURLErrorMessage = curl_error($cURL);

		if(!$ReuseConnection)curl_close($cURL); //* Close cURL connection if required

		//? Determine generic error
		if($cURLErrorCode){ //! Communication error
			$ErrorCode = $cURLErrorCode;
			$ErrorMessage = $cURLErrorMessage;
		}
		elseif($ResponseStatusCode < 200 || $ResponseStatusCode > 399){ //! HTTP error
			$ErrorCode = $ResponseStatusCode;
			$ErrorMessage = $ResponseStatusMessage;
		}
		else{ //* No error
			$ErrorCode = 0;
			$ErrorMessage = null;
		}

		if($ExpectJSONResponse){ //? Try decoding JSON data if expected
			$EffectiveResponseData = json_decode($Response);
			if(is_null($EffectiveResponseData))$EffectiveResponseData = $Response; //! Fallback to raw response data
		}
		else{
			$EffectiveResponseData = $Response;
		}		

		if($ResponseFile)file_put_contents($ResponseFile, $EffectiveResponseData); //? Save response data to file; does not include response header

		#region Set property values
		$this->_Property->Error = new \S\Exception($ErrorCode, $ErrorMessage);

		$this->_Property->Request = (object)[
			// "URL" => $URL, 
			"Method" => $Method, 
			"Header" => $RequestHeader, 
			// "Data" => $Data, 
		];

		$this->_Property->Response = (object)[
			"Status" => (object)[
				"Code" => $ResponseStatusCode, 
				"Message" => $ResponseStatusMessage, 
			], 
			"Header" => $ResponseHeader, 
			"Data" => $EffectiveResponseData, 
		];

		$this->_Property->Debug = (object)[
			"Duration" => $cURLExecutionDuration, 
		];
		#endregion Set property values

		$Result = (object)[ // Return request information
			"Error" => $this->_Property->Error, 
			"Request" => $this->_Property->Request, 
			"Response" => $this->_Property->Response, 
			"Debug" => $this->_Property->Debug, 
		];

		return $Result;
	}
}

// $HTTP = new \S\Communication\HTTP();

// var_dump(
// 	$HTTP->Call("https://brixly.uk/"), 
// );

// $C = curl_init(); curl_setopt_array($C, [
// 	CURLOPT_URL => "https://brixly.uk/", 
// 	CURLOPT_RETURNTRANSFER => true, 
// 	CURLOPT_SSL_VERIFYPEER => false, 
// ]); var_dump(curl_exec($C), curl_error($C)); curl_close($C);

// die("DEBUG: " . __FILE__ . ":" . __LINE__);
?>