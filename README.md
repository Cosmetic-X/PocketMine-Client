# Cosmetic-X

## How to use
### First, if you send Spam, you will be automatic-blocked, if you got rate-limited because your player count is too huge please create a ticket on out [Discord](https://discord.cosmetic-x.de).

```php
<?php
use cosmeticx\generic\presence\DiscordRichPresence;

/**
 * @var int $players_online
 * @var int $max_players
 * @var \pocketmine\player\Player $player 
 */;
$ends_at_presence = DiscordRichPresence::endsAt($player, "network.com", "A Cosmetic-X Server", 20); //NOTE: 20 seconds

$presences = [
    # Default Presence, uses the values from ./resources/config.yml
    DiscordRichPresence::default($player);,
    
    # Normal Presence, Params: [$player, "Network name", "Server name"]
    DiscordRichPresence::normal($player, "network.com", "A Cosmetic-X Server (" . $players_online . " / " . $max_players . ")"),
    
    # Ending Presence, Params: [$player, "Network name", "Server name", "20 seconds, after that it should send a new presence, if not then the default presence will be sent"]
    DiscordRichPresence::endsAt($player, "network.com", "A Cosmetic-X Server (" . $players_online . " / " . $max_players . ")", 20); //NOTE: 20 seconds
];
# set presence
setUserPresence($presences[array_rand($presences)]);

# Clear presence(if player quits the server)
DiscordRichPresence::clear($player);
```

We are using <a href="https://github.com/vezdehod">vezdehod</a> / <a href="https://github.com/vezdehod/VPacks">VPacks</a>


[![ko-fi](https://ko-fi.com/img/githubbutton_sm.svg)](https://ko-fi.com/cosmeticx)
