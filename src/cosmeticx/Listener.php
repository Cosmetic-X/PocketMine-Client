<?php
/*
 * Copyright (c) Jan Sohn
 * All rights reserved.
 * This plugin is under GPL license
 */
declare(strict_types=1);
namespace cosmeticx;
use cosmeticx\utils\Utils;
use pocketmine\entity\Skin;
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
		CosmeticX::sendRequest(new ApiRequest("/merge-skin-with-cosmetic", ["id" => "0","skinData" => Utils::encodeSkinData($event->getPlayer()->getSkin()->getSkinData())], true), function (array $data) use ($event): void{
			$skin = $event->getPlayer()->getSkin();
			$event->getPlayer()->setSkin(new Skin($skin->getSkinId(), Utils::decodeSkinData($data["buffer"]), $skin->getCapeData(), $data["geometry_name"] ?? $skin->getGeometryName(), $data["geometry_data"] ?? $skin->getGeometryData()));
			$event->getPlayer()->sendSkin();
			$event->getPlayer()->sendMessage("§9Skin changed");
		});
	}
	public function PlayerChangeSkinEvent(PlayerChangeSkinEvent $event): void{
		$event->cancel();
		$event->getPlayer()->sendMessage("§cSkin changing is not implemented yet.");
	}
}
