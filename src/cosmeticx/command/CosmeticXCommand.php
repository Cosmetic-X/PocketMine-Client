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
use cosmeticx\CosmeticX;
use pocketmine\command\Command;
use pocketmine\command\CommandMap;
use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use Throwable;


/**
 * Class CosmeticXCommand
 * @package cosmeticx\command
 * @author Jan Sohn / xxAROX
 * @date 11. Dezember, 2021 - 20:19
 * @ide PhpStorm
 * @project PocketMine-Client
 */
final class CosmeticXCommand extends Command{
	/** @var SubCommand[] */
	private array $subCommands = [], $aliasSubCommands = [];

	/**
	 * CosmeticXCommand constructor.
	 */
	public function __construct(){
		parent::__construct("cosmeticx", "Cosmetic-X command", "§cUsage: §7/cosmeticx help", ["cx"]);
		$this->setPermission("cosmetic-x.command");
		$this->loadSubCommand(new HelpSubCommand("help", "Info sub-command.", ["?"]));
		$this->loadSubCommand(new InfoSubCommand("info", "Info sub-command.", ["i"]));
		$this->loadSubCommand(new ReloadSubCommand("reload", "Reload sub-command.", ["rl"]));
		$this->loadSubCommand(new MenuSubCommand("menu", "Shows public cosmetic menu.", ["public"]));
		$this->loadSubCommand(new SlotCosmeticsMenuSubCommand("slot", "Shows Slot cosmetic menu."));
		$this->loadSubCommand(new EncodeSubCommand("encode", "Encode cosmetic to the correct file format."));
		$this->loadSubCommand(new PermissionsSubCommand("permissions", "Show all permissions.", ["perms"]));
	}

	/**
	 * Function getSubCommands
	 * @return SubCommand[]
	 */
	public function getSubCommands(): array{
		return $this->subCommands;
	}

	public function loadSubCommand(SubCommand $subCommand): void{
		$this->subCommands[$subCommand->getName()] = $subCommand;
		foreach ($subCommand->getAliases() as $alias) {
			if ($alias != "") {
				$this->aliasSubCommands[$alias] = $subCommand;
			}
		}
	}

	/**
	 * Function execute
	 * @param CommandSender $sender
	 * @param string $commandLabel
	 * @param array $args
	 * @return void
	 */
	public function execute(CommandSender $sender, string $commandLabel, array $args): void{
		if (!isset($args[0])) {
			$sender->sendMessage($this->getUsage());
		} else {
			$subCommandName = strtolower((string)array_shift($args));
			if (isset($this->subCommands[$subCommandName])) {
				$subCommand = $this->subCommands[$subCommandName];
			} else if (isset($this->aliasSubCommands[$subCommandName])) {
				$subCommand = $this->aliasSubCommands[$subCommandName];
			} else {
				$sender->sendMessage("Unknown sub-command '" . $subCommandName . "'.");
				return;
			}
			if ($subCommand instanceof PlayerSubCommand && !$sender instanceof Player) {
				$sender->sendMessage("§cSub-command can only executed by players.");
				return;
			}
			if ($subCommand instanceof ConsoleSubCommand && !$sender instanceof ConsoleCommandSender) {
				if (Server::getInstance()->isOp($sender->getName())) {
					$sender->sendMessage("§cSub-command can only executed in console.");
				}
				return;
			}
			if (!is_null($subCommand->getPermission()) && !$this->testPermission($sender, $this->getPermission() . "." . $subCommand->getPermission())) {
				return;
			}
			try {
				$subCommand->execute($sender, $args);
			} catch (Throwable $throwable) {
				$sender->sendMessage("§cError while executing sub-command '" . $subCommand->getName() . "'");
				CosmeticX::getInstance()->getLogger()->logException($throwable);
			}
		}
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
