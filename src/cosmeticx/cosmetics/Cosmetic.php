<?php
/*
 * Copyright (c) Jan Sohn
 * All rights reserved.
 * This plugin is under GPL license
 */
declare(strict_types=1);
namespace cosmeticx\cosmetics;
use Frago9876543210\EasyForms\elements\Image;
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

	private string $id;
	private string $name;
	private string $display_name;
	private ?Image $image;
	private bool $public;

	/**
	 * Cosmetic constructor.
	 * @param string $name
	 * @param string $display_name
	 * @param null|string $id
	 * @param null|Image $image
	 * @param int $type
	 */
	public function __construct(string $name, string $display_name, string $id = null, ?Image $image = null, int $type = self::PUBLIC){
		$this->id = $id ?? Uuid::uuid4()->toString();
		$this->name = $name;
		$this->display_name = $display_name;
		$this->image = $image;
		$this->public = $type == self::PUBLIC;
	}

	/**
	 * Function getId
	 * @return string
	 */
	public function getId(): string{
		return $this->id;
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
	 * Function getImage
	 * @return ?Image
	 */
	public function getImage(): ?Image{
		return $this->image;
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
}
