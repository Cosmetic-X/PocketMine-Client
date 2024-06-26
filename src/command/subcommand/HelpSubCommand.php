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
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use xxAROX\forms\elements\Button;
use xxAROX\forms\types\MenuForm;
use xxAROX\utils\addons\commando\SubCommando;


/**
 * Class HelpSubCommand
 * @package cosmeticx\command\subcommand
 * @author Jan Sohn / xxAROX
 * @date 11. Dezember, 2021 - 21:01
 * @ide PhpStorm
 * @project PocketMine-Client
 */
class HelpSubCommand extends SubCommando{
	/**
	 * Function prepare
	 * @return void
	 */
	protected function prepare(): void{
	}

	/**
	 * Function onRun
	 * @param Player|CommandSender $sender
	 * @param string $aliasUsed
	 * @param array $args
	 * @return void
	 */
	public function onRun(Player|CommandSender $sender, string $aliasUsed, array $args): void{
		$help = [];
		foreach (CosmeticX::getInstance()->getCosmeticXCommand()->getSubCommands() as $_ => $subCommand) {
			if (!$subCommand instanceof self) $help[] = "§a/" . CosmeticX::getInstance()->getCosmeticXCommand()->getName() . " " . $subCommand->getName() . " | " . (count($subCommand->getAliases()) > 0 ? implode(" | ", $subCommand->getAliases()) . " | " : "") . $subCommand->getDescription();
		}
		$help = array_unique($help);
		if ($sender instanceof Player) {
			$sender->sendForm(new MenuForm("Help", "", array_map(fn (string $str) => new Button($cmd=TextFormat::clean($str), function (Player $player) use ($cmd): void{
				Server::getInstance()->dispatchCommand($player, str_replace("/", "", explode(" | ", $cmd)[0]));
			}), $help)));
		} else {
			$sender->sendMessage("§a--- " . CosmeticX::getInstance()->getDescription()->getName() . " - Help ---" . PHP_EOL . implode(PHP_EOL, $help));
		}
	}
}
