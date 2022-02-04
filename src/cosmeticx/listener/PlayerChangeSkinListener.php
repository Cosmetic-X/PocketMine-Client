<?php

namespace cosmeticx\listener;

use cosmeticx\CosmeticX;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChangeSkinEvent;
use pocketmine\utils\TextFormat;

class PlayerChangeSkinListener implements Listener {

    /**
     * Function PlayerChangeSkinEvent
     * @param PlayerChangeSkinEvent $event
     * @return void
     * @priority MONITOR
     */
    public function PlayerChangeSkinEvent(PlayerChangeSkinEvent $event): void{
        $event->cancel();
        $event->getPlayer()->sendMessage(CosmeticX::PREFIX.TextFormat::RED."Skin changing is not implemented yet.");
    }
}