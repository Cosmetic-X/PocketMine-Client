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
 * You MUST run this plugin on your server as compiled .phar file from our releases.
 */
declare(strict_types=1);
namespace cosmeticx\cosmetics;
use xxAROX\forms\elements\Image;


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
	private string $category;

	/**
	 * Cosmetic constructor.
	 * @param string $id
	 * @param string $category
	 * @param string $name
	 * @param string $display_name
	 * @param string $creator
	 * @param null|string $owner
	 * @param null|Image $image
	 */
	public function __construct(string $id, string $category, string $name, string $display_name, string $creator, ?string $owner = null, ?Image $image = null){
		$this->id = $id;
		$this->name = $name;
		$this->category = $category;
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

	/**
	 * Function getCategory
	 * @return string
	 */
	public function getCategory(): string{
		return $this->category;
	}
}
