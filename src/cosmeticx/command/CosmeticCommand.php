<?php

namespace cosmeticx\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class CosmeticCommand extends Command {
    
    public function __construct(){
        parent::__construct("cosmeticx", "Main command for CosmeticX", "", ["cx"]);
    }
    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if(!$sender instanceof Player) return;
    }
}