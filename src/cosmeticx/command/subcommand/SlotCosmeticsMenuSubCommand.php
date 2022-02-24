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
use cosmeticx\forms\BasicForms;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;


/**
 * Class SlotCosmeticsMenuSubCommand
 * @package cosmeticx\command\subcommand
 * @author Jan Sohn / xxAROX
 * @date 02. Februar, 2022 - 02:17
 * @ide PhpStorm
 * @project PocketMine-Client
 */
class SlotCosmeticsMenuSubCommand extends PlayerSubCommand{
	/**
	 * Function execute
	 * @param Player $sender
	 * @param array $args
	 * @return void
	 */
	public function execute(CommandSender $sender, array $args): void{
		BasicForms::getInstance()->sendSlotCosmeticsForm($sender);
	}
}
