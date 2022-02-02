<?php
/*
 * Copyright (c) Jan Sohn
 * All rights reserved.
 * This plugin is under GPL license
 */
namespace Frago9876543210\EasyForms\elements;
use Closure;
use pocketmine\player\Player;


/**
 * Class FunctionalButton
 * @package Frago9876543210\EasyForms\elements
 * @author xxAROX
 * @date 25.10.2020 - 02:26
 * @project StimoCloud
 */
class FunctionalButton extends Button{
	protected ?Closure $onClick = null;

	/**
	 * FunctionalButton constructor.
	 * @param string $text
	 * @param null|Closure $onClick
	 * @param null|Image $image
	 */
	public function __construct(string $text, ?Closure $onClick = null, ?Image $image = null){
		parent::__construct($text, $image);
		$this->onClick = $onClick;
	}

	/**
	 * Function onClick
	 * @param Player $player
	 * @return void
	 */
	public function onClick(Player $player): void{
		($this->onClick)($player);
	}
}
