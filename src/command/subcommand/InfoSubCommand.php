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
use cosmeticx\command\SubCommand;
use cosmeticx\CosmeticManager;
use cosmeticx\CosmeticX;
use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use xxAROX\forms\types\MenuForm;
use xxAROX\utils\addons\commando\SubCommando;


/**
 * Class InfoSubCommand
 * @package cosmeticx\command\subcommand
 * @author Jan Sohn / xxAROX
 * @date 11. Dezember, 2021 - 20:50
 * @ide PhpStorm
 * @project PocketMine-Client
 */
class InfoSubCommand extends SubCommando{
	/**
	 * Function prepare
	 * @return void
	 */
	protected function prepare(): void{
	}

	/**
	 * Function onRun
	 * @param Player|CommandSender $sender
	 * @param string $aliasUsed
	 * @param array $args
	 * @return void
	 */
	public function onRun(Player|CommandSender $sender, string $aliasUsed, array $args): void{
		$desc = CosmeticX::getInstance()->getDescription();
		$string = "§7§m-----------------------------------------------------\n";
		$string .= "§a§l" . $desc->getName() . " §8§l» §7" . $desc->getDescription() . "\n";
		$string .= "§7§m-----------------------------------------------------\n";
		$string .= "  §aSoftware:  §8§l» §7" . Server::getInstance()->getName() . "\n";
		$string .= "  §aVersion:  §8§l» §7" . $desc->getVersion() . "\n";
		$string .= "  §aAuthor:  §8§l» §7" . implode(", ", $desc->getAuthors()) . "\n";
		$string .= "  §aToken-Owner:  §8§l» §7" . CosmeticX::getInstance()->getHolder() . "\n";
		$string .= "  §aLoaded public-cosmetics:  §8§l» §7" . count(CosmeticManager::getInstance()->getPublicCosmetics()) . "\n";
		$string .= "  §aLoaded server-cosmetics:  §8§l» §7" . count(CosmeticManager::getInstance()->getServerCosmetics()) . "\n";
		$string .= "§7§m-----------------------------------------------------\n";
		if ($sender instanceof ConsoleCommandSender) {
			$sender->sendMessage($string);
		} else if ($sender instanceof Player) {
			$sender->sendForm(new MenuForm($desc->getFullName(), $string));
		}
		//$sender->sendMessage("------- " . $desc->getName() . " -------");
	}
}
