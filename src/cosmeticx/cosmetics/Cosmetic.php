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
	private string $id;
	private string $name;
	private string $display_name;
	private ?string $owner;
	private ?Image $image;
	private string $creator;

	/**
	 * Cosmetic constructor.
	 * @param string $id
	 * @param string $name
	 * @param string $display_name
	 * @param string $creator
	 * @param null|string $owner
	 * @param null|Image $image
	 */
	public function __construct(string $id, string $name, string $display_name, string $creator, ?string $owner = null, ?Image $image = null){
		$this->id = $id;
		$this->name = $name;
		$this->display_name = $display_name;
		$this->image = $image;
		$this->creator = $creator;
		$this->owner = $owner;
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
	 * Function getOwner
	 * @return ?string
	 */
	public function getOwner(): ?string{
		return $this->owner;
	}

	/**
	 * Function getCreator
	 * @return string
	 */
	public function getCreator(): string{
		return $this->creator;
	}
}
