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
use xxAROX\utils\addons\commando\Commando;
use xxAROX\utils\addons\commando\constraint\InGameRequiredConstraint;
use xxAROX\utils\addons\commando\SubCommando;


/**
 * Class MenuSubCommand
 * @package cosmeticx\command\subcommand
 * @author Jan Sohn / xxAROX
 * @date 02. Februar, 2022 - 02:05
 * @ide PhpStorm
 * @project PocketMine-Client
 */
class MenuSubCommand extends SubCommando{
	/**
	 * Function prepare
	 * @return void
	 */
	protected function prepare(): void{
		$this->addConstraint(new InGameRequiredConstraint($this));
	}

	/**
	 * Function onRun
	 * @param Player|CommandSender $sender
	 * @param string $aliasUsed
	 * @param array $args
	 * @return void
	 */
	public function onRun(Player|CommandSender $sender, string $aliasUsed, array $args): void{
		BasicForms::getInstance()->sendPublicCosmeticsForm($sender);
	}
}
