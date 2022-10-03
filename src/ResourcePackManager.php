<?php
declare(strict_types=1);
namespace cosmeticx;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use vezdehod\packs\ContentFactory;
use vezdehod\packs\PluginContent;
use vezdehod\packs\ui\jsonui\binding\Binding;
use vezdehod\packs\ui\jsonui\binding\BindingType;
use vezdehod\packs\ui\jsonui\binding\DataBinding;
use vezdehod\packs\ui\jsonui\element\CustomElement;
use vezdehod\packs\ui\jsonui\element\Element;
use vezdehod\packs\ui\jsonui\element\ImageElement;
use vezdehod\packs\ui\jsonui\element\ScreenElement;
use vezdehod\packs\ui\jsonui\element\StackPanelElement;
use vezdehod\packs\ui\jsonui\element\types\Anchor;
use vezdehod\packs\ui\jsonui\element\types\FontType;
use vezdehod\packs\ui\jsonui\element\types\Offset;
use vezdehod\packs\ui\jsonui\element\types\Orientation;
use vezdehod\packs\ui\jsonui\element\types\PropertyBag;
use vezdehod\packs\ui\jsonui\element\types\Rotation;
use vezdehod\packs\ui\jsonui\element\types\Size;
use vezdehod\packs\ui\jsonui\element\types\TextAlignment;
use vezdehod\packs\ui\jsonui\vanilla\form\IFormDecorator;
use vezdehod\packs\ui\jsonui\vanilla\form\SimpleFormStyle;
use vezdehod\packs\VPacksAPI;


/**
 * Class ResourcePackManager
 * @package cosmeticx
 * @author Jan Sohn / xxAROX
 * @date 02. October, 2022 - 17:03
 * @ide PhpStorm
 * @project PocketMine-Client
 */
final class ResourcePackManager{
	use SingletonTrait{
		setInstance as private;
		reset as private;
	}

	protected VPacksAPI $VPacksAPI;
	protected PluginContent $content;
	protected IFormDecorator $categoriesDecorator;
	protected IFormDecorator $categoryDecorator;


	/**
	 * ResourcePackManager constructor.
	 * @param CosmeticX $plugin
	 */
	public function __construct(CosmeticX $plugin){
		self::setInstance($this);
		$this->VPacksAPI = new VPacksAPI(
			$plugin,
			"§l§9  «  §r§dCosmetic-X§r §l§9 »",
			"§l§8»§r  §dCosmetic-X §6Resource-Pack§r  §l§8«§r",
			"c0dff0f6-937d-86d6-b5f1-eca7558d56b7",
			"826edee9-da0b-5613-a0c5-1e86eb6a2b65"
		);
		$this->content = ContentFactory::create($plugin);
		$this->make();
	}

	/**
	 * Function getVPacksAPI
	 * @return VPacksAPI
	 */
	public function getVPacksAPI(): VPacksAPI{
		return $this->VPacksAPI;
	}

	private function make(): void{
		$this->categoryDecorator = $this->makeCategoryDecorator();
		$this->categoriesDecorator = $this->makeCategoriesDecorator();
	}

	/**
	 * Function makeCategoriesDecorator
	 * @return IFormDecorator
	 */
	private function makeCategoriesDecorator(): IFormDecorator{
		// Categories form
		$style = $this->content->getUI()->getJsonUIs()->createSimpleFromStyle();
		$uuidBinding = new Binding("player_uuid");
		$wrapper = $style->addStackPanel($style->getRootName(), Orientation::HORIZONTAL());
		$wrapper->offset(new Offset(Offset::ofHeight(0), Offset::ofWidth(20)));
		$wrapper->addCustom("skin_viewer")
			->size(new Size("235px", "200px"))
			//->size(new Size("110px", "100px"))
			->anchor(Anchor::LEFT_MIDDLE())
			->renderer(CustomElement::PAPER_DOLL_RENDERER)
			->useUuid(true)
			->setVar("show_background", true)
			->useSelectedSkin(true)
			->rotation(Rotation::GESTURE_X())
			->propertyInBag(PropertyBag::PLAYER_UUID, $uuidBinding)
			->binding(DataBinding::viewRebind(SimpleFormStyle::getContentBinding(), $uuidBinding))		// Use form "content" field to uuid-storage

			->in(function (Element $elem): Element{
				$elem->addExtends("clear_cosmetics", "common_buttons.light_text_button")
					->size(new Size("25px", "25px"))
					->anchor(Anchor::TOP_RIGHT())
					->setVar('pressed_button_name', 'button.form_button_click')
					->setVar('border_visible', true)
					->binding(DataBinding::overrideFromCollection(
						SimpleFormStyle::getFormButtons(),
						SimpleFormStyle::getButtonTextureBinding(),
						ImageElement::getTextureBinding())
					)
					->binding(DataBinding::overrideFromCollection(
						SimpleFormStyle::getFormButtons(),
						SimpleFormStyle::getButtonTextureFSBinding(),
						ImageElement::getTextureFSBinding()
					))
					->in(fn(Element $elem) => $elem->addImage("reload_image", "textures/ui/cancel")
						->size(new Size("25px", "25px"))
						->layer(2)
					)
				;
				return $elem;
			})
		;

		// TODO: dont show first button

		$wrapper->addExtends("default", "server_form.long_form")
			->anchor(Anchor::RIGHT_MIDDLE())
			->size(new Size("250px", "250px"))
		;

		$style->setRootElement($wrapper);

		return $style->getDecorator();
	}

