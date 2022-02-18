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
use pocketmine\utils\TextFormat;


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
		$sender->sendMessage(implode("\n", [
			CosmeticX::PREFIX.TextFormat::DARK_AQUA."Token-Owner: ".TextFormat::GRAY.CosmeticX::getInstance()->getHolder(),
			CosmeticX::PREFIX.TextFormat::DARK_AQUA."Version: ".$desc->getVersion(),
			CosmeticX::PREFIX.TextFormat::DARK_AQUA."Description: ".$desc->getDescription(),
			CosmeticX::PREFIX.TextFormat::DARK_AQUA."Authors: ".join(", ", $desc->getAuthors()),
		]));
	}
}
