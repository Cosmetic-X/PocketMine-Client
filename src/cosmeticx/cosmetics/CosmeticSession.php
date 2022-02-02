<?php
/*
 * Copyright (c) Jan Sohn
 * All rights reserved.
 * This plugin is under GPL license
 */

declare(strict_types=1);
namespace cosmeticx\cosmetics;
use pocketmine\player\Player;


/**
 * Class CosmeticSession
 * @package cosmeticx\cosmetics
 * @author Jan Sohn / xxAROX
 * @date 02. Februar, 2022 - 10:53
 * @ide PhpStorm
 * @project PocketMine-Client
 */
final class CosmeticSession{
	/**
	 * CosmeticSession constructor.
	 * @param Player $holder
	 */
	public function __construct(protected Player $holder){
	}

	/**
	 * Function getHolder
	 * @return Player
	 */
	public function getHolder(): Player{
		return $this->holder;
	}
}
