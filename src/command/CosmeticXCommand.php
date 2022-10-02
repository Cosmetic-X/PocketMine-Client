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
namespace cosmeticx\command;
use cosmeticx\command\subcommand\EncodeSubCommand;
use cosmeticx\command\subcommand\HelpSubCommand;
use cosmeticx\command\subcommand\InfoSubCommand;
use cosmeticx\command\subcommand\MenuSubCommand;
use cosmeticx\command\subcommand\PermissionsSubCommand;
use cosmeticx\command\subcommand\ReloadSubCommand;
use cosmeticx\command\subcommand\SlotCosmeticsMenuSubCommand;
use cosmeticx\command\subcommand\TestSubCommand;
use cosmeticx\command\subcommand\VerifySubCommand;
use cosmeticx\CosmeticX;
use pocketmine\command\Command;
use pocketmine\command\CommandMap;
use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use Throwable;
use xxAROX\utils\addons\commando\Commando;


/**
 * Class CosmeticXCommand
 * @package cosmeticx\command
 * @author Jan Sohn / xxAROX
 * @date 11. Dezember, 2021 - 20:19
 * @ide PhpStorm
 * @project PocketMine-Client
 */
final class CosmeticXCommand extends Commando{
	/**
	 * CosmeticXCommand constructor.
	 */
	public function __construct(){
		parent::__construct("cosmeticx", "Cosmetic-X command", ["cx"]);
	}

	/**
	 * Function prepare
	 * @return void
	 */
	protected function prepare(): void{
		$this->setPermission("cosmetic-x.command");
		$this->registerSubCommand(new HelpSubCommand("help", "Help sub-command.", ["?"]));
		$this->registerSubCommand(new InfoSubCommand("info", "Info sub-command.", ["i"]));
		$this->registerSubCommand(new ReloadSubCommand("reload", "Reload sub-command.", ["rl"]));
		$this->registerSubCommand(new MenuSubCommand("menu", "Shows public cosmetic menu.", ["public"]));
		$this->registerSubCommand(new SlotCosmeticsMenuSubCommand("slot", "Shows Slot cosmetic menu."));
		$this->registerSubCommand(new EncodeSubCommand("encode", "Encode cosmetic to the correct file format."));
		$this->registerSubCommand(new PermissionsSubCommand("permissions", "Show all permissions.", ["perms"]));
		$this->registerSubCommand(new VerifySubCommand("verify", "Verify user account for exclusive cosmetics.", ["v"]));
		$this->registerSubCommand(new TestSubCommand("test", "Test sub-command.", ["t"]));
	}

	protected function onRun(Player|CommandSender $sender, string $aliasUsed, array $args): void{
		$sender->sendMessage($this->getUsage());
	}

	/**
	 * Function unregister
	 * @param CommandMap $commandMap
	 * @return bool
	 */
	public function unregister(CommandMap $commandMap): bool{
		return false; //NOTE: can't overwrite this command
	}
}
