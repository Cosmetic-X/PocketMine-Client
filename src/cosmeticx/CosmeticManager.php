<?php
/*
 * Copyright (c) 2021. Jan Sohn.
 * All rights reserved.
 * I don't want anyone to use my source code without permission.
 */
declare(strict_types=1);
namespace cosmeticx;
use cosmeticx\cosmetics\Cosmetic;
use pocketmine\entity\Skin;
use pocketmine\utils\SingletonTrait;


/**
 * Class CosmeticManager
 * @package cosmeticx
 * @author Jan Sohn / xxAROX
 * @date 11. Dezember, 2021 - 21:16
 * @ide PhpStorm
 * @project PocketMine-Client
 */
class CosmeticManager{
	use SingletonTrait;

	/** @var Cosmetic[] */
	private array $publicCosmetics = [];
	/** @var Cosmetic[] */
	private array $slotCosmetics = [];
	/** @var Skin[] */
	private array $localCosmetics = [];

	/**
	 * Function resetLocalCosmetics
	 * @return void
	 */
	function resetLocalCosmetics(): void{
		$this->localCosmetics = [];
	}

	/**
	 * Function resetPublicCosmetics
	 * @return void
	 */
	function resetPublicCosmetics(): void{
		$this->publicCosmetics = [];
	}

	/**
	 * Function resetCosmetics
	 * @return void
	 */
	function resetSlotCosmetics(): void{
		$this->slotCosmetics = [];
	}

	/**
	 * Function registerLocalCosmetic
	 * @param string $name
	 * @param Skin $skin
	 * @return void
	 */
	function registerLocalCosmetic(string $name, Skin $skin): void{
		$this->localCosmetics[$name] = $skin;
	}

	/**
	 * Function registerCosmetic
	 * @param string $name
	 * @param string $id
	 * @return void
	 */
	function registerPublicCosmetics(string $name, string $id): void{
		$this->publicCosmetics[$name] = new Cosmetic($name, $id, Cosmetic::PUBLIC);
	}

	/**
	 * Function registerSlotCosmetic
	 * @param string $name
	 * @param string $id
	 * @return void
	 */
	function registerSlotCosmetic(string $name, string $id): void{
		$this->slotCosmetics[$name] = new Cosmetic($name, $id, Cosmetic::SLOT);
	}

	/**
	 * Function getLocalCosmetics
	 * @return Skin[]
	 */
	function getLocalCosmetics(): array{
		return $this->localCosmetics;
	}

	/**
	 * Function getPublicCosmetics
	 * @return Cosmetic[]
	 */
	function getPublicCosmetics(): array{
		return $this->publicCosmetics;
	}

	/**
	 * Function getSlotCosmetics
	 * @return array
	 */
	function getSlotCosmetics(): array{
		return $this->slotCosmetics;
	}
}
