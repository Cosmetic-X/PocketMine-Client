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
use Frago9876543210\EasyForms\elements\FunctionalButton;
use Frago9876543210\EasyForms\forms\MenuForm;
use Frago9876543210\EasyForms\forms\PageForm;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;


/**
 * Class HelpSubCommand
 * @package cosmeticx\command\subcommand
 * @author Jan Sohn / xxAROX
 * @date 11. Dezember, 2021 - 21:01
 * @ide PhpStorm
 * @project PocketMine-Client
 */
class HelpSubCommand extends SubCommand{
	/**
	 * Function execute
	 * @param CommandSender $sender
	 * @param array $args
	 * @return void
	 */
	public function execute(CommandSender $sender, array $args): void{
		$help = [];
		foreach (CosmeticX::getInstance()->getCosmeticXCommand()->getSubCommands() as $_ => $subCommand) {
			if (!$subCommand instanceof self) {
				$help[] = "§a/" . CosmeticX::getInstance()->getCosmeticXCommand()->getName() . " {$subCommand->getName()}" . (count($subCommand->getAliases()) > 0 ? " | " . implode(" | ", $subCommand->getAliases()) : "");
			}
		}
		if ($sender instanceof Player) {
			$sender->sendForm(new MenuForm("Help", "", array_map(fn (string $str) => new FunctionalButton($cmd=TextFormat::clean($str), function (Player $player) use ($cmd): void{
				Server::getInstance()->dispatchCommand($player, str_replace("/", "", explode(" | ", $cmd)[0]));
			}), $help)));
		} else {
			$sender->sendMessage("§a--- " . CosmeticX::getInstance()->getDescription()->getName() . " - Help ---" . PHP_EOL . implode(PHP_EOL, $help));
		}
	}
}
