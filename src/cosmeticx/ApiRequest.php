<?php
/*
 * Copyright (c) 2021. Jan Sohn.
 * All rights reserved.
 * I don't want anyone to use my source code without permission.
 */
declare(strict_types=1);
namespace cosmeticx;
use Closure;
use DaveRandom\CallbackValidator\CallbackType;
use DaveRandom\CallbackValidator\ParameterType;
use DaveRandom\CallbackValidator\ReturnType;
use pocketmine\utils\Utils;
use Stringable;


/**
 * Class BaseRequest
 * @package cosmeticx
 * @author Jan Sohn / xxAROX
 * @date 11. Dezember, 2021 - 19:29
 * @ide PhpStorm
 * @project PocketMine-Client
 */
class ApiRequest{
	private array $headers = [];
	private array $data = [];
	private ?Closure $onResponse;

	/**
	 * ApiRequest constructor.
	 * @param string $token
	 * @param null|Closure $onResponse
	 */
	public function __construct(string $token, Closure $onResponse = null){
		Utils::validateCallableSignature(function (array $responseData){
		}, $onResponse);
		$this->onResponse = $onResponse;
		$this->header("Accept", "application/json");
		$this->header("Content-Type", "application/json");
		$this->header("Token", $token);
	}

	/**
	 * Function response
	 * @param array $responseData
	 * @return null|Closure
	 */
	public function response(array $responseData): ?Closure{
		return ($this->onResponse)($responseData);
	}

	/**
	 * Function header
	 * @param string $key
	 * @param string $value
	 * @return $this
	 */
	public function header(string $key, string $value): self{
		$this->headers[$key] = $value;
		return $this;
	}

	/**
	 * Function data
	 * @param string $key
	 * @param string $value
	 * @return $this
	 */
	public function data(string $key, string $value): self{
		$this->data[$key] = $value;
		return $this;
	}

	/**
	 * Function getHeaders
	 * @return array
	 */
	public function getHeaders(): array{
		return $this->headers;
	}

	/**
	 * Function getData
	 * @return array
	 */
	public function getData(): array{
		return $this->data;
	}
}
