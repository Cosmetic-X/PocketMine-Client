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
use Frago9876543210\EasyForms\forms\MenuForm;
use pocketmine\command\CommandSender;


/**
 * Class PermissionsSubCommand
 * @package cosmeticx\command\subcommand
 * @author Jan Sohn / xxAROX
 * @date 02. Februar, 2022 - 05:23
 * @ide PhpStorm
 * @project PocketMine-Client
 */
class PermissionsSubCommand extends SubCommand{
	/**
	 * PermissionsSubCommand constructor.
	 * @param string $name
	 * @param array $aliases
	 */
	public function __construct(string $name, string $description = "No description provided.", array $aliases = []){
		parent::__construct($name, $description, $aliases);
		$this->setPermission("permission");
	}

	/**
	 * Function execute
	 * @param CommandSender $sender
	 * @param array $args
	 * @return void
	 */
	public function execute(CommandSender $sender, array $args): void{
		if (method_exists($sender, "sendForm")) {
			$sender->sendForm(new MenuForm(
				"Cosmetic-X - Permissions",
				implode(PHP_EOL, array_map(fn (string $permission) => $permission->getName(), CosmeticX::getInstance()->getPermissions()))
			));
		} else {
			foreach (CosmeticX::getInstance()->getPermissions() as $permission) {
				$sender->sendMessage("   " . $permission->getName());
			}
		}
	}
}