	private function makeCategoryDecorator(): IFormDecorator{
		// Category form
		$style = $this->content->getUI()->getJsonUIs()->createSimpleFromStyle();

		$title = $style->addPanel("title")
			->layer(1)
			->size(new Size("100%", "10px"))
			->anchor(Anchor::TOP_MIDDLE())
			->in(fn($elem) => $elem->addLabel("text")
				->text(ScreenElement::getTitleBinding())
				->fontType(FontType::SMOOTH())
				->textAlignment(TextAlignment::CENTER())
				->anchor(Anchor::TOP_MIDDLE())
			)
			->in(fn($elem) => $elem->addExtends("close_button", "common.close_button")
				->anchor(Anchor::TOP_RIGHT())
			)
		;

		$contents = $style->addStackPanel("contents", Orientation::HORIZONTAL());

		$uuidBinding = new Binding("player_uuid");
		$contents->addCustom("skin_viewer")
			->size(new Size("225px", "200px"))
			->anchor(Anchor::CENTER())
			->renderer(CustomElement::PAPER_DOLL_RENDERER)
			->useUuid(true)
			->useSelectedSkin(true)
			->rotation(Rotation::GESTURE_X())
			->propertyInBag(PropertyBag::PLAYER_UUID, $uuidBinding)
			->binding(DataBinding::viewRebind(SimpleFormStyle::getContentBinding(), $uuidBinding)); // Use form "content" field to uuid-storage

		$btn = $style->addStackPanel("category_buttons", Orientation::HORIZONTAL())
			->size(new Size("100%", "32px"))
		;

		$btn->addExtends("form_button", "common_buttons.light_text_button")
			->anchor(Anchor::CENTER())
			->layer(2)
			->size(new Size("100%", "100% /6"))
			->setVar('pressed_button_name', 'button.form_button_click')
			->setVar('border_visible', false)
			->setVar('button_text', SimpleFormStyle::getButtonTextBinding())
			->setVar('button_text_binding_type', BindingType::COLLECTION())
			->setVar('button_text_grid_collection_name', SimpleFormStyle::getFormButtons())
			->setVar('button_text_max_size', new Size("100%", "20px"))
			->binding(DataBinding::collectionDetails(SimpleFormStyle::getFormButtons()));

		$actions = $style->addStackPanel("scrolling_view", Orientation::VERTICAL())
			->anchor(Anchor::CENTER())
			->size(new Size("100%", "100%c"))
			->factory("buttons", $btn->getId())
			->collectionName(SimpleFormStyle::getFormButtons())
			->binding(DataBinding::override(SimpleFormStyle::getButtonsLengthBinding(), StackPanelElement::getCollectionLengthBinding()));


		$contents->addExtends("contents", "common.scrolling_panel")
			->layer(1)
			->anchor(Anchor::TOP_RIGHT())
			->size(new Size("100%", "100%"))
			->setVar("show_background", false)
			->setVar("scrolling_content", $actions->getId())
			->setVar("scrolling_pane_size", new Size("200px", "100%"))
			->setVar("scrolling_pane_offset", new Offset("2px", "0px"))
			->setVar("scroll_bar_right_padding_size", new Size("0px", "0px"));

		$root = $style->addStackPanel($style->getRootName(), Orientation::VERTICAL())
			//->size(new Size("100% - 50px", "100% - 20px"));
			->size(new Size("100% - 500px", "100% - 200px"));
		$root->addExtends("title", $title->getId());
		$root->addPanel("padding")->size(new Size("100%", "30px"));
		$root->addExtends("contents", $contents->getId());

		$style->setRootElement($root);

		return $style->getDecorator();
	}

	/**
	 * Function getContent
	 * @return PluginContent
	 */
	public function getContent(): PluginContent{
		return $this->content;
	}

	/**
	 * Function getCategoryDecorator
	 * @return IFormDecorator
	 */
	public function getCategoryDecorator(): IFormDecorator{
		return $this->categoryDecorator;
	}

	/**
	 * Function getCategoriesDecorator
	 * @return IFormDecorator
	 */
	public function getCategoriesDecorator(): IFormDecorator{
		return $this->categoriesDecorator;
	}
}
