<?php

namespace cosmeticx\listener;

use cosmeticx\CosmeticManager;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCreationEvent;

class PlayerCreationListener implements Listener {

    /**
     * Function PlayerCreationEvent
     * @param PlayerCreationEvent $event
     * @return void
     * @priority MONITOR
     */
    public function PlayerCreationEvent(PlayerCreationEvent $event): void{
        CosmeticManager::getInstance()->legacy[$event->getNetworkSession()->getPlayerInfo()->getUsername()] = $event->getNetworkSession()->getPlayerInfo()->getSkin();
    }
}