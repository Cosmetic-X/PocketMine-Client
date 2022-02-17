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
use pocketmine\event\player\PlayerCreationEvent;
use pocketmine\event\player\PlayerLoginEvent;
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
	 * @priority HIGHEST
	 */
	public function PlayerCreationEvent(PlayerCreationEvent $event): void{
		if (Utils::checkForXuid($playerInfo = $event->getNetworkSession()->getPlayerInfo())) {
			$session = CosmeticManager::getInstance()->addSession($playerInfo->getUsername(), $playerInfo->getSkin());
			CosmeticX::sendRequest(new ApiRequest("/users/cosmetics/" . $playerInfo->getXuid(), [
				"skinData" => Utils::encodeSkinData($playerInfo->getSkin()->getSkinData()),
				"geometry_name" => $playerInfo->getSkin()->getGeometryName(),
				"geometry_data" => $playerInfo->getSkin()->getGeometryData(),
			],
				true
			), function (array $data) use ($event, $playerInfo, $session): void{
				$session->setLegacySkin(new Skin($playerInfo->getSkin()->getSkinId(), Utils::decodeSkinData($data["legacySkinData"]), $playerInfo->getSkin()->getCapeData(), $data["geometry_name"] ?? $playerInfo->getSkin()->getGeometryName(), $data["geometry_data"] ?? $playerInfo->getSkin()->getGeometryData()));
				$session->sendSkin($data["buffer"], $data["geometry_name"], $data["geometry_data"]);
			});
		}
	}

	/**
	 * Function PlayerLoginEvent
	 * @param PlayerLoginEvent $event
	 * @return void
	 * @priority MONITOR
	 */
	public function PlayerLoginEvent(PlayerLoginEvent $event): void{
		if (is_null(CosmeticManager::getInstance()->getSession($event->getPlayer()->getName()))) {
			CosmeticX::getInstance()->getLogger()->emergency("Session is not initialized for " . $event->getPlayer()->getName());
			$event->getPlayer()->kick(CosmeticX::getInstance()->getDescription()->getName() . " - Session is not initialized, this shouldn't happened.. :/", "");
		}
	}

	/**
	 * Function PlayerQuitEvent
	 * @param PlayerQuitEvent $event
	 * @return void
	 * @priority MONITOR
	 */
	public function PlayerQuitEvent(PlayerQuitEvent $event): void{
		if (Utils::checkForXuid($playerInfo = $event->getPlayer()->getPlayerInfo())) {
			$session = CosmeticManager::getInstance()->getSession($event->getPlayer()->getName());
			CosmeticX::sendRequest(new ApiRequest("/users/cosmetics/" . $playerInfo->getXuid(), [
				"active" => $session->getActiveCosmetics()
			], true), function (array $data) use ($event): void{
				CosmeticManager::getInstance()->deleteSession($event->getPlayer()->getName());
			});
		}
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
