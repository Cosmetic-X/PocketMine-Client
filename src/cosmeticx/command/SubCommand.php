<?php
/*
 * Copyright (c) 2021. Jan Sohn.
 * All rights reserved.
 * I don't want anyone to use my source code without permission.
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
	/** @var string[] */
	private array $aliases;
	private ?string $permission = null;

	/**
	 * SubCommand constructor.
	 * @param string $name
	 * @param array $aliases
	 */
	public function __construct(string $name, array $aliases = []){
		$this->name = strtolower($name);
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
