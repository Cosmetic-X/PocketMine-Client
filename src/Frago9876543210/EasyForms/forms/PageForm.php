<?php
/*
 * Copyright (c) 2021 Jan Sohn.
 * All rights reserved.
 * I don't want anyone to use my source code without permission.
 */
namespace Frago9876543210\EasyForms\forms;
use Closure;
use Exception;
use Frago9876543210\EasyForms\elements\Button;
use Frago9876543210\EasyForms\elements\FunctionalButton;
use pocketmine\utils\TextFormat;


/**
 * Class PageForm
 * @package Frago9876543210\EasyForms\forms
 * @author xxAROX
 * @date 25.10.2020 - 01:56
 * @project StimoCloud
 */
class PageForm{
	protected Player $player;
	protected string $title = "";
	/** @var string[][]|FunctionalButton[][] */
	protected array $pages = [];
	protected ?Closure $onClose = null;
	protected int $activePage;
	protected MenuForm $form;

	/**
	 * PageForm constructor.
	 * @param Player $player
	 * @param string $title
	 * @param string[]|FunctionalButton[] $pages
	 * @param Closure|null $onClose
	 */
	public function __construct(Player $player, string $title, array $pages = [], ?Closure $onClose = null){
		$this->player = $player;
		$this->title = $title;
		$this->pages = $pages;
		$this->activePage = 1;
		$this->setOnClose($onClose);
		try {
			$this->resendForm();
		} catch (Exception $e) {
		}
	}

	/**
	 * Function setOnClose
	 * @param null|Closure $onClose
	 * @return void
	 */
	public function setOnClose(?Closure $onClose): void{
		if ($onClose !== null) {
			$this->onClose = $onClose;
		}
	}

	/**
	 * Function updateForm
	 * @return void
	 * @throws Exception
	 */
	private function resendForm(): void{
		$buttonForm = (isset($this->pages[0]) && isset($this->pages[0][0])
			? $this->pages[0][0] instanceof FunctionalButton : null);
		if (is_null($buttonForm))
			throw new Exception("Invalid array format, it must be: '\$pages[integer][string|FunctionalButton]'");
		$this->player->sendForm(new MenuForm($this->title, ($buttonForm ? ""
				: implode(PHP_EOL . TextFormat::RESET, $this->pages[$this->activePage])), ($buttonForm
				? array_merge($this->pages[$this->activePage], $this->getActionButtons())
				: $this->getActionButtons()), function (Player $player, Button $button): void{
				if ($button instanceof FunctionalButton) {
					$button->onClick($player);
					return;
				}
				if (TextFormat::clean($button->getText()) == "<-") {
					$this->activePage--;
					$this->resendForm();
					return;
				}
				if (TextFormat::clean($button->getText()) == "->") {
					$this->activePage++;
					$this->resendForm();
					return;
				}
				//if (TextFormat::clean($button->getText()) == ($player instanceof self ? $player->translate("ui.button.close") : "%ui.button.close")) {
				if (TextFormat::clean($button->getText()) == "%ui.button.close") {
					return;
				}
			}, $this->onClose));
	}

	/**
	 * Function getActionButtons
	 * @return Button[]
	 */
	public function getActionButtons(): array{
		$buttons = [];
		if ($this->activePage <= 1) {
			$buttons[] = new Button("%ui.button.close");
		} else {
			$buttons[] = new Button("<-");
		}
		if ($this->activePage < count($this->pages)) {
			$buttons[] = new Button("->");
		}
		return $buttons;
	}
}
