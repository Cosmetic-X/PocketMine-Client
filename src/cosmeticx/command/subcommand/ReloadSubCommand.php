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
 * Class ReloadSubCommand
 * @package cosmeticx\command\subcommand
 * @author Jan Sohn / xxAROX
 * @date 01. Februar, 2022 - 23:03
 * @ide PhpStorm
 * @project PocketMine-Client
 */
class ReloadSubCommand extends SubCommand{
	/**
	 * ReloadSubCommand constructor.
	 * @param string $name
	 * @param string $description
	 * @param array $aliases
	 */
	public function __construct(string $name, string $description = "No description provided.", array $aliases = []){
		parent::__construct($name, $description, $aliases);
		$this->setPermission("reload");
	}

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
