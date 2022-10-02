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
namespace cosmeticx\command\subcommand;
use cosmeticx\command\PlayerSubCommand;
use cosmeticx\CosmeticManager;
use cosmeticx\cosmetics\Category;
use cosmeticx\cosmetics\Cosmetic;
use cosmeticx\CosmeticX;
use cosmeticx\forms\BasicForms;
use cosmeticx\forms\CategoryForm;
use cosmeticx\ResourcePackManager;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use xxAROX\forms\elements\Button;
use xxAROX\forms\elements\Image;
use xxAROX\forms\types\MenuForm;
use xxAROX\utils\addons\commando\SubCommando;


/**
 * Class TestSubCommand
 * @package cosmeticx\command\subcommand
 * @author Jan Sohn / xxAROX
 * @date 21. August, 2022 - 14:14
 * @ide PhpStorm
 * @project PocketMine-Client
 */
class TestSubCommand extends SubCommando{
	public function __construct(string $name, string $description = "No description provided.", array $aliases = []){
		parent::__construct($name, $description, $aliases);
	}

	protected function prepare(): void{
	}

	/**
	 * Function execute
	 * @param Player|CommandSender $sender
	 * @param string $aliasUsed
	 * @param array $args
	 * @return void
	 */
	public function onRun(Player|CommandSender $sender, string $aliasUsed, array $args): void{
		if (!$sender instanceof Player) return;
		$sender->sendForm(ResourcePackManager::getInstance()->getCategoriesDecorator()->decorate(new MenuForm(
			"§cC o s m e t i x  -  X",
			"",
			[
				new Button("Hat", fn (Player $player) => $player->sendForm(ResourcePackManager::getInstance()->getCategoryDecorator()->decorate(new CategoryForm($player, Category::HAT)))),
				new Button("Head", fn (Player $player) => $player->sendForm(ResourcePackManager::getInstance()->getCategoryDecorator()->decorate(new CategoryForm($player, Category::HEAD)))),
				new Button("Cape", fn (Player $player) => $player->sendForm(ResourcePackManager::getInstance()->getCategoryDecorator()->decorate(new CategoryForm($player, Category::CAPE)))),
				new Button("Body", fn (Player $player) => $player->sendForm(ResourcePackManager::getInstance()->getCategoryDecorator()->decorate(new CategoryForm($player, Category::BODY)))),
				new Button("Wings", fn (Player $player) => $player->sendForm(ResourcePackManager::getInstance()->getCategoryDecorator()->decorate(new CategoryForm($player, Category::WINGS)))),
				new Button("Shoes", fn (Player $player) => $player->sendForm(ResourcePackManager::getInstance()->getCategoryDecorator()->decorate(new CategoryForm($player, Category::SHOES)))),
			],
			fn (Player $player) => $player->sendMessage("§cCLOSED")
		)));

		//$sender->sendForm(new MenuForm("<cosmeticForm>"));
	}
}
