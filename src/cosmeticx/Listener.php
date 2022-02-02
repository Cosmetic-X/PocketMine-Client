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
use pocketmine\player\XboxLivePlayerInfo;


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
	 * Function PlayerLoginEvent
	 * @param PlayerLoginEvent $event
	 * @return void
	 * @priority MONITOR
	 */
	public function PlayerLoginEvent(PlayerLoginEvent $event): void{
		$playerInfo = $event->getPlayer()->getPlayerInfo();
		if (!$playerInfo instanceof XboxLivePlayerInfo) {
			CosmeticX::getInstance()->getLogger()->emergency("Please install WD_LoginDataFix, can be download here https://github.com/xxAROX/WaterdogPE-LoginExtras-Fix/releases/download/latest/WD_LoginDataFix.phar");
		} else {
			CosmeticX::sendRequest(new ApiRequest("/users/cosmetics/{$playerInfo->getXuid()}", ["skinData" => Utils::encodeSkinData($event->getPlayer()->getSkin()->getSkinData())]), function (array $data) use ($event): void{
				$session = CosmeticManager::getInstance()->getSession($event->getPlayer());
				$skin = $session->getHolder()->getSkin();
				$session->getHolder()->setSkin(new Skin($skin->getSkinId(), Utils::decodeSkinData($data["buffer"]), $skin->getCapeData(), $data["geometry_name"] ?? $skin->getGeometryName(), $data["geometry_data"] ?? $skin->getGeometryData()));
				$session->getHolder()->sendSkin();
			});
		}
	}

	/**
	 * Function PlayerQuitEvent
	 * @param PlayerQuitEvent $event
	 * @return void
	 * @priority MONITOR
	 */
	public function PlayerQuitEvent(PlayerQuitEvent $event): void{
		unset(CosmeticManager::getInstance()->legacy[$event->getPlayer()->getPlayerInfo()->getUsername()]);

		$playerInfo = $event->getPlayer()->getPlayerInfo();
		if (!$playerInfo instanceof XboxLivePlayerInfo) {
			CosmeticX::getInstance()->getLogger()->emergency("Please install WD_LoginDataFix, can be download here https://github.com/xxAROX/WaterdogPE-LoginExtras-Fix/releases/download/latest/WD_LoginDataFix.phar");
		} else {
			CosmeticX::sendRequest(new ApiRequest("/users/cosmetics/{$playerInfo->getXuid()}", [], true), function (array $data) use ($event): void{
				var_dump($data);
				//TODO: store player cosmetics
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
