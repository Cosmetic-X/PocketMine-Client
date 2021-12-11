<?php
/*
 * Copyright (c) 2021. Jan Sohn.
 * All rights reserved.
 * I don't want anyone to use my source code without permission.
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
	private bool $public;
	private string $id;

	/**
	 * Cosmetic constructor.
	 * @param string $name
	 * @param null|string $id
	 * @param int $type
	 */
	public function __construct(string $name, string $id = null, int $type = self::PUBLIC){
		$this->name = $name;
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
