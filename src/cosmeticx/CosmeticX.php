<?php
/*
 * Copyright (c) 2021. Jan Sohn.
 * All rights reserved.
 * I don't want anyone to use my source code without permission.
 */

namespace cosmeticx;
use Closure;
use cosmeticx\command\CosmeticXCommand;
use cosmeticx\command\subcommand\HelpSubCommand;
use cosmeticx\task\async\SendRequestAsyncTask;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginDescription;
use pocketmine\plugin\PluginLoader;
use pocketmine\plugin\ResourceProvider;
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


	const         IS_DEVELOPMENT = false;

	private static string $PROTOCOL = "https";
	private static string $URL = "cosmetic-x.be";
	static string $URL_API;

	private string $token = "";
	public CosmeticXCommand $command;

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
		$this->command = new CosmeticXCommand();
		$this->command->loadSubCommand(new HelpSubCommand("help", ["?"]));
	}

	/**
	 * Function onLoad
	 * @return void
	 */
	protected function onLoad(): void{
		if (!str_starts_with(\Phar::running(), "phar://") && !self::IS_DEVELOPMENT) {
			$this->getLogger()->error("This plugin cannot be run via source-code");
			$this->getServer()->getPluginManager()->disablePlugin($this);
			return;
		}
		$this->saveDefaultConfig();
		CosmeticX::$PROTOCOL = $this->getConfig()->get("protocol", CosmeticX::$PROTOCOL);
		CosmeticX::$URL = $this->getConfig()->get("host", CosmeticX::$URL);
		$port = $this->getConfig()->get("port", "");
		CosmeticX::$URL_API = CosmeticX::$PROTOCOL . "://" . CosmeticX::$URL . (!empty($port) ? ":$port" : "") . "/api";

		if (empty($this->token)) {
			$this->saveResource("TOKEN.txt");
			if (!file_exists($this->getDataFolder() . "TOKEN.txt")) {
				file_put_contents($this->getDataFolder() . "TOKEN.txt", "TOKEN HERE");
			}
			$this->token = file_get_contents($this->getDataFolder() . "TOKEN.txt");
		}
	}

	/**
	 * Function onEnable
	 * @return void
	 */
	protected function onEnable(): void{
		$this->registerPermissions();
		$this->getServer()->getPluginManager()->registerEvents(new Listener(), $this);
		$this->getServer()->getCommandMap()->register("cosmeticx", $this->command);
		$this->check();
	}

	/**
	 * Function check
	 * @return void
	 */
	private function check(): void{
		$request = new ApiRequest("/", ["version" => $this->getDescription()->getVersion()]);
		self::sendRequest($request, function (array $data){
			var_dump($data);
			$this->loadCosmetics();
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
					var_dump($obj);
					if ($where === "public") {
						CosmeticManager::getInstance()->registerPublicCosmetics($obj["id"], $obj["display_name"], $obj["name"]);
					} else if ($where === "slot") {
						CosmeticManager::getInstance()->registerSlotCosmetic($obj["id"], $obj["display_name"], $obj["name"]);
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
		$overlord = new Permission("cosmetic-x.*", "Overlord permission");
		foreach ($this->command->getSubCommands() as $subCommand) {
			if (!is_null($subCommand->getPermission())) {
				$permission = $this->command->getPermission() . "." . $subCommand->getPermission();
				PermissionManager::getInstance()->addPermission(new Permission($permission, "Allows to use the '/{$this->command->getName()} {$subCommand->getName()}' command."));
				$overlord->addChild($permission, true);
			}
		}
		PermissionManager::getInstance()->addPermission($overlord);
	}
}