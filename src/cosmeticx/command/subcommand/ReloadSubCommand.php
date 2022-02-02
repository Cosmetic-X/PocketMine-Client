<?php
/*
 * Copyright (c) Jan Sohn
 * All rights reserved.
 * This plugin is under GPL license
 */

declare(strict_types=1);
namespace cosmeticx\command\subcommand;
use cosmeticx\command\ConsoleSubCommand;
use cosmeticx\CosmeticX;
use pocketmine\command\CommandSender;


/**
 * Class ReloadSubCommand
 * @package cosmeticx\command\subcommand
 * @author Jan Sohn / xxAROX
 * @date 01. Februar, 2022 - 23:03
 * @ide PhpStorm
 * @project PocketMine-Client
 */
class ReloadSubCommand extends ConsoleSubCommand{
	/**
	 * Function execute
	 * @param CommandSender $sender
	 * @param array $args
	 * @return void
	 */
	public function execute(CommandSender $sender, array $args): void{
		CosmeticX::getInstance()->reload();
	}
}
