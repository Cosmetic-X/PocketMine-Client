<?php
/*
 * Copyright (c) 2021. Jan Sohn.
 * All rights reserved.
 * I don't want anyone to use my source code without permission.
 */

namespace cosmeticx;
use cosmeticx\command\CosmeticXCommand;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginDescription;
use pocketmine\plugin\PluginLoader;
use pocketmine\plugin\ResourceProvider;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\Internet;
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


	const         IS_DEVELOPMENT = true;
	private const URL            = "https://cosmetic-x.be";
	const         URL_API        = self::URL . "/api";
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
		parent::__construct($loader, $server, $description, $dataFolder, $file, $resourceProvider);
		self::setInstance($this);
		$this->command = new CosmeticXCommand();
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
		$this->getServer()->getCommandMap()->register("cosmeticx", $this->command);
		$request = new ApiRequest($this->token, function (array $data){
			foreach ($data as $id => $name) {
				CosmeticManager::getInstance()->registerCosmetic($id, $name);
			}
			$cosmetics = count(CosmeticManager::getInstance()->getCosmetics());
			if ($cosmetics > 0) {
				$this->getLogger()->debug("Loaded " . $cosmetics . ($cosmetics == 1 ? " cosmetic" : " cosmetics"));
			}
		});
		self::sendRequest("/available-cosmetics", $request);
	}

	/**
	 * Function sendRequest
	 * @param string $uri
	 * @param ApiRequest $request
	 * @return void
	 */
	public static function sendRequest(string $uri, ApiRequest $request): void{
		Server::getInstance()->getAsyncPool()->submitTask(new class($uri, $request) extends AsyncTask{
			public function __construct(private string $uri, private ApiRequest $request){
			}

			public function onRun(): void{
				$result = Internet::postURL(CosmeticX::URL_API . $this->uri, $this->request->getData(), 10, $this->request->getHeaders(), $err);
				if ($err) {
					throw $err;
				}
				$this->setResult($result ?? null);
			}

			public function onCompletion(): void{
				if (!is_null($result = $this->getResult())) {
					$this->request->response($result);
				}
			}
		});
	}

	/**
	 * Function registerPermissions
	 * @return void
	 */
	private function registerPermissions(): void{
		$overlord = new Permission("cosmeticx.*", "Overlord permission");
		foreach ($this->command->getSubCommands() as $subCommand) {
			if (!is_null($subCommand->getPermission())) {
				$permission = $this->command->getPermission() . "." . $subCommand->getPermission();
				PermissionManager::getInstance()->addPermission(new Permission($permission, "Allows to use the '/{$this->command->getName()} {$subCommand->getName()}' command."));
				$overlord->addChild($permission, true);
			}
		}
	}
}