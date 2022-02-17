<?php
/*
 * Copyright (c) Jan Sohn
 * All rights reserved.
 * This plugin is under GPL license
 */

namespace cosmeticx;
use Closure;
use cosmeticx\command\CosmeticXCommand;
use cosmeticx\task\async\SendRequestAsyncTask;
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
	private static string $URL = "cosmetic-x.be";
	static string $URL_API;

	private string $token = "TOKEN HERE";
	private string $holder = "n/a";
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
		$this->saveDefaultConfig();
		$this->saveResource("TOKEN.txt");
		CosmeticX::$PROTOCOL = $this->getConfig()->get("protocol", CosmeticX::$PROTOCOL);
		CosmeticX::$URL = $this->getConfig()->get("host", CosmeticX::$URL);
		$port = $this->getConfig()->get("port", "");
		CosmeticX::$URL_API = CosmeticX::$PROTOCOL . "://" . CosmeticX::$URL . (!empty($port) ? ":$port" : "") . "/api";
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
		CosmeticManager::getInstance()->resetSlotCosmetics();
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
		CosmeticManager::getInstance()->resetSlotCosmetics();
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
		self::sendRequest(new ApiRequest("/"), function (array $data){
			if (version_compare($data["lastest-client-version"], explode("+", $this->getDescription()->getVersion())[0]) == 1) {
				$this->getLogger()->notice("New update available. https://github.com/Cosmetic-X");
				//TODO: auto update function
			}
			$this->holder = $data["holder"] ?? "n/a";
			if ($this->holder == "n/a") {
				$this->getLogger()->alert("Token is not valid.");
			} else {
				$this->getLogger()->notice("Logged in as " . $this->holder);
				$this->loadCosmetics();
			}
		});
	}

	/**
	 * Function loadCosmetics
	 * @return void
	 */
	private function loadCosmetics(): void{
		$request = new ApiRequest("/available-cosmetics", [], true);
		self::sendRequest($request, function (array $data){
			foreach ($data as $where => $objs) {
				foreach ($objs as $obj) {
					if ($where === "public") {
						CosmeticManager::getInstance()->registerPublicCosmetics($obj["name"], $obj["display_name"], $obj["id"], (isset($obj["image"]) ? new Image($obj["image"], str_starts_with($obj["image"], "http") ? Image::TYPE_URL : Image::TYPE_PATH) : null));
					} else if ($where === "slot") {
						CosmeticManager::getInstance()->registerSlotCosmetic($obj["name"], $obj["display_name"], $obj["id"], (isset($obj["image"]) ? new Image($obj["image"], str_starts_with($obj["image"], "http") ? Image::TYPE_URL : Image::TYPE_PATH) : null));
					}
				}
			}
			$publicCosmetics = count(CosmeticManager::getInstance()->getPublicCosmetics());
			if ($publicCosmetics > 0) {
				$this->getLogger()->debug("Loaded " . $publicCosmetics . ($publicCosmetics == 1 ? " public-cosmetic"
						: " public-cosmetics"));
			}
			$slotCosmetics = count(CosmeticManager::getInstance()->getPublicCosmetics());
			if ($slotCosmetics > 0) {
				$this->getLogger()->debug("Loaded " . $slotCosmetics . ($slotCosmetics == 1 ? " server-cosmetic"
						: " server-cosmetics"));
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