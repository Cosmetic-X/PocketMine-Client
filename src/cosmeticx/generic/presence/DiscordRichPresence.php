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
namespace cosmeticx\generic\presence;
use cosmeticx\ApiRequest;
use cosmeticx\CosmeticX;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use JsonSerializable;
use pocketmine\player\Player;


/**
 * Class DiscordRichPresence
 * @package cosmeticx\generic\presence
 * @author Jan Sohn / xxAROX
 * @date 02. May 2022 - 13:12
 * @ide PhpStorm
 * @project PocketMine-Client
 */
class DiscordRichPresence implements JsonSerializable{
	private Player $holder;
	private ?string $network, $server;
	private ?int $ends_at;

	/**
	 * Function normal
	 * @param Player $player
	 * @return DiscordRichPresence
	 */
	#[Pure] public static function default(Player $player): DiscordRichPresence{
		return new DiscordRichPresence($player, CosmeticX::$NETWORK, CosmeticX::$SERVER, null);
	}

	/**
	 * Function normal
	 * @param Player $player
	 * @param null|string $network
	 * @param null|string $server
	 * @return DiscordRichPresence
	 */
	#[Pure] public static function normal(Player $player, string $network = null, string $server = null): DiscordRichPresence{
		return new DiscordRichPresence($player, $network, $server, null);
	}
	/**
	 * Function ends_at
	 * @param Player $player
	 * @param int $ends_at
	 * @param null|string $network
	 * @param null|string $server
	 * @return DiscordRichPresence
	 */
	#[Pure] public static function ends_at(Player $player, int $ends_at, string $network = null, string $server = null): DiscordRichPresence{
		return new DiscordRichPresence($player, $network, $server, $ends_at);
	}
	/**
	 * Function clear
	 * @param Player $holder
	 * @return void
	 */
	public static function clear(Player $holder): void{
		setUserPresence(new DiscordRichPresence($holder, "", "", null));
	}

	/**
	 * DiscordRichPresence constructor.
	 * @param Player $holder
	 * @param null|string $network
	 * @param null|string $server
	 * @param null|int $ends_at
	 */
	protected function __construct(Player $holder, string $network = null, string $server = null, int $ends_at = null){
		$this->holder = $holder;
		$this->network = $network ?? CosmeticX::$NETWORK;
		$this->server = $server ?? CosmeticX::$SERVER;
		$this->ends_at = $ends_at;
	}

	/**
	 * Function jsonSerialize
	 * @return array
	 */
	#[Pure] #[ArrayShape([
		"gamertag" => "string",
		"network"  => "null|string",
		"server"   => "null|string",
		"ends_at"  => "int|null",
	])] public function jsonSerialize(): array{
		$arr = [
			"gamertag" => $this->holder->getName(),
			"network"  => $this->network,
			"server"   => $this->server,
		];
		if (!is_null($this->ends_at)) {
			$arr["ends_at"] = $this->ends_at;
		}
		return $arr;
	}

	/**
	 * Function getHolder
	 * @return Player
	 */
	public function getHolder(): Player{
		return $this->holder;
	}

	/**
	 * Function getServer
	 * @return string
	 */
	public function getServer(): string{
		return $this->server;
	}

	/**
	 * Function setServer
	 * @param string $server
	 * @return void
	 */
	public function setServer(string $server): void{
		$this->server = $server;
	}

	/**
	 * Function getNetwork
	 * @return string
	 */
	public function getNetwork(): string{
		return $this->network;
	}

	/**
	 * Function setNetwork
	 * @param string $network
	 * @return void
	 */
	public function setNetwork(string $network): void{
		$this->network = $network;
	}

	/**
	 * Function getEndsAt
	 * @return ?int
	 */
	public function getEndsAt(): ?int{
		return $this->ends_at;
	}

	/**
	 * Function setEndsAt
	 * @param null|int $seconds_left
	 * @return void
	 */
	public function setEndsAt(?int $seconds_left): void{
		$this->ends_at = $seconds_left;
	}
}
