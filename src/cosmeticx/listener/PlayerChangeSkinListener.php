<?php

namespace cosmeticx\listener;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChangeSkinEvent;

class PlayerChangeSkinListener implements Listener {

    /**
     * Function PlayerChangeSkinEvent
     * @param PlayerChangeSkinEvent $event
     * @return void
     * @priority MONITOR
     */
    public function PlayerChangeSkinEvent(PlayerChangeSkinEvent $event): void{
        $event->cancel();
        $event->getPlayer()->sendMessage("§cSkin changing is not implemented yet.");
    }
}