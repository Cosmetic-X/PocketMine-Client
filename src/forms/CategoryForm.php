<?php
declare(strict_types=1);
namespace cosmeticx\forms;
use Closure;
use cosmeticx\CosmeticManager;
use cosmeticx\cosmetics\Category;
use cosmeticx\cosmetics\Cosmetic;
use pocketmine\player\Player;
use xxAROX\forms\elements\Button;
use xxAROX\forms\types\MenuForm;


/**
 * Class CategoryForm
 * @package cosmeticx\forms
 * @author Jan Sohn / xxAROX
 * @date 02. October, 2022 - 18:41
 * @ide PhpStorm
 * @project PocketMine-Client
 */
class CategoryForm extends MenuForm{
	public function __construct(Player $player, string $category){
		$cosmetics = CosmeticManager::getInstance()->getCosmetics(fn (Cosmetic $cosmetic): bool => $cosmetic->getCategory() == $category);
		$buttons = array_map(function (Cosmetic $cosmetic): Button {
			return new Button($cosmetic->getDisplayName(), function (Player $player) use ($cosmetic): void{
				$session = CosmeticManager::getInstance()->getSession($player->getName());
				if (!$session->isActiveCosmetic($cosmetic)) $session->activateCosmetic($cosmetic);
				else $session->deactivateCosmetic($cosmetic);
				$player->sendForm($this);
			});
		}, $cosmetics);
		parent::__construct("C o s m e t i c  -  X", "", $buttons, null);
	}
}
