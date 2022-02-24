<?php
/*
 * Copyright (c) Jan Sohn
 * All rights reserved.
 * This plugin is under GPL license
 */
declare(strict_types=1);
namespace cosmeticx;
use cosmeticx\utils\Utils;
use JsonMapper_Exception;
use pocketmine\entity\Skin;
use pocketmine\event\player\PlayerChangeSkinEvent;
use pocketmine\event\player\PlayerCreationEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\handler\LoginPacketHandler;
use pocketmine\network\mcpe\JwtException;
use pocketmine\network\mcpe\JwtUtils;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\network\mcpe\protocol\types\login\ClientData;
use pocketmine\network\PacketHandlingException;
use pocketmine\player\XboxLivePlayerInfo;
use pocketmine\Server;
use ReflectionClass;
use ReflectionException;


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
				"username" => $playerInfo->getUsername(),
				"skinData" => Utils::encodeSkinData($playerInfo->getSkin()->getSkinData()),
				"geometry_name" => $playerInfo->getSkin()->getGeometryName(),
				"geometry_data" => $playerInfo->getSkin()->getGeometryData(),
			],
				true
			), function (array $data) use ($event, $playerInfo, $session): void{
				$session->setPremium($data["premium"] ?? false);
				$session->setLegacySkin(new Skin($playerInfo->getSkin()->getSkinId(), Utils::decodeSkinData($data["legacySkinData"]), $playerInfo->getSkin()->getCapeData(), $data["geometry_name"] ?? $playerInfo->getSkin()->getGeometryName(), $data["geometry_data"] ?? $playerInfo->getSkin()->getGeometryData()));
				$session->sendSkin($data["buffer"], $data["geometry_data"]);
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
				"active" => $session->getActiveCosmetics(),
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

	/**
	 * Function DataPacketReceiveEvent
	 * @param DataPacketReceiveEvent $event
	 * @return void
	 * @throws ReflectionException
	 * @priority MONITOR
	 * @handleCancelled true
	 */
	public function DataPacketReceiveEvent(DataPacketReceiveEvent $event): void{
		if (CosmeticX::$IS_WATERDOG_ENABLED) {
			$packet = $event->getPacket();
			if ($packet instanceof LoginPacket) {
				try {
					[, $clientData,] = JwtUtils::parse($packet->clientDataJwt);
				} catch (JwtException $e) {
					throw PacketHandlingException::wrap($e);
				}
				$event->getOrigin()->setHandler(new class(Server::getInstance(), $event->getOrigin(), function (XboxLivePlayerInfo $info) use ($event, $clientData, $packet): void{
					$class = new ReflectionClass($event->getOrigin());
					$property = $class->getProperty("info");
					$property->setAccessible(true);
					$property->setValue($event->getOrigin(), new XboxLivePlayerInfo($clientData["Waterdog_XUID"], $info->getUsername(), $info->getUuid(), $info->getSkin(), $info->getLocale(), $info->getExtraData()));
				}, function (bool $isAuthenticated, bool $authRequired, ?string $error, ?string $clientPubKey) use ($event): void{
					$class = new ReflectionClass($event->getOrigin());
					$method = $class->getMethod("setAuthenticationStatus");
					$method->setAccessible(true);
					$method->invoke($event->getOrigin(), $isAuthenticated, $authRequired, $error, $clientPubKey);
				}) extends LoginPacketHandler{
					/**
					 * Function parseClientData
					 * @param string $clientDataJwt
					 * @return ClientData
					 */
					protected function parseClientData(string $clientDataJwt): ClientData{
						try {
							[, $clientDataClaims,] = JwtUtils::parse($clientDataJwt);
						} catch (JwtException $e) {
							throw PacketHandlingException::wrap($e);
						}
						$mapper = new \JsonMapper;
						$mapper->bEnforceMapType = false;
						$mapper->bExceptionOnMissingData = true;
						$mapper->bExceptionOnUndefinedProperty = true;
						try {
							$properties = array_map(fn(\ReflectionProperty $property) => $property->getName(), (new ReflectionClass(ClientData::class))->getProperties());
							foreach ($clientDataClaims as $k => $v) {
								if (!in_array($k, $properties)) {
									unset($clientDataClaims[$k]);
								}
							}
							unset($properties);
							$clientData = $mapper->map($clientDataClaims, new ClientData);
						} catch (JsonMapper_Exception $e) {
							throw PacketHandlingException::wrap($e);
						}
						return $clientData;
					}
				});
				if (isset($clientData["Waterdog_IP"])) {
					$class = new ReflectionClass($event->getOrigin());
					$property = $class->getProperty("ip");
					$property->setAccessible(true);
					$property->setValue($event->getOrigin(), $clientData["Waterdog_IP"]);
				}
				unset($clientData);
			}
		}
	}
}
