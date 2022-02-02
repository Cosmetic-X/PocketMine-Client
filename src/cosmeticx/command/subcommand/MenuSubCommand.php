<?php
/*
 * Copyright (c) Jan Sohn
 * All rights reserved.
 * This plugin is under GPL license
 */

declare(strict_types=1);
namespace cosmeticx\command\subcommand;
use cosmeticx\command\PlayerSubCommand;
use cosmeticx\forms\BasicForms;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;


/**
 * Class MenuSubCommand
 * @package cosmeticx\command\subcommand
 * @author Jan Sohn / xxAROX
 * @date 02. Februar, 2022 - 02:05
 * @ide PhpStorm
 * @project PocketMine-Client
 */
class MenuSubCommand extends PlayerSubCommand{
	/**
	 * Function execute
	 * @param Player $sender
	 * @param array $args
	 * @return void
	 */
	public function execute(CommandSender $sender, array $args): void{
		BasicForms::getInstance()->sendPublicCosmeticsForm($sender);
	}
}
