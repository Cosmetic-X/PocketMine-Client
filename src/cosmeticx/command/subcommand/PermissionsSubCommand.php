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
	public function __construct(string $name, array $aliases = []){
		parent::__construct($name, $aliases);
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
