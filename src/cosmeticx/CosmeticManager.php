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
	private array $cosmetics = [];
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
	 * Function resetCosmetics
	 * @return void
	 */
	function resetCosmetics(): void{
		$this->cosmetics = [];
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
	function registerCosmetic(string $name, string $id): void{
		$this->cosmetics[$name] = new Cosmetic($name, $id);
	}

	/**
	 * Function getLocalCosmetics
	 * @return array
	 */
	function getLocalCosmetics(): array{
		return $this->localCosmetics;
	}

	/**
	 * Function getCosmetics
	 * @return array
	 */
	function getCosmetics(): array{
		return $this->cosmetics;
	}
}
