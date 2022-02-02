<?php
/*
 * Copyright (c) Jan Sohn
 * All rights reserved.
 * This plugin is under GPL license
 */
declare(strict_types=1);
namespace cosmeticx\forms;
use Frago9876543210\EasyForms\forms\MenuForm;
use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;


/**
 * Class BasicForms
 * @package cosmeticx\forms
 * @author Jan Sohn / xxAROX
 * @date 11. Dezember, 2021 - 21:13
 * @ide PhpStorm
 * @project PocketMine-Client
 */
class BasicForms{
	use SingletonTrait;

	function sendPublicCosmeticsForm(Player $player): void{
		$player->sendForm(new MenuForm(

		));
	}
}
