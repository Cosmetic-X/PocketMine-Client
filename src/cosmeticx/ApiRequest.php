<?php
/*
 * Copyright (c) Jan Sohn
 * All rights reserved.
 * Only people with the explicit permission from Jan Sohn are allowed to modify, share or distribute this code.
 *
 * You are NOT allowed to do any kind of modification to this plugin.
 * You are NOT allowed to share this plugin with others without the explicit permission from Jan Sohn.
 * You are NOT allowed to run this plugin on your server as source code.
 * You MUST acquire this plugin from official sources.
 * You MUST run this plugin on your server as compiled .phar file from our releases.
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
	private array $body;
	private bool $post_method;

	static string $URI_CHECKOUT = "/";
	static string $URI_USER_VERIFY = "/users/verify";
	static string $URI_USER_GET_COSMETICS = "/users/cosmetics/"/*xuid*/;
	static string $URI_USER_ACTIVATE_COSMETIC = "/users/cosmetics/activate";
	static string $URI_USER_DEACTIVATE_COSMETIC = "/users/cosmetics/deactivate";
	static string $URI_USER_RPC_PRESENCE = "/users/rpc";

	/**
	 * ApiRequest constructor.
	 * @param string $uri
	 * @param array $body
	 * @param bool $post_method
	 */
	public function __construct(private string $uri, array $body = [], bool $post_method = false){
		$this->body = $body;
		$this->post_method = $post_method;
	}

	/**
	 * Function getUri
	 * @return string
	 */
	public function getUri(): string{
		return $this->uri;
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
	 * Function body
	 * @param string $key
	 * @param string $value
	 * @return $this
	 */
	public function body(string $key, string $value): self{
		$this->body[$key] = $value;
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
	 * Function getBody
	 * @return array
	 */
	public function getBody(): array{
		return $this->body;
	}

	/**
	 * Function isPostMethod
	 * @return bool
	 */
	public function isPostMethod(): bool{
		return $this->post_method;
	}
}
