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
use pocketmine\player\Player;
use xxAROX\utils\addons\commando\SubCommando;


/**
 * Class ReloadSubCommand
 * @package cosmeticx\command\subcommand
 * @author Jan Sohn / xxAROX
 * @date 01. Februar, 2022 - 23:03
 * @ide PhpStorm
 * @project PocketMine-Client
 */
class ReloadSubCommand extends SubCommando{
	/**
	 * ReloadSubCommand constructor.
	 * @param string $name
	 * @param string $description
	 * @param array $aliases
	 */
	public function __construct(string $name, string $description = "No description provided.", array $aliases = []){
		parent::__construct($name, $description, $aliases);
	}

	protected function prepare(): void{
		$this->setPermission("reload");
	}

	/**
	 * Function onRun
	 * @param Player|CommandSender $sender
	 * @param string $aliasUsed
	 * @param array $args
	 * @return void
	 */
	public function onRun(Player|CommandSender $sender, string $aliasUsed, array $args): void{
		CosmeticX::getInstance()->reload();
	}
}
