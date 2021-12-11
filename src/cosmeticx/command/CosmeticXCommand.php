<?php
/*
 * Copyright (c) 2021. Jan Sohn.
 * All rights reserved.
 * I don't want anyone to use my source code without permission.
 */
declare(strict_types=1);
namespace cosmeticx\command;
use cosmeticx\command\subcommand\HelpSubCommand;
use cosmeticx\command\subcommand\InfoSubCommand;
use cosmeticx\CosmeticX;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\player\Player;
use Throwable;


/**
 * Class CosmeticXCommand
 * @package cosmeticx\command
 * @author Jan Sohn / xxAROX
 * @date 11. Dezember, 2021 - 20:19
 * @ide PhpStorm
 * @project PocketMine-Client
 */
class CosmeticXCommand extends Command{
	/** @var SubCommand[] */
	private array $subCommands = [], $aliasSubCommands = [];

	/**
	 * CosmeticXCommand constructor.
	 */
	public function __construct(){
		parent::__construct("cosmeticx", "Cosmetic-X command", "§cUsage: §7/cosmeticx help", ["cx"]);
		$this->setPermission("cosmetic-x.command");
		$this->loadSubCommand(new InfoSubCommand("info", ["i"]));
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
	 * @return mixed|void
	 */
	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if (!isset($args[0])) {
			$sender->sendMessage($this->getUsage());
		} else {
			$subCommandName = strtolower((string)array_shift($args));
			if (isset($this->subCommands[$subCommandName])) {
				$subCommand = $this->subCommands[$subCommandName];
			} else if (isset($this->aliasSubCommands[$subCommandName])) {
				$subCommand = $this->aliasSubCommands[$subCommandName];
			} else {
				$sender->sendMessage("Unknown sub-command '{$subCommandName}'.");
				return;
			}
			if (!is_null($subCommand->getPermission()) && !$this->testPermission($sender, $subCommand->getPermission())) {
				return;
			}
			if ($subCommand instanceof PlayerSubCommand && !$sender instanceof Player) {
				$sender->sendMessage("§cSub-command can only executed by players.");
				return;
			}
			if ($subCommand instanceof ConsoleSubCommand && !$sender instanceof ConsoleCommandSender) {
				return;
			}
			try {
				$subCommand->execute($sender, $args);
			} catch (Throwable $throwable) {
				$sender->sendMessage("§cError while executing sub-command '{$subCommand->getName()}'");
				CosmeticX::getInstance()->getLogger()->logException($throwable);
			}
		}
	}
}
