<?php
/*
 * Copyright (c) Jan Sohn
 * All rights reserved.
 * Only people with the explicit permission from Jan Sohn are allowed to modify, share or distribute this code.
 *
 * You are NOT allowed to do any kind of modification to this plugin.
 * You are NOT allowed to share this plugin with others without the explicit permission from Jan Sohn.
 * You are NOT allowed to run this plugin on your server as source code.
 * You MUST acquire this plugin from official sources.
 * You MUST run this plugin on your server as compiled .phar file from our releases.
 */
declare(strict_types=1);
namespace cosmeticx\forms;
use cosmeticx\CosmeticManager;
use cosmeticx\cosmetics\Cosmetic;
use cosmeticx\CosmeticX;
use Frago9876543210\EasyForms\elements\FunctionalButton;
use Frago9876543210\EasyForms\elements\Input;
use Frago9876543210\EasyForms\elements\Label;
use Frago9876543210\EasyForms\forms\CustomForm;
use Frago9876543210\EasyForms\forms\CustomFormResponse;
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
			(count(CosmeticManager::getInstance()->getServerCosmetics()) == 0) ? "§cNo cosmetics found" : "",
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
			}, CosmeticManager::getInstance()->getServerCosmetics()))
		));
	}

	function sendVerifyForm(Player $player, string $default = null): void{
		$player->sendForm(
			new CustomForm(
				"Cosmetic-X Verification",
				[
					new Label("Cosmetic-X Verification, for access to exclusive cosmetics"),
					new Label("§cYou must be a member of our Discord"),
					new Input("§l§bPlease enter your Discord tag or ID", "", $default ?? ""),
				],
				function (Player $player, CustomFormResponse $response): void{
					$discord_tag_or_id = $response->getInput()->getValue();
					if (strlen($discord_tag_or_id) < 0) {
						$player->sendMessage("§cPlease enter a valid Discord tag or ID");
						$this->sendVerifyForm($player, $discord_tag_or_id);
					} else {
						if (is_numeric($discord_tag_or_id) && (strlen($discord_tag_or_id) != 17 && strlen($discord_tag_or_id) != 18)) {
							$player->sendMessage("§cPlease enter a valid Discord ID");
							$this->sendVerifyForm($player, $discord_tag_or_id);
						} else if (!str_contains($discord_tag_or_id, "#") || strlen($discord_tag_or_id) < 5 || strlen($discord_tag_or_id) > 37) {
							$player->sendMessage("§cPlease enter a valid Discord tag or ID");
							$this->sendVerifyForm($player, $discord_tag_or_id);
						} else {
							CosmeticManager::getInstance()->getSessionOrThrow($player->getName())->verify($discord_tag_or_id);
						}
					}
				}
			)
		);
	}
}
