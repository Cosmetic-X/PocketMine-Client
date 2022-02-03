<?php
/*
 * Copyright (c) Jan Sohn
 * All rights reserved.
 * This plugin is under GPL license
 */

declare(strict_types=1);
namespace cosmeticx\cosmetics;
use cosmeticx\ApiRequest;
use cosmeticx\CosmeticX;
use cosmeticx\utils\Utils;
use JetBrains\PhpStorm\Pure;
use pocketmine\entity\Skin;
use pocketmine\player\Player;
use pocketmine\Server;


/**
 * Class CosmeticSession
 * @package cosmeticx\cosmetics
 * @author Jan Sohn / xxAROX
 * @date 02. Februar, 2022 - 10:53
 * @ide PhpStorm
 * @project PocketMine-Client
 */
final class CosmeticSession{
	protected string $username;
	private Skin $legacySkin;
	protected ?Player $holder = null;

	/** @var string[] */
	private array $activeCosmetics = [];

	/**
	 * CosmeticSession constructor.
	 * @param string $username
	 * @param Skin $legacySkin
	 */
	public function __construct(string $username, Skin $legacySkin){
		$this->username = $username;
		$this->legacySkin = $legacySkin;
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
			CosmeticX::sendRequest(new ApiRequest("/cosmetic/remove", ["id" => $cosmetic->getId(),"skinData" => Utils::encodeSkinData($this->getHolder()->getSkin()->getSkinData())], true), function (array $data): void{
				$skin = $this->getHolder()->getSkin();
				if (!empty($data["geometries"])) {
					//TODO: $data["geometry_data"] = json_encode(array_merge([Utils::json_decode($skin->getGeometryData()), array_merge(...$data["geometries"] ?? [])]));
				}
				$this->getHolder()->setSkin(new Skin($skin->getSkinId(), Utils::decodeSkinData($data["buffer"]), $skin->getCapeData(), $data["geometry_name"] ?? $skin->getGeometryName(), $data["geometry_data"] ?? $skin->getGeometryData()));
				$this->getHolder()->sendSkin();
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
			CosmeticX::sendRequest(new ApiRequest("/cosmetic/remove", [
				"id" => $cosmetic->getId(),
				"active" => $this->activeCosmetics,
				"skinData" => Utils::encodeSkinData($this->legacySkin->getSkinData())
			], true), function (array $data) :void{
				if (!is_null($data["buffer"])) { //FIXME: remove this line if test is done
					$skin = $this->holder->getSkin();
					$this->holder->setSkin(new Skin($skin->getSkinId(), Utils::decodeSkinData($data["buffer"]), $skin->getCapeData(), $data["geometry_name"] ?? $skin->getGeometryName(), $data["geometry_data"] ?? $skin->getGeometryData()));
					$this->holder->sendSkin();
				} //FIXME: remove this line if test is done
			});
		}
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
}
