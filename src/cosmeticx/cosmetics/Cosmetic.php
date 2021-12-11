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
	private string $name;
	private bool $local;
	private string $id;

	/**
	 * Cosmetic constructor.
	 * @param string $name
	 * @param null|string $id
	 */
	public function __construct(string $name, string $id = null){
		$this->name = $name;
		$this->local = is_null($id);
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
	 * Function getLocal
	 * @return bool
	 */
	public function isLocal(): bool{
		return $this->local;
	}

	/**
	 * Function getId
	 * @return string
	 */
	public function getId(): string{
		return $this->id;
	}
}
