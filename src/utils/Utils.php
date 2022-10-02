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
namespace cosmeticx\utils;
use Ahc\Json\Comment;
use cosmeticx\CosmeticX;
use GlobalLogger;
use pocketmine\entity\Skin;
use pocketmine\player\PlayerInfo;
use pocketmine\player\XboxLivePlayerInfo;


/**
 * Class Utils
 * @package cosmeticx\utils
 * @author Jan Sohn / xxAROX
 * @date 12. Dezember, 2021 - 17:26
 * @ide PhpStorm
 * @project PocketMine-Client
 * @internal
 */
class Utils{
	/**
	 * Function saveSkinData
	 * @param string $filename
	 * @param string $skinData
	 * @return void
	 * @internal
	 */
	static function saveSkinData(string $filename, string $skinData): void{
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
			if (!isset($color[0])) {
				$color = [0, 0, 0, 127];
			} else if (!isset($color[1])) {
				$color = array_merge($color, [0, 0, 127]);
			} else if (!isset($color[2])) {
				$color = array_merge($color, [0, 127]);
			} else if (!isset($color[3])) {
				$color = array_merge($color, [127]);
			}
			imagesetpixel($image, $x, $y, imagecolorallocatealpha($image, ...$color));
			$position--;
		}
		@imagepng($image, CosmeticX::getInstance()->getDataFolder() . $filename . ".png");
	}

	/**
	 * Function encodeSkinData
	 * @param string $skinData
	 * @return string
	 * @internal
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
			if (!isset($color[0])) {
				$color = [0, 0, 0, 127];
			} else if (!isset($color[1])) {
				$color = array_merge($color, [0, 0, 127]);
			} else if (!isset($color[2])) {
				$color = array_merge($color, [0, 127]);
			} else if (!isset($color[3])) {
				$color = array_merge($color, [127]);
			}
			imagesetpixel($image, $x, $y, imagecolorallocatealpha($image, ...$color));
			$position--;
		}
		$filename = tempnam(CosmeticX::getInstance()->getDataFolder(), "Skin");
		@imagepng($image, $filename);
		@imagedestroy($image);
		$image_data = file_get_contents($filename);
		if (is_file($filename)) {
			unlink($filename);
		}
		return base64_encode($image_data);
	}

	/**
	 * Function decodeSkinData
	 * @param string $raw
	 * @return string
	 * @internal
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

	/**
	 * Function checkForXuid
	 * @param PlayerInfo $playerInfo
	 * @return bool
	 * @internal
	 */
	static function checkForXuid(PlayerInfo $playerInfo): bool{
		if (!($found = $playerInfo instanceof XboxLivePlayerInfo)) {
			GlobalLogger::get()->warning("No XUID found, please enable XBOX-Live auth");
			GlobalLogger::get()->warning("If you are using WaterdogPE, then please install WD_LoginDataFix, can be download here https://github.com/xxAROX/WaterdogPE-LoginExtras-Fix/releases/download/latest/WD_LoginDataFix.phar");
		}
		return $found;
	}

	/**
	 * Function json_decode
	 * @param string $json
	 * @param false $assoc
	 * @param int $depth
	 * @param int $flags
	 * @return mixed
	 * @internal
	 */
	static function json_decode(string $json, bool $assoc = false, int $depth = 512, int $flags = 0): mixed{
		if (empty($json)) {
			$json = "[]";
		}
		return Comment::parse($json, $assoc, $depth, $flags);
		//return json_decode(preg_replace("#(/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/)|([\s\t]//.*)|(^//.*)#", '', $json), $assoc, $depth, $flags);
	}

	/**
	 * Function fixSkinSizeForUs
	 * @param Skin $skin
	 * @return Skin
	 * @throws \JsonException
	 * @internal
	 */
	static function fixSkinSizeForUs(Skin $skin): Skin{
		// BLAME: MOJANG BEGIN
		if ($skin->getGeometryData() !== "null") {
			var_dump("format_version: " . (Utils::json_decode($skin->getGeometryData(), true)["format_version"] ?? null));
		}
		if (isset(CosmeticX::$defaultGeometry[$skin->getGeometryName()])) {
			$geometryData = json_encode(CosmeticX::$defaultGeometry[$skin->getGeometryName()]);
		} else {
			$geometryData = $skin->getGeometryData();
		}
		// BLAME: MOJANG END
		if (strlen($skin->getSkinData()) != (64 * 32 * 4)) {
			$height = $width = intval(sqrt(strlen($skin->getSkinData())) / 2);
		} else {
			$height = 32;
			$width = 64;
		}
		$pixelarray = str_split(bin2hex($skin->getSkinData()), 8);
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
			$alpha = array_pop($color);
			$alpha = ((~((int)$alpha)) & 0xff) >> 1;
			array_push($color, $alpha);
			if (!isset($color[0])) {
				$color = [0, 0, 0, 127];
			} else if (!isset($color[1])) {
				$color = array_merge($color, [0, 0, 127]);
			} else if (!isset($color[2])) {
				$color = array_merge($color, [0, 127]);
			} else if (!isset($color[3])) {
				$color = array_merge($color, [127]);
			}
			imagesetpixel($image, $x, $y, imagecolorallocatealpha($image, ...$color));
			$position--;
		}
		$oldw = imagesx($image);
		$oldh = imagesy($image);
		$temp = imagecreatetruecolor(64, 64);
		imagecopyresampled($temp, $image, 0, 0, 0, 0, 64, 64, $oldw, $oldh);

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
		return new Skin($skin->getSkinId(), $bytes, $skin->getCapeData(), $skin->getGeometryName(), $geometryData);
	}
}
