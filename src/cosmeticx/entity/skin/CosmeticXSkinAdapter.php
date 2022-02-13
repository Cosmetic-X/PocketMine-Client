<?php
/*
 * Copyright (c) Jan Sohn
 * All rights reserved.
 * This plugin is under GPL license
 */
declare(strict_types=1);
namespace cosmeticx\entity\skin;
use pocketmine\entity\Skin;
use pocketmine\network\mcpe\convert\LegacySkinAdapter;
use pocketmine\network\mcpe\convert\SkinAdapter;
use pocketmine\network\mcpe\protocol\types\skin\SkinData;


/**
 * Class CosmeticXSkinAdapter
 * @package cosmeticx\entity\skin
 * @author Jan Sohn / xxAROX
 * @date 12. Februar, 2022 - 20:35
 * @ide PhpStorm
 * @project PocketMine-Client
 */
class CosmeticXSkinAdapter extends LegacySkinAdapter implements SkinAdapter{
	/** @var SkinData[] */
	private array $personaSkins = [];

	/**
	 * Function fromSkinData
	 * @param SkinData $data
	 * @return Skin
	 */
	public function fromSkinData(SkinData $data): Skin{
		if ($data->isPersona()) {
			$id = $data->getSkinId();
			$this->personaSkins[$id] = $data;
			return new Skin($id, str_repeat(random_bytes(3) . "\xff", 2048));
		}
		return parent::fromSkinData($data);
	}

	/**
	 * Function toSkinData
	 * @param Skin $skin
	 * @return SkinData
	 */
	public function toSkinData(Skin $skin): SkinData{
		return $this->personaSkins[$skin->getSkinId()] ?? parent::toSkinData($skin);
	}
}
