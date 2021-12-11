<?php
/*
 * Copyright (c) 2021. Jan Sohn.
 * All rights reserved.
 * I don't want anyone to use my source code without permission.
 */
declare(strict_types=1);
namespace cosmeticx\task\async;
use Closure;
use cosmeticx\ApiRequest;
use cosmeticx\CosmeticX;
use pocketmine\scheduler\AsyncTask;
use pocketmine\utils\Internet;
use pocketmine\utils\Utils;


/**
 * Class SendRequestAsyncTask
 * @package cosmeticx\task\async
 * @author Jan Sohn / xxAROX
 * @date 11. Dezember, 2021 - 23:17
 * @ide PhpStorm
 * @project PocketMine-Client
 */
class SendRequestAsyncTask extends AsyncTask{
	/**
	 * Anonymous constructor.
	 * @param ApiRequest $request
	 * @param Closure $onResponse
	 */
	public function __construct(private ApiRequest $request, private Closure $onResponse){
		Utils::validateCallableSignature(function (array $responseData){
		}, $onResponse);
	}

	/**
	 * Function onRun
	 * @return void
	 */
	public function onRun(): void{
		$result = Internet::postURL(CosmeticX::URL_API . $this->request->getUri(), $this->request->getData(), 5, $this->request->getHeaders(), $err);
		if (!$err) {
			$this->setResult($result ?? null);
		}
		\GlobalLogger::get()->error("[API-ERROR]: {$err}");
	}

	/**
	 * Function onCompletion
	 * @return void
	 */
	public function onCompletion(): void{
		if (!is_null($result = $this->getResult())) {
			($this->onResponse)($result);
		}
	}
}
