<?php
/*
 * Copyright (c) Jan Sohn
 * All rights reserved.
 * This plugin is under GPL license
 */
declare(strict_types=1);
namespace cosmeticx\forms;
use cosmeticx\ApiRequest;
use cosmeticx\CosmeticManager;
use cosmeticx\cosmetics\Cosmetic;
use cosmeticx\CosmeticX;
use cosmeticx\utils\Utils;
use Frago9876543210\EasyForms\elements\FunctionalButton;
use Frago9876543210\EasyForms\forms\MenuForm;
use pocketmine\entity\Skin;
use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;


/**
 * Class BasicForms
 * @package cosmeticx\forms
 * @author Jan Sohn / xxAROX
 * @date 11. Dezember, 2021 - 21:13
 * @ide PhpStorm
 * @project PocketMine-Client
 */
class BasicForms{
	use SingletonTrait;


	/**
	 * Function sendPublicCosmeticsForm
	 * @param Player $player
	 * @return void
	 */
	function sendPublicCosmeticsForm(Player $player): void{
		$player->sendForm(new MenuForm(
			CosmeticX::PREFIX,
			(count(CosmeticManager::getInstance()->getPublicCosmetics()) == 0) ? "§cNo cosmetics found" : "",
			array_merge(($player->getSkin()->getSkinData() != (isset(CosmeticManager::getInstance()->legacy[$player->getPlayerInfo()->getUsername()]) ? CosmeticManager::getInstance()->legacy[$player->getPlayerInfo()->getUsername()]->getSkinData() : "JIC") ? [new FunctionalButton("§cReset Skin", function (Player $player): void{
					$player->setSkin(CosmeticManager::getInstance()->legacy[$player->getPlayerInfo()->getUsername()]);
					$player->sendSkin();
				})] : []), array_map(function (Cosmetic $cosmetic){
				return new FunctionalButton($cosmetic->getDisplayName(), function (Player $player) use ($cosmetic): void{
					CosmeticX::sendRequest(new ApiRequest("/merge-skin-with-cosmetic", ["id" => $cosmetic->getId(),"skinData" => Utils::encodeSkinData($player->getSkin()->getSkinData())], true), function (array $data) use ($player): void{
						$skin = $player->getSkin();
						$player->setSkin(new Skin($skin->getSkinId(), Utils::decodeSkinData($data["buffer"]), $skin->getCapeData(), $data["geometry_name"] ?? $skin->getGeometryName(), $data["geometry_data"] ?? $skin->getGeometryData()));
						$player->sendSkin();
					});
				}, $cosmetic->getImage());
			}, CosmeticManager::getInstance()->getPublicCosmetics()))
		));
	}

	/**
	 * Function sendSlotCosmeticsForm
	 * @param Player $player
	 * @return void
	 */
	function sendSlotCosmeticsForm(Player $player): void{
		$player->sendForm(new MenuForm(
            CosmeticX::PREFIX,
			(count(CosmeticManager::getInstance()->getSlotCosmetics()) == 0) ? "§cNo cosmetics found" : "",
			array_merge(($player->getSkin()->getSkinData() != (isset(CosmeticManager::getInstance()->legacy[$player->getPlayerInfo()->getUsername()]) ? CosmeticManager::getInstance()->legacy[$player->getPlayerInfo()->getUsername()]->getSkinData() : "JIC") ? [new FunctionalButton("§cReset Skin", function (Player $player): void{
					$player->setSkin(CosmeticManager::getInstance()->legacy[$player->getPlayerInfo()->getUsername()]);
					$player->sendSkin();
				})] : []), array_map(function (Cosmetic $cosmetic){
				return new FunctionalButton($cosmetic->getDisplayName(), function (Player $player) use ($cosmetic): void{
					CosmeticX::sendRequest(new ApiRequest("/slot/merge-skin-with-cosmetic", ["id" => $cosmetic->getId(),"skinData" => Utils::encodeSkinData($player->getSkin()->getSkinData())], true), function (array $data) use ($player): void{
						$skin = $player->getSkin();
						$player->setSkin(new Skin($skin->getSkinId(), Utils::decodeSkinData($data["buffer"]), $skin->getCapeData(), $data["geometry_name"] ?? $skin->getGeometryName(), $data["geometry_data"] ?? $skin->getGeometryData()));
						$player->sendSkin();
					});
				}, $cosmetic->getImage());
			}, CosmeticManager::getInstance()->getSlotCosmetics()))
		));
	}
}
