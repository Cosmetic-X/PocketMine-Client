<?php
/*
 * Copyright (c) Jan Sohn
 * All rights reserved.
 * Only people with the explicit permission from Jan Sohn are allowed to modify, share or distribute this code.
 *
 * You are NOT allowed to do any kind of modification to this plugin.
 * You are NOT allowed to share this plugin with others without the explicit permission from Jan Sohn.
 * You are NOT allowed to run this plugin on your server as source code.
 * You MUST acquire this plugin from official sources.
 * You MUST run this plugin on your server as compiled .phar file from our releases.
 */
use cosmeticx\CosmeticXAPI;
use cosmeticx\generic\presence\DiscordRichPresence;


/**
 * Function setUserPresence
 * @param DiscordRichPresence $presence
 * @return void
 */
function setUserPresence(DiscordRichPresence $presence) {
	CosmeticXAPI::setPresence($presence);
}