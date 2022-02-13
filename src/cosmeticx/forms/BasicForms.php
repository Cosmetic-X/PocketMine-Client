<?php
/*
 * Copyright (c) Jan Sohn
 * All rights reserved.
 * This plugin is under GPL license
 */
declare(strict_types=1);
namespace cosmeticx\forms;
use cosmeticx\CosmeticManager;
use cosmeticx\cosmetics\Cosmetic;
use cosmeticx\CosmeticX;
use Frago9876543210\EasyForms\elements\FunctionalButton;
use Frago9876543210\EasyForms\forms\MenuForm;
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
		$session = CosmeticManager::getInstance()->getSession($player->getName());
		$player->sendForm(new MenuForm(
			CosmeticX::getInstance()->getDescription()->getName(),
			(count(CosmeticManager::getInstance()->getPublicCosmetics()) == 0) ? "§cNo cosmetics found" : "",
			array_merge(($player->getSkin()->getSkinData() != $session->getLegacySkin()->getSkinData() ? [new FunctionalButton("§cReset", function (Player $player) use ($session): void{
				$session->deactivateCosmetics();
				$player->setSkin($session->getLegacySkin());
				$player->sendSkin();
			})] : []), array_map(function (Cosmetic $cosmetic) use ($session){
				return new FunctionalButton($cosmetic->getDisplayName(), function (Player $player) use ($cosmetic, $session): void{
					if (!$session->isActiveCosmetic($cosmetic)) {
						$session->activateCosmetic($cosmetic);
					} else {
						$session->deactivateCosmetic($cosmetic);
					}
					$this->sendPublicCosmeticsForm($player);
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
		$session = CosmeticManager::getInstance()->getSession($player->getName());
		$player->sendForm(new MenuForm(
			CosmeticX::getInstance()->getDescription()->getName(),
			(count(CosmeticManager::getInstance()->getSlotCosmetics()) == 0) ? "§cNo cosmetics found" : "",
			array_merge(($player->getSkin()->getSkinData() != $session->getLegacySkin()->getSkinData() ? [new FunctionalButton("§cReset", function (Player $player) use ($session): void{
				$session->deactivateCosmetics();
				$player->setSkin($session->getLegacySkin());
				$player->sendSkin();
			})] : []), array_map(function (Cosmetic $cosmetic) use ($session){
				return new FunctionalButton($cosmetic->getDisplayName() . "§r" . PHP_EOL . ($session->isActiveCosmetic($cosmetic) ? "§aActive" : "§cInactive"), function (Player $player) use ($cosmetic, $session): void{
					if (!$session->isActiveCosmetic($cosmetic)) {
						$session->activateCosmetic($cosmetic);
					} else {
						$session->deactivateCosmetic($cosmetic);
					}
					$this->sendSlotCosmeticsForm($player);
				}, $cosmetic->getImage());
			}, CosmeticManager::getInstance()->getSlotCosmetics()))
		));
	}
}
