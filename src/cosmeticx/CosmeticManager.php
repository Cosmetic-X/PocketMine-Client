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
use cosmeticx\cosmetics\Cosmetic;
use cosmeticx\cosmetics\CosmeticSession;
use cosmeticx\utils\Utils;
use Frago9876543210\EasyForms\elements\Image;
use pocketmine\entity\Skin;
use pocketmine\player\PlayerInfo;
use pocketmine\player\XboxLivePlayerInfo;
use pocketmine\utils\SingletonTrait;


/**
 * Class CosmeticManager
 * @package cosmeticx
 * @author Jan Sohn / xxAROX
 * @date 11. Dezember, 2021 - 21:16
 * @ide PhpStorm
 * @project PocketMine-Client
 * @internal
 */
final class CosmeticManager{
	use SingletonTrait;

	/** @var CosmeticSession[] */
	private array $sessions = [];
	/** @var Cosmetic[] */
	private array $serverCosmetics = [], $publicCosmetics = [];
	/** @var Cosmetic[] */
	private array $slotCosmetics = [];

	/**
	 * Function resetPublicCosmetics
	 * @return void
	 * @internal
	 */
	function resetPublicCosmetics(): void{
		unset($this->publicCosmetics);
		$this->publicCosmetics = [];
	}

	/**
	 * Function resetCosmetics
	 * @return void
	 * @internal
	 */
	function resetServerCosmetics(): void{
		unset($this->serverCosmetics);
		$this->serverCosmetics = [];
	}

	/**
	 * Function registerPublicCosmetics
	 * @param string $id
	 * @param string $name
	 * @param string $owner
	 * @param string $display_name
	 * @param string $creator
	 * @param null|Image $image
	 * @return void
	 * @internal
	 */
	function registerCosmetic(string $id, string $name, string $owner, string $display_name, string $creator, ?Image $image = null): void{
		$cosmetic = new Cosmetic($id, $name, $display_name, $creator, $owner, $image);

		if ($owner === "Cosmetic-X") {
			$this->publicCosmetics[] = $cosmetic;
		} else {
			$this->serverCosmetics[] = $cosmetic;
		}
	}

	/**
	 * Function addSession
	 * @param string $username
	 * @param XboxLivePlayerInfo|PlayerInfo $playerInfo
	 * @param Skin $legacySkin
	 * @return CosmeticSession
	 * @internal
	 */
	function addSession(XboxLivePlayerInfo|PlayerInfo $playerInfo, Skin $legacySkin): CosmeticSession{
		if (!isset($this->sessions[mb_strtolower($playerInfo->getUsername())])) {
			$this->sessions[mb_strtolower($playerInfo->getUsername())] = new CosmeticSession($playerInfo->getUsername(), $legacySkin);
		}
		$session = $this->sessions[mb_strtolower($playerInfo->getUsername())];

		if (!$session->isInitialized()) {
			$session->initialize($playerInfo, $legacySkin);
		}
		return $session;
	}

	/**
	 * Function getSession
	 * @param string $username
	 * @return null|CosmeticSession
	 * @internal
	 */
	function getSession(string $username): ?CosmeticSession{
		if (isset($this->sessions[mb_strtolower($username)])) {
			return $this->sessions[mb_strtolower($username)];
		}
		return null;
	}

	/**
	 * Function deleteSession
	 * @param string $username
	 * @return void
	 * @internal
	 */
	function deleteSession(string $username): void{
		if (isset($this->sessions[$username])) {
			unset($this->sessions[$username]);
		}
	}

	/**
	 * Function getPublicCosmetics
	 * @return Cosmetic[]
	 * @internal
	 */
	function getPublicCosmetics(): array{
		return $this->publicCosmetics;
	}

	/**
	 * Function getServerCosmetics
	 * @return array
	 * @internal
	 */
	function getServerCosmetics(): array{
		return $this->serverCosmetics;
	}
}
