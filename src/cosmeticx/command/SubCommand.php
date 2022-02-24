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
 * You MAY run this plugin on your server as .phar file.
 */
declare(strict_types=1);
namespace cosmeticx\command;
use pocketmine\command\CommandSender;


/**
 * Class SubCommand
 * @package cosmeticx\command
 * @author Jan Sohn / xxAROX
 * @date 11. Dezember, 2021 - 20:26
 * @ide PhpStorm
 * @project PocketMine-Client
 */
abstract class SubCommand{
	private string $name;
	private string $description;
	/** @var string[] */
	private array $aliases;
	private ?string $permission = null;

	/**
	 * SubCommand constructor.
	 * @param string $name
	 * @param string $description
	 * @param array $aliases
	 */
	public function __construct(string $name, string $description = "No description provided.", array $aliases = []){
		$this->name = strtolower($name);
		$this->description = $description;
		$this->aliases = array_map(fn(string $alias) => strtolower($alias), $aliases);
	}

	/**
	 * Function getName
	 * @return string
	 */
	public final function getName(): string{
		return $this->name;
	}

	/**
	 * Function getDescription
	 * @return string
	 */
	public function getDescription(): string{
		return $this->description;
	}

	/**
	 * Function getAliases
	 * @return array
	 */
	public final function getAliases(): array{
		return $this->aliases;
	}

	/**
	 * Function setPermission
	 * @param null|string $permission
	 * @return void
	 */
	public final function setPermission(?string $permission): void{
		$this->permission = $permission;
	}

	/**
	 * Function getPermission
	 * @return ?string
	 */
	public final function getPermission(): ?string{
		return $this->permission;
	}

	abstract public function execute(CommandSender $sender, array $args): void;
}
