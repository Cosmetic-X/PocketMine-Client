<?php
/*
 * Copyright (c) Jan Sohn
 * All rights reserved.
 * This plugin is under GPL license
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
