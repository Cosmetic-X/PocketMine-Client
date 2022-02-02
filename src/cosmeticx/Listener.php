<?php
/*
 * Copyright (c) Jan Sohn
 * All rights reserved.
 * This plugin is under GPL license
 */
declare(strict_types=1);
namespace cosmeticx;
use pocketmine\event\player\PlayerChangeSkinEvent;
use pocketmine\event\player\PlayerCreationEvent;
use pocketmine\event\player\PlayerQuitEvent;


/**
 * Class Listener
 * @package cosmeticx
 * @author Jan Sohn / xxAROX
 * @date 11. Dezember, 2021 - 20:17
 * @ide PhpStorm
 * @project PocketMine-Client
 */
class Listener implements \pocketmine\event\Listener{
	/**
	 * Function PlayerCreationEvent
	 * @param PlayerCreationEvent $event
	 * @return void
	 * @priority MONITOR
	 */
	public function PlayerCreationEvent(PlayerCreationEvent $event): void{
		CosmeticManager::getInstance()->legacy[$event->getNetworkSession()->getPlayerInfo()->getUsername()] = $event->getNetworkSession()->getPlayerInfo()->getSkin();
	}

	/**
	 * Function PlayerQuitEvent
	 * @param PlayerQuitEvent $event
	 * @return void
	 * @priority MONITOR
	 */
	public function PlayerQuitEvent(PlayerQuitEvent $event): void{
		unset(CosmeticManager::getInstance()->legacy[$event->getPlayer()->getPlayerInfo()->getUsername()]);
	}

	/**
	 * Function PlayerChangeSkinEvent
	 * @param PlayerChangeSkinEvent $event
	 * @return void
	 * @priority MONITOR
	 */
	public function PlayerChangeSkinEvent(PlayerChangeSkinEvent $event): void{
		$event->cancel();
		$event->getPlayer()->sendMessage("§cSkin changing is not implemented yet.");
	}
}
