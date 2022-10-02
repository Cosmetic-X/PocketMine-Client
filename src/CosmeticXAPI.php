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
use cosmeticx\generic\presence\DiscordRichPresence;
use cosmeticx\utils\Utils;
use DaveRandom\CallbackValidator\CallbackType;
use DaveRandom\CallbackValidator\ParameterType;
use DaveRandom\CallbackValidator\ReturnType;
use pocketmine\entity\Skin;


/**
 * Class CosmeticXAPI
 * @package cosmeticx
 * @author Jan Sohn / xxAROX
 * @date 27. March, 2022 - 22:17
 * @ide PhpStorm
 * @project PocketMine-Client
 */
class CosmeticXAPI{
	/**
	 * Function setPresence
	 * @param DiscordRichPresence $presence
	 * @return void
	 */
	public static function setPresence(DiscordRichPresence $presence): void{
		if (CosmeticX::$ENABLE_RICH_PRESENCE) {
			CosmeticX::sendRequest(new ApiRequest(ApiRequest::$URI_USER_RPC_PRESENCE, $presence->jsonSerialize(), true), function (array $responseData): void{
				if (isset($responseData["error"])) {
					CosmeticX::getInstance()->getLogger()->error("Error while setting presence: " . $responseData["error"]);
				}
			});
		}
	}

	/**
	 * Function getSkin
	 * @param string $xuid
	 * @param Closure $function (Skin $skin): void
	 * @return void
	 */
	public static function getSkin(string $xuid, Closure $function): void{
		CosmeticX::sendRequest(new ApiRequest(ApiRequest::$URI_USER_SKIN, ["xuid" => $xuid]), function (array $data) use ($function): void{
			if (isset($responseData["error"])) {
				CosmeticX::getInstance()->getLogger()->error("Error while getting skin: " . $responseData["error"]);
				return;
			}
			\pocketmine\utils\Utils::validateCallableSignature(new CallbackType(new ReturnType(), new ParameterType("skin", Skin::class)), $function);
			$function(new Skin(
				$data["skinId"],
				Utils::decodeSkinData($data["skinData"]),
				Utils::decodeSkinData($data["capeData"]),
				$data["geometryName"],
				$data["geometryData"]
			));
		});
	}
}
