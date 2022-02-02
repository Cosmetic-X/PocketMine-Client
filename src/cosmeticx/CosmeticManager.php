<?php
/*
 * Copyright (c) Jan Sohn
 * All rights reserved.
 * This plugin is under GPL license
 */
declare(strict_types=1);
namespace cosmeticx;
use cosmeticx\cosmetics\Cosmetic;
use Frago9876543210\EasyForms\elements\Image;
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

	/** @var Skin[] */
	public array $legacy = [];
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
	 * @param null|Image $image
	 * @return void
	 */
	function registerPublicCosmetics(string $name, string $display_name, string $id, ?Image $image = null): void{
		$this->publicCosmetics[] = new Cosmetic($name, $display_name, $id, $image, Cosmetic::PUBLIC);
	}

	/**
	 * Function registerSlotCosmetic
	 * @param string $name
	 * @param string $display_name
	 * @param string $id
	 * @param null|Image $image
	 * @return void
	 */
	function registerSlotCosmetic(string $name, string $display_name, string $id, ?Image $image = null): void{
		$this->slotCosmetics[] = new Cosmetic($name, $display_name, $id, $image, Cosmetic::SLOT);
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
