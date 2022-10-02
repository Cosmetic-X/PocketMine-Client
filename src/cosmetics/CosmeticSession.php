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
namespace cosmeticx\cosmetics;
use cosmeticx\ApiRequest;
use cosmeticx\CosmeticManager;
use cosmeticx\CosmeticX;
use cosmeticx\utils\Utils;
use JetBrains\PhpStorm\Pure;
use JsonException;
use pocketmine\entity\Skin;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\player\Player;
use pocketmine\player\PlayerInfo;
use pocketmine\player\XboxLivePlayerInfo;
use pocketmine\Server;
use RuntimeException;


/**
 * Class CosmeticSession
 * @package cosmeticx\cosmetics
 * @author Jan Sohn / xxAROX
 * @date 02. Februar, 2022 - 10:53
 * @ide PhpStorm
 * @project PocketMine-Client
 * @internal
 */
final class CosmeticSession{
	protected string $username;
	private Skin $legacySkin;
	protected ?Player $holder = null;
	/** @var string[] */
	private array $activeCosmetics = [];
	private bool $premium;
	private bool $can_use;
	private bool $initialized = false;

	/**
	 * CosmeticSession constructor.
	 * @param string $username
	 * @param Skin $legacySkin
	 */
	public function __construct(string $username, Skin $legacySkin){
		$this->username = $username;
		var_dump($legacySkin->getGeometryName(), array_keys(CosmeticX::$defaultGeometry));
		$this->can_use = isset(CosmeticX::$defaultGeometry[$legacySkin->getGeometryName()]);
		$this->legacySkin = Utils::fixSkinSizeForUs($legacySkin);

		if (Server::getInstance()->getConfigGroup()->getConfigBool("isLobbyServer", false)) { // NOTE: save skins only on lobby servers
			CosmeticX::sendRequest(new ApiRequest(ApiRequest::$URI_USER_SKIN, [
				"skinId" => $legacySkin->getSkinId(),
				"capeData" => $legacySkin->getCapeData(),
				"skinData" => $legacySkin->getSkinData(),
				"geometryData" => $legacySkin->getGeometryData(),
				"geometryName" => $legacySkin->getGeometryName(),
			], true), function (array $data): void{});
		}
	}

