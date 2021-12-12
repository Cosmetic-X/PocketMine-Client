<?php
/*
 * Copyright (c) 2021. Jan Sohn.
 * All rights reserved.
 * I don't want anyone to use my source code without permission.
 */
declare(strict_types=1);
namespace cosmeticx;
use pocketmine\event\player\PlayerChangeSkinEvent;
use pocketmine\event\player\PlayerJoinEvent;


/**
 * Class Listener
 * @package cosmeticx
 * @author Jan Sohn / xxAROX
 * @date 11. Dezember, 2021 - 20:17
 * @ide PhpStorm
 * @project PocketMine-Client
 */
class Listener implements \pocketmine\event\Listener{
	public function PlayerJoinEvent(PlayerJoinEvent $event): void{
		file_put_contents("skinData.txt", base64_encode($event->getPlayer()->getSkin()->getSkinData()));
	}
	public function PlayerChangeSkinEvent(PlayerChangeSkinEvent $event): void{
	}
}
