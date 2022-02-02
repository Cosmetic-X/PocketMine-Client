<?php
/*
 * Copyright (c) Jan Sohn
 * All rights reserved.
 * This plugin is under GPL license
 */
declare(strict_types=1);
namespace cosmeticx;
use cosmeticx\cosmetics\Cosmetic;
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

	/**
	 * Function resetPublicCosmetics
	 * @return void
	 */
	function resetPublicCosmetics(): void{
		unset($this->publicCosmetics);
		$this->publicCosmetics = [];
	}

	/**
	 * Function resetCosmetics
	 * @return void
	 */
	function resetSlotCosmetics(): void{
		unset($this->slotCosmetics);
		$this->slotCosmetics = [];
	}

	/**
	 * Function registerCosmetic
	 * @param string $name
	 * @param string $display_name
	 * @param string $id
	 * @return void
	 */
	function registerPublicCosmetics(string $name, string $display_name, string $id): void{
		$this->publicCosmetics[$id] = new Cosmetic($name, $display_name, $id, Cosmetic::PUBLIC);
	}

	/**
	 * Function registerSlotCosmetic
	 * @param string $name
	 * @param string $display_name
	 * @param string $id
	 * @return void
	 */
	function registerSlotCosmetic(string $name, string $display_name, string $id): void{
		$this->slotCosmetics[$id] = new Cosmetic($name, $display_name, $id, Cosmetic::SLOT);
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