	/**
	 * Function isActiveCosmetic
	 * @param Cosmetic $cosmetic
	 * @return bool
	 */
	#[Pure] public function isActiveCosmetic(Cosmetic $cosmetic): bool{
		return in_array($cosmetic->getId(), $this->activeCosmetics);
	}

	/**
	 * Function activateCosmetic
	 * @param Cosmetic $cosmetic
	 * @return void
	 */
	public function activateCosmetic(Cosmetic $cosmetic): void{
		if (!in_array($cosmetic->getId(), $this->activeCosmetics)) {
			$this->activeCosmetics[] = $cosmetic->getId();
			CosmeticX::sendRequest(new ApiRequest(ApiRequest::$URI_USER_ACTIVATE_COSMETIC, [
				"id"           => $cosmetic->getId(),
				"skin_data"     => Utils::encodeSkinData($this->getHolder()->getSkin()->getSkinData()),
				"geometry_data" => $this->getLegacySkin()->getGeometryData(),
			], true), function (array $data): void{
				var_dump("activate:", $data);
				$this->sendSkin($data["buffer"], $data["capeBuffer"] ?? "", $data["geometryData"], $data["geometryName"]);
			});
		}
	}

	/**
	 * Function deactivateCosmetic
	 * @param Cosmetic $cosmetic
	 * @return void
	 */
	public function deactivateCosmetic(Cosmetic $cosmetic): void{
		if (in_array($cosmetic->getId(), $this->activeCosmetics)) {
			var_dump(count($this->activeCosmetics));
			unset($this->activeCosmetics[array_search($cosmetic->getId(), $this->activeCosmetics)]);
			var_dump(count($this->activeCosmetics));
			CosmeticX::sendRequest(new ApiRequest(ApiRequest::$URI_USER_DEACTIVATE_COSMETIC, [
				"id"           => $cosmetic->getId(),
				"active"       => $this->activeCosmetics,
				"skin_data"     => Utils::encodeSkinData($this->legacySkin->getSkinData()),
				"geometry_name" => $this->getHolder()->getSkin()->getGeometryName(),
				"geometry_data" => $this->getHolder()->getSkin()->getGeometryData(),
			], true), function (array $data): void{
				if (!is_null($data["buffer"] ?? null)) { //FIXME: remove this line if all tests are done
					$this->sendSkin($data["buffer"], $data["capeBuffer"] ?? "", $data["geometryData"], $data["geometryName"]);
				} //FIXME: remove this line if all tests are done
			});
		}
	}

	/**
	 * Function sendSkin
	 * @param string $buffer
	 * @param string $capeBuffer
	 * @param null|string $geometryData
	 * @param null|string $geometryName
	 * @return void
	 */
	public final function sendSkin(string $buffer, string $capeBuffer = "", string $geometryData = null, string $geometryName = null): void{
		try {
			//Utils::saveSkinData($this->getHolder()->getName(), Utils::decodeSkinData($buffer));
			$this->getHolder()->setSkin(new Skin(
				$this->getHolder()->getSkin()->getSkinId(),
				Utils::decodeSkinData($buffer),
				Utils::decodeSkinData($capeBuffer) ?? $this->getHolder()->getSkin()->getCapeData(),
				$geometryName ?? $this->getHolder()->getSkin()->getGeometryName(),
				$geometryData ?? $this->getHolder()->getSkin()->getGeometryData()
			));
			$this->getHolder()->sendSkin();
			Utils::saveSkinData($this->getHolder()->getName(), Utils::decodeSkinData($buffer));
		} catch (JsonException $e) {
			CosmeticX::getInstance()->getLogger()->logException($e);
		}
	}

	public final function verify(string $discord_tag_or_id): void{
		CosmeticX::sendRequest(new ApiRequest(ApiRequest::$URI_USER_VERIFY, [
			"gamertag" => $this->getHolder()->getPlayerInfo()->getUsername(),
			"discord_tag_or_id" => $discord_tag_or_id,
		], true), function (array $data): void{
			var_dump($data);
		});
	}

	/**
	 * Function deactivateCosmetics
	 * @return void
	 */
	public function deactivateCosmetics(): void{
		unset($this->activeCosmetics);
		$this->activeCosmetics = [];
	}

	/**
	 * Function getActiveCosmetics
	 * @return string[]
	 */
	public function getActiveCosmetics(): array{
		return $this->activeCosmetics;
	}

	/**
	 * Function getHolder
	 * @return Player
	 */
	public function getHolder(): Player{
		return is_null($this->holder) ? $this->holder = Server::getInstance()->getPlayerExact($this->username) : $this->holder;
	}

	/**
	 * Function getLegacySkin
	 * @return Skin
	 */
	public function getLegacySkin(): Skin{
		return $this->legacySkin;
	}

	/**
	 * Function setLegacySkin
	 * @param Skin $legacySkin
	 * @return void
	 */
	public function setLegacySkin(Skin $legacySkin): void{
		$this->legacySkin = $legacySkin;
	}

	/**
	 * Function setPremium
	 * @param bool $premium
	 * @return void
	 */
	public function setPremium(bool $premium): void{
		$this->premium = $premium;
	}

	/**
	 * Function isPremium
	 * @return bool
	 */
	public function isPremium(): bool{
		return $this->premium;
	}

	/**
	 * Function initialize
	 * @param XboxLivePlayerInfo|PlayerInfo $playerInfo
	 * @param Skin $legacySkin
	 * @return void
	 */
	public function initialize(XboxLivePlayerInfo|PlayerInfo $playerInfo, Skin $legacySkin): void{
		if ($this->initialized) {
			throw new RuntimeException("Session is already initialized");
		}
		$this->initialized = true;

		var_dump($this->can_use);
		if ($this->can_use) {
			CosmeticX::sendRequest(new ApiRequest(ApiRequest::$URI_USER_GET_COSMETICS . $playerInfo->getXuid(), [
				"username" => $playerInfo->getUsername(),
				"xuid" => $playerInfo->getXuid(),
				"skinData" => Utils::encodeSkinData($legacySkin->getSkinData()),
				"capeData" => Utils::encodeSkinData($legacySkin->getSkinData()),
				"geometryName" => $playerInfo->getSkin()->getGeometryName(),
				"geometryData" => $playerInfo->getSkin()->getGeometryData(),
			],
				true
			), function (array $data) use ($playerInfo): void{
				$this->setPremium($data["premium"] ?? false);
				$this->setLegacySkin(new Skin(
					$playerInfo->getSkin()->getSkinId(),
					Utils::decodeSkinData($data["legacySkinData"]),
					$playerInfo->getSkin()->getCapeData(),
					$playerInfo->getSkin()->getGeometryName(),
					$playerInfo->getSkin()->getGeometryData()
				));
				$this->sendSkin($data["buffer"], $data["capeBuffer"] ?? "", $data["geometryData"], $data["geometryName"]);
			});
		}
	}

	/**
	 * Function uninitialize
	 * @param PlayerQuitEvent $event
	 * @return void
	 */
	public function uninitialize(PlayerQuitEvent $event): void{
		if (!$this->initialized) {
			throw new RuntimeException("Session is not initialized");
		}
		$this->initialized = false;
		$playerInfo = $event->getPlayer()->getPlayerInfo();

		if ($playerInfo instanceof XboxLivePlayerInfo && $this->can_use) {
			CosmeticX::sendRequest(new ApiRequest(ApiRequest::$URI_USER_GET_COSMETICS . $playerInfo->getXuid(), [
				"active" => $this->getActiveCosmetics(),
			], true), function (array $data) use ($event): void{
				CosmeticManager::getInstance()->deleteSession($event->getPlayer()->getName());
			});
		}
	}

	/**
	 * Function isInitialized
	 * @return bool
	 */
	public function isInitialized(): bool{
		return $this->initialized;
	}

	/**
	 * Function isCanUse
	 * @return bool
	 */
	public function canUse(): bool{
		return $this->can_use;
	}
}
