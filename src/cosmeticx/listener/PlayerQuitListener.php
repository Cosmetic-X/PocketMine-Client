<?php

namespace cosmeticx\listener;

use cosmeticx\ApiRequest;
use cosmeticx\CosmeticManager;
use cosmeticx\CosmeticX;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\player\XboxLivePlayerInfo;
use function var_dump;

class PlayerQuitListener implements Listener {

    /**
     * Function PlayerQuitEvent
     * @param PlayerQuitEvent $event
     * @return void
     * @priority MONITOR
     */
    public function PlayerQuitEvent(PlayerQuitEvent $event): void{
        unset(CosmeticManager::getInstance()->legacy[$event->getPlayer()->getPlayerInfo()->getUsername()]);

        $playerInfo = $event->getPlayer()->getPlayerInfo();
        if (!$playerInfo instanceof XboxLivePlayerInfo) {
            CosmeticX::getInstance()->getLogger()->emergency("Please install WD_LoginDataFix, can be download here https://github.com/xxAROX/WaterdogPE-LoginExtras-Fix/releases/download/latest/WD_LoginDataFix.phar");
        } else {
            CosmeticX::sendRequest(new ApiRequest("/users/cosmetics/{$playerInfo->getXuid()}", [], true), function (array $data) use ($event): void{
                var_dump($data);
                //TODO: store player cosmetics
                CosmeticManager::getInstance()->deleteSession($event->getPlayer()->getName());
            });
        }
    }
}