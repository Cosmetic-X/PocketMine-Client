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
use cosmeticx\CosmeticX;
use pocketmine\command\CommandSender;
use pocketmine\Server;


/**
 * Class InfoSubCommand
 * @package cosmeticx\command\subcommand
 * @author Jan Sohn / xxAROX
 * @date 11. Dezember, 2021 - 20:50
 * @ide PhpStorm
 * @project PocketMine-Client
 */
class InfoSubCommand extends SubCommand{
	public function execute(CommandSender $sender, array $args): void{
		$desc = CosmeticX::getInstance()->getDescription();
		$sender->sendMessage("----- " . $desc->getName() . " -----");
		$sender->sendMessage("  Software: " . Server::getInstance()->getName());
		$sender->sendMessage("  Token-Owner: " . CosmeticX::getInstance()->getHolder());
		$sender->sendMessage("  Version: " . $desc->getVersion());
		$sender->sendMessage("  Description: " . $desc->getDescription());
		$sender->sendMessage("  Authors: " . join(", ", $desc->getAuthors()));
	}
}
