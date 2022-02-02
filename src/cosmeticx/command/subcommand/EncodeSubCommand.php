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


/**
 * Class EncodeSubCommand
 * @package cosmeticx\command\subcommand
 * @author Jan Sohn / xxAROX
 * @date 02. Februar, 2022 - 03:17
 * @ide PhpStorm
 * @project PocketMine-Client
 */
class EncodeSubCommand extends SubCommand{
	public function __construct(string $name, array $aliases = []){
		parent::__construct($name, $aliases);
		$this->setPermission("encode");
	}

	public function execute(CommandSender $sender, array $args): void{
		$path = CosmeticX::getInstance()->getDataFolder() . $args[0];
		if (!is_file($path . ".png")) {
			$sender->sendMessage("§cFile '$path.png' not found");
		} else {
			$sender->sendMessage("§eEncoding..");
			file_put_contents($path . ".txt", base64_encode(file_get_contents($path . ".png")));
			$sender->sendMessage("§aDone!");
		}
	}
}
