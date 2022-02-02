<?php
/*
 * Copyright (c) Jan Sohn
 * All rights reserved.
 * This plugin is under GPL license
 */

declare(strict_types=1);
namespace cosmeticx\utils;
use cosmeticx\CosmeticX;


/**
 * Class Utils
 * @package cosmeticx\utils
 * @author Jan Sohn / xxAROX
 * @date 12. Dezember, 2021 - 17:26
 * @ide PhpStorm
 * @project PocketMine-Client
 */
class Utils{
	/**
	 * Function encodeSkinData
	 * @param string $skinData
	 * @return string
	 */
	static function encodeSkinData(string $skinData): string{
		if (strlen($skinData) != (64 * 32 * 4)) {
			$height = $width = intval(sqrt(strlen($skinData)) / 2);
		} else {
			$height = 32;
			$width = 64;
		}
		$pixelarray = str_split(bin2hex($skinData), 8);
		$image = imagecreatetruecolor($width, $height);
		imagealphablending($image, false);//do not touch
		imagefill($image, 0, 0, imagecolorallocatealpha($image, 0, 0, 0, 127));
		imagesavealpha($image, true);
		$position = count($pixelarray) - 1;
		while (!empty($pixelarray)) {
			$x = $position % $width;
			$y = ($position - $x) / $height;
			$walkable = str_split(array_pop($pixelarray), 2);
			$color = array_map(function ($val){
				return hexdec($val);
			}, $walkable);
			$alpha = array_pop($color); // equivalent to 0 for imagecolorallocatealpha()
			$alpha = ((~((int)$alpha)) & 0xff) >> 1; // back = (($alpha << 1) ^ 0xff) - 1
			array_push($color, $alpha);
			imagesetpixel($image, $x, $y, imagecolorallocatealpha($image, ...$color));
			$position--;
		}
		@imagepng($image, CosmeticX::getInstance()->getDataFolder() . ".temp");
		@imagedestroy($image);
		$image_data = file_get_contents(CosmeticX::getInstance()->getDataFolder() . ".temp");
		unlink(CosmeticX::getInstance()->getDataFolder() . ".temp");
		return base64_encode($image_data);
	}

	/**
	 * Function decodeSkinData
	 * @param string $raw
	 * @return string
	 */
	static function decodeSkinData(string $raw): string{
		$image = imagecreatefromstring(base64_decode($raw));
		$bytes = "";
		for ($y = 0; $y < imagesy($image); $y++) {
			for ($x = 0; $x < imagesx($image); $x++) {
				$rgba = @imagecolorat($image, $x, $y);
				$a = ((~((int)($rgba >> 24))) << 1) & 0xff;
				$r = ($rgba >> 16) & 0xff;
				$g = ($rgba >> 8) & 0xff;
				$b = $rgba & 0xff;
				$bytes .= chr($r) . chr($g) . chr($b) . chr($a);
			}
		}
		@imagedestroy($image);
		return $bytes;
	}
}
