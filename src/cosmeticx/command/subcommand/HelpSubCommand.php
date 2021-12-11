<?php
/*
 * Copyright (c) 2021. Jan Sohn.
 * All rights reserved.
 * I don't want anyone to use my source code without permission.
 */
declare(strict_types=1);
namespace cosmeticx\command\subcommand;
use cosmeticx\command\SubCommand;
use cosmeticx\CosmeticX;
use pocketmine\command\CommandSender;


/**
 * Class HelpSubCommand
 * @package cosmeticx\command\subcommand
 * @author Jan Sohn / xxAROX
 * @date 11. Dezember, 2021 - 21:01
 * @ide PhpStorm
 * @project PocketMine-Client
 */
class HelpSubCommand extends SubCommand{
	protected array $help = [];

	/**
	 * HelpSubCommand constructor.
	 * @param string $name
	 * @param array $aliases
	 */
	public function __construct(string $name, array $aliases = []){
		parent::__construct($name, $aliases);
		foreach (CosmeticX::getInstance()->command->getSubCommands() as $_ => $subCommand) {
			$this->help[] = "§a  /" . CosmeticX::getInstance()->command->getName() . " {$subCommand->getName()}" . (count($subCommand->getAliases()) > 0
					? " |" . implode("|", $subCommand->getAliases()) : "");
		}
	}

	/**
	 * Function execute
	 * @param CommandSender $sender
	 * @param array $args
	 * @return void
	 */
	public function execute(CommandSender $sender, array $args): void{
		$sender->sendMessage(PHP_EOL . implode(PHP_EOL, $this->help));
	}
}
