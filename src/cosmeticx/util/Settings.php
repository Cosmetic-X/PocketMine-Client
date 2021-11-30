<?php

use cosmeticx\CosmeticX;
use pocketmine\utils\Config;

class Settings {

    public const DEVELOPMENT_BUILD = true;

    /** @var string|null  */
    private static ?string $apiToken = null;
    /** @var string  */
    private static string $protocol = "https://";
    /** @var string  */
    private static string $backendAddress;
    /** @var Config  */
    private static Config $config;

    public static function load(){
        $config = new Config(CosmeticX::getInstance()->getDataFolder()."config.json");
        self::$config = $config;

        self::$protocol = $config->get("protocol");
        self::$apiToken = $config->get("token");
        self::$backendAddress = $config->get("backend", "cosmetic-x.be/api/");
        if(strlen(self::$apiToken) <= 0) {
            CosmeticX::getInstance()->getLogger()->error("Please paste your api-token into the config.json and restart your server!");
            CosmeticX::getInstance()->getServer()->getPluginManager()->disablePlugin(CosmeticX::getInstance());
            return;
        }
    }

    /**
     * @return string
     */
    public static function getProtocol(): string{
        return self::$protocol;
    }

    /**
     * @return string
     */
    public static function getBackendAddress(): string{
        return self::$backendAddress;
    }

    /**
     * @return string|null
     */
    public static function getApiToken(): ?string{
        return self::$apiToken;
    }

    /**
     * @return Config
     */
    public static function getConfig(): Config{
        return self::$config;
    }
}