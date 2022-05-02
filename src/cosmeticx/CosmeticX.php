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

namespace cosmeticx;
use Closure;
use cosmeticx\command\CosmeticXCommand;
use cosmeticx\task\async\SendRequestAsyncTask;
use cosmeticx\utils\Utils;
use Frago9876543210\EasyForms\elements\Image;
use Phar;
use pocketmine\network\mcpe\convert\SkinAdapter;
use pocketmine\network\mcpe\convert\SkinAdapterSingleton;
use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginDescription;
use pocketmine\plugin\PluginLoader;
use pocketmine\plugin\ResourceProvider;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskHandler;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;


/**
 * Class CosmeticX
 * @package cosmeticx
 * @author Jan Sohn / xxAROX
 * @date 11. Dezember, 2021 - 19:22
 * @ide PhpStorm
 * @project PocketMine-Client
 */
class CosmeticX extends PluginBase{
	use SingletonTrait;


	private static string $PROTOCOL = "https";
	private static string $URL = "cosmetic-x.de";
	static string $URL_API;
	static bool $SHOW_LOCKED_COSMETICS = false;
	static bool $ENABLE_RICH_PRESENCE = false;
	static ?string $NETWORK = null;
	static ?string $SERVER = "PocketMine-MP Server";
	static bool $IS_WATERDOG_ENABLED = false;
	static array $defaultGeometry = [];

	private string $token = "TOKEN HERE";
	private string $holder = "n/a";
	private string $team = "n/a";
	private CosmeticXCommand $command;
	public ?TaskHandler $refresh_interval = null;
	/** @var Permission[] */
	private array $permissions = [];
	private SkinAdapter $legacy_skin_adapter;

	/**
	 * CosmeticX constructor.
	 * @param PluginLoader $loader
	 * @param Server $server
	 * @param PluginDescription $description
	 * @param string $dataFolder
	 * @param string $file
	 * @param ResourceProvider $resourceProvider
	 */
	public function __construct(PluginLoader $loader, Server $server, PluginDescription $description, string $dataFolder, string $file, ResourceProvider $resourceProvider){
		self::$URL_API = self::$PROTOCOL . "://" . self::$URL . "/api";
		parent::__construct($loader, $server, $description, $dataFolder, $file, $resourceProvider);
		self::setInstance($this);
		PermissionManager::getInstance()->addPermission(new Permission("cosmetic-x.command", "Allows to use the '/cosmeticx' command."));
	}

	/**
	 * Function onLoad
	 * @return void
	 */
	protected function onLoad(): void{
		if (!str_starts_with(Phar::running(), "phar://")) {
			$this->getLogger()->error("This plugin cannot be run via source-code");
			$this->getServer()->getPluginManager()->disablePlugin($this);
			return;
		}
		include_once __DIR__ . "/functions.php";
		$this->saveDefaultConfig();
		$this->saveResource("TOKEN.txt");
		$this->saveResource("geometry.json");
		CosmeticX::$PROTOCOL = $this->getConfig()->get("protocol", CosmeticX::$PROTOCOL);
		CosmeticX::$URL = $this->getConfig()->get("host", CosmeticX::$URL);
		$port = $this->getConfig()->get("port", "");
		CosmeticX::$URL_API = CosmeticX::$PROTOCOL . "://" . CosmeticX::$URL . (!empty($port) ? ":$port" : "") . "/api";
		CosmeticX::$SHOW_LOCKED_COSMETICS = $this->getConfig()->get("show_locked_cosmetics", true);
		CosmeticX::$IS_WATERDOG_ENABLED = $this->getConfig()->get("enable_waterdog_support", false);
		CosmeticX::$ENABLE_RICH_PRESENCE = $this->getConfig()->get("enable-rich-presence", false);
		CosmeticX::$NETWORK = $this->getConfig()->get("network", "???");
		CosmeticX::$SERVER = $this->getConfig()->get("server", "???");
		CosmeticX::$defaultGeometry = Utils::json_decode(file_get_contents($this->getDataFolder() . "geometry.json"), true);
		$this->token = file_get_contents($this->getDataFolder() . "TOKEN.txt");
	}

	/**
	 * Function onEnable
	 * @return void
	 */
	protected function onEnable(): void{
		$this->getServer()->getPluginManager()->registerEvents(new Listener(), $this);
		$this->getServer()->getCommandMap()->register("cosmeticx", $this->command = new CosmeticXCommand());
		$this->legacy_skin_adapter = SkinAdapterSingleton::get();
		//SkinAdapterSingleton::set(new CosmeticXSkinAdapter());
		$this->registerPermissions();
		$this->getScheduler()->scheduleDelayedTask(new ClosureTask(fn () => $this->refresh_interval = $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(fn () => $this->refresh()), $this->getConfig()->get("refresh-interval", 300) * 20)), $this->getConfig()->get("refresh-interval", 300) * 20);
		$this->check();
	}

	protected function onDisable(): void{
		SkinAdapterSingleton::set($this->legacy_skin_adapter);
	}

	/**
	 * Function reload
	 * @return void
	 */
	public function reload(): void{
		CosmeticManager::getInstance()->resetPublicCosmetics();
		CosmeticManager::getInstance()->resetServerCosmetics();
		if (!is_null($this->refresh_interval)) {
			$this->refresh_interval->cancel();
			$this->getScheduler()->scheduleDelayedTask(new ClosureTask(fn () => $this->refresh_interval = $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(fn () => $this->refresh()), $this->getConfig()->get("refresh-interval", 300) * 20)), $this->getConfig()->get("refresh-interval", 300) * 20);
		}
		$this->reloadConfig();
		CosmeticX::$PROTOCOL = $this->getConfig()->get("protocol", CosmeticX::$PROTOCOL);
		CosmeticX::$URL = $this->getConfig()->get("host", CosmeticX::$URL);
		$port = $this->getConfig()->get("port", "");
		CosmeticX::$URL_API = CosmeticX::$PROTOCOL . "://" . CosmeticX::$URL . (!empty($port) ? ":$port" : "") . "/api";
		$this->token = file_get_contents($this->getDataFolder() . "TOKEN.txt");
		$this->getLogger()->notice("Reloaded config");
		$this->check();
	}

	/**
	 * Function refresh
	 * @return void
	 */
	private function refresh(): void{
		CosmeticManager::getInstance()->resetPublicCosmetics();
		CosmeticManager::getInstance()->resetServerCosmetics();
		$this->getLogger()->debug("Refreshed cosmetics");
		$this->loadCosmetics();
	}

	/**
	 * Function check
	 * @return void
	 */
	private function check(): void{
		if ($this->token == "TOKEN HERE") {
			$this->getLogger()->alert("Token is not set, type '/" . $this->command->getName() . " reload' if set.");
			return;
		}
		self::sendRequest(new ApiRequest(ApiRequest::$URI_CHECKOUT), function (array $data){
			if (version_compare($data["lastest-client-version"], explode("+", $this->getDescription()->getVersion())[0]) == 1) {
				$this->getLogger()->notice("New update available. https://cosmetic-x.de/downloads/PocketMine-Client/" . $data["lastest-client-version"]);
				//TODO: auto update function
			}
			$this->team = $data["team"] ?? "n/a";
			$this->holder = $data["holder"] ?? "n/a";
			if ($this->holder == "n/a" || $this->team == "n/a") {
				$this->getLogger()->alert("Token is not valid.");
			} else {
				$this->getLogger()->notice("Token is from " . $this->team . " by " . $this->holder);
				$this->loadCosmetics();
			}
		});
	}

	/**
	 * Function loadCosmetics
	 * @return void
	 */
	private function loadCosmetics(): void{
		$request = new ApiRequest("/cosmetics", [], true);
		self::sendRequest($request, function (array $data){
			var_dump($data);
			foreach ($data as $obj) {
				CosmeticManager::getInstance()->registerCosmetic((string)$obj["id"], $obj["name"], $obj["display_name"], $obj["owner"], $obj["creator"], (isset($obj["image"]) ? new Image($obj["image"], str_starts_with($obj["image"], "http") ? Image::TYPE_URL : Image::TYPE_PATH) : null));
			}
			if (($cosmetics = count(CosmeticManager::getInstance()->getPublicCosmetics())) > 0) {
				$this->getLogger()->debug("Loaded " . $cosmetics . " public-cosmetic" . ($cosmetics == 1 ? "" : "s"));
			}
			if (($cosmetics = count(CosmeticManager::getInstance()->getServerCosmetics())) > 0) {
				$this->getLogger()->debug("Loaded " . $cosmetics . " server-cosmetic" . ($cosmetics == 1 ? "" : "s"));
			}
		});
	}

	/**
	 * Function sendRequest
	 * @param ApiRequest $request
	 * @param Closure $onResponse
	 * @return void
	 */
	public static function sendRequest(ApiRequest $request, Closure $onResponse): void{
		$request->header("token", CosmeticX::getInstance()->token);
		$request->header("Cosmetic-X", "by xxAROX");
		Server::getInstance()->getAsyncPool()->submitTask(new SendRequestAsyncTask($request, $onResponse));
	}

	/**
	 * Function registerPermissions
	 * @return void
	 */
	private function registerPermissions(): void{
		unset($this->permissions);
		$this->permissions = [];
		$overlord = new Permission("cosmetic-x.*", "Overlord permission");
		foreach ($this->command->getSubCommands() as $subCommand) {
			if (!is_null($subCommand->getPermission())) {
				$permission = $this->command->getPermission() . "." . $subCommand->getPermission();
				PermissionManager::getInstance()->addPermission($this->permissions[] = new Permission($permission, "Allows to use the '/" . $this->command->getName() . " {$subCommand->getName()}' command."));
				$overlord->addChild($permission, true);
			}
		}
		foreach ($this->permissions as $permission) {
			PermissionManager::getInstance()->getPermission(DefaultPermissions::ROOT_OPERATOR)->addChild($permission->getName(), true);
			PermissionManager::getInstance()->getPermission(DefaultPermissions::ROOT_CONSOLE)->addChild($permission->getName(), true);
		}
		PermissionManager::getInstance()->addPermission($overlord);
	}

	/**
	 * Function getTeam
	 * @return string
	 */
	public function getTeam(): string{
		return $this->team;
	}

	/**
	 * Function getHolder
	 * @return string
	 */
	public function getHolder(): string{
		return $this->holder;
	}

	/**
	 * Function getCosmeticXCommand
	 * @return CosmeticXCommand
	 */
	public function getCosmeticXCommand(): CosmeticXCommand{
		return $this->command;
	}

	/**
	 * Function getPermissions
	 * @return Permission[]
	 */
	public function getPermissions(): array{
		return $this->permissions;
	}
}