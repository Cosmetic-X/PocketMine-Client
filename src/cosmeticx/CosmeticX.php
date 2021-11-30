<?php

namespace cosmeticx;

use pocketmine\plugin\PluginBase;
use Settings;

class CosmeticX extends PluginBase {

    /** @var CosmeticX  */
    private static CosmeticX $instance;

    public function onEnable(){
        self::$instance = $this;
        $this->saveResource("config.json");
        Settings::load();
    }

    /**
     * @return CosmeticX
     */
    public static function getInstance(): CosmeticX{
        return self::$instance;
    }
}