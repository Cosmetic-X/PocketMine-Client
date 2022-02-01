<?php
/*
 * Copyright (c) Jan Sohn
 * All rights reserved.
 * This plugin is under GPL license
 */
declare(strict_types=1);
namespace cosmeticx\cosmetics;
use Ramsey\Uuid\Uuid;


/**
 * Class Cosmetic
 * @package cosmeticx\cosmetics
 * @author Jan Sohn / xxAROX
 * @date 11. Dezember, 2021 - 21:16
 * @ide PhpStorm
 * @project PocketMine-Client
 */
class Cosmetic{
	const PUBLIC = 0;
	const SLOT   = 1;
	private string $name;
	private string $display_name;
	private bool $public;
	private string $id;

	/**
	 * Cosmetic constructor.
	 * @param string $name
	 * @param string $display_name
	 * @param null|string $id
	 * @param int $type
	 */
	public function __construct(string $name, string $display_name, string $id = null, int $type = self::PUBLIC){
		$this->name = $name;
		$this->display_name = $display_name;
		$this->public = $type == self::PUBLIC;
		$this->id = $id ?? Uuid::uuid4()->toString();
	}

	/**
	 * Function getName
	 * @return string
	 */
	public function getName(): string{
		return $this->name;
	}

	/**
	 * Function getDisplayName
	 * @return string
	 */
	public function getDisplayName(): string{
		return $this->display_name;
	}

	/**
	 * Function isPublic
	 * @return bool
	 */
	public function isPublic(): bool{
		return $this->public;
	}

	/**
	 * Function isSlot
	 * @return bool
	 */
	public function isSlot(): bool{
		return !$this->public;
	}

	/**
	 * Function getId
	 * @return string
	 */
	public function getId(): string{
		return $this->id;
	}
}
