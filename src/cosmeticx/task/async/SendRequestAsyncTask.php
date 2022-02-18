<?php
/*
 * Copyright (c) Jan Sohn
 * All rights reserved.
 * This plugin is under GPL license
 */
declare(strict_types=1);
namespace cosmeticx\task\async;
use Closure;
use cosmeticx\api\ApiRequest;
use cosmeticx\CosmeticX;
use GlobalLogger;
use pocketmine\scheduler\AsyncTask;
use pocketmine\utils\AssumptionFailedError;
use pocketmine\utils\InternetException;
use pocketmine\utils\InternetRequestResult;
use pocketmine\utils\Utils;
use Throwable;


/**
 * Class SendRequestAsyncTask
 * @package cosmeticx\task\async
 * @author Jan Sohn / xxAROX
 * @date 11. Dezember, 2021 - 23:17
 * @ide PhpStorm
 * @project PocketMine-Client
 */
class SendRequestAsyncTask extends AsyncTask{
	protected string $url;
	/**
	 * Anonymous constructor.
	 * @param ApiRequest $request
	 * @param Closure $onResponse
	 * @param int $timeout
	 */
	public function __construct(private ApiRequest $request, private Closure $onResponse, private int $timeout = 5){
		$this->url = CosmeticX::$URL_API;
		Utils::validateCallableSignature(function (array $responseData){
		}, $onResponse);
	}

	/**
	 * Function onRun
	 * @return void
	 */
	public function onRun(): void{
		$headers = [];
		foreach ($this->request->getHeaders() as $hk => $hv) {
			$headers[] = $hk . ": " . $hv;
		}
		$ch = curl_init($this->url . $this->request->getUri());

		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, (int) ($this->timeout * 1000));
		curl_setopt($ch, CURLOPT_TIMEOUT_MS, (int) ($this->timeout * 1000));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge(["Content-Type: application/json"], $headers));
		curl_setopt($ch, CURLOPT_HEADER, true);

		if ($this->request->isPostMethod()) {
			curl_setopt($ch, CURLOPT_POST,1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->request->getBody()));
		}

		try{
			$raw = curl_exec($ch);
			if($raw === false){
				throw new InternetException(curl_error($ch));
			}
			if(!is_string($raw)) {
				throw new AssumptionFailedError("curl_exec() should return string|false when CURLOPT_RETURNTRANSFER is set");
			}
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			if (!is_int($httpCode)) {
				throw new AssumptionFailedError("curl_getinfo(CURLINFO_HTTP_CODE) always returns int");
			}
			$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
			$rawHeaders = substr($raw, 0, $headerSize);
			$body = substr($raw, $headerSize);
			$headers = [];
			foreach(explode("\r\n\r\n", $rawHeaders) as $rawHeaderGroup){
				$headerGroup = [];
				foreach(explode("\r\n", $rawHeaderGroup) as $line){
					$nameValue = explode(":", $line, 2);
					if(isset($nameValue[1])){
						$headerGroup[trim(strtolower($nameValue[0]))] = trim($nameValue[1]);
					}
				}
				$headers[] = $headerGroup;
			}
			$this->setResult(new InternetRequestResult($headers, $body, $httpCode));
		} finally {
			curl_close($ch);
		}
	}

	/**
	 * Function onCompletion
	 * @return void
	 */
	public function onCompletion(): void{
		/** @var InternetRequestResult $result */
		if (!is_null($result = $this->getResult())) {
			if ($result->getCode() >= 400 && $result->getCode() < 600) {
				CosmeticX::getInstance()->getLogger()->error("[API-ERROR] [" .$this->request->getUri() . "]: {$result->getBody()}");
				return;
			}
			try {
				$result = json_decode($result->getBody(), true, 512, JSON_THROW_ON_ERROR);
				($this->onResponse)($result);
			} catch (Throwable $e) {
				GlobalLogger::get()->error($this->url . $this->request->getUri());
				GlobalLogger::get()->logException($e);
			}
		} else {
			CosmeticX::getInstance()->getLogger()->error("[API-ERROR] [" . $this->url . $this->request->getUri() . "]: got null, that's not good");
		}
	}
}
