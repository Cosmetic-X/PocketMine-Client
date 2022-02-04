<?php

namespace cosmeticx\listener;

use cosmeticx\ApiRequest;
use cosmeticx\CosmeticManager;
use cosmeticx\CosmeticX;
use cosmeticx\utils\Utils;
use pocketmine\entity\Skin;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\player\XboxLivePlayerInfo;
use pocketmine\utils\TextFormat;
use pocketmine\world\sound\XpLevelUpSound;

class PlayerLoginListener implements Listener {

    /**
     * Function PlayerLoginEvent
     * @param PlayerLoginEvent $event
     * @return void
     * @priority MONITOR
     */
    public function PlayerLoginEvent(PlayerLoginEvent $event): void{
        $playerInfo = $event->getPlayer()->getPlayerInfo();
        if (!$playerInfo instanceof XboxLivePlayerInfo) {
            CosmeticX::getInstance()->getLogger()->emergency("Please install WD_LoginDataFix, can be download here https://github.com/xxAROX/WaterdogPE-LoginExtras-Fix/releases/download/latest/WD_LoginDataFix.phar");
        } else {
            CosmeticX::sendRequest(new ApiRequest("/users/cosmetics/{$playerInfo->getXuid()}", ["skinData" => Utils::encodeSkinData($event->getPlayer()->getSkin()->getSkinData())]), function (array $data) use ($event): void{
                $player = $event->getPlayer();
                $session = CosmeticManager::getInstance()->getSession($player);
                $skin = $session->getHolder()->getSkin();
                $session->getHolder()->setSkin(new Skin($skin->getSkinId(), Utils::decodeSkinData($data["buffer"]), $skin->getCapeData(), $data["geometry_name"] ?? $skin->getGeometryName(), $data["geometry_data"] ?? $skin->getGeometryData()));
                $session->getHolder()->sendSkin();
                $player->getWorld()->addSound($player->getLocation()->asVector3(), new XpLevelUpSound(1), [$player]);
                $player->sendMessage(CosmeticX::PREFIX.TextFormat::GREEN."All data synchronized!");
            });
        }
    }
}