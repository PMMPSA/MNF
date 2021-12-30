<?php

namespace TungstenVn\SeasonPass\menuHandle;

use pocketmine\item\Item;

use TungstenVn\SeasonPass\menuHandle\menuHandle;

use TungstenVn\SeasonPass\libs\muqsit\invmenu\InvMenu;
use TungstenVn\SeasonPass\libs\muqsit\invmenu\SharedInvMenu;
class createDefaultMenu
{
    public $menu;

    public function __construct(menuHandle $mnh, $sender)
    {
        $this->createMenu($sender);
    }

    public function createMenu($sender)
    {
        $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST)
            ->setName("§lSSP MÙA II §r(XEM CẤP /minelevel)");
        $normalBook = Item::get(340, 0, 1)->setCustomName("§r§a§l【 §7THẺ THÔNG THƯỜNG §a】");
        $normalBook->setLore(["§r§fMọi người đều có thể nhận vật phẩm trong thẻ này"]);

        $royalBook = Item::get(387, 0, 1)->setCustomName("§r§a§l【 §6THẺ HUYỀN THOẠI §a】");
        $royalBook->setLore(["§r§6Mua Nó Tại /muassp để nhận các vật phẩm tại thẻ này"]);

        $menu->getInventory()->setItem(0, $normalBook);
        $menu->getInventory()->setItem(1, Item::get(160, 5, 1));
        $menu->getInventory()->setItem(10, Item::get(160, 5, 1));
        $menu->getInventory()->setItem(27, $royalBook);
        $menu->getInventory()->setItem(28, Item::get(160, 4, 1));
        $menu->getInventory()->setItem(37, Item::get(160, 4, 1));

        $menu->getInventory()->setItem(45, Item::get(339, 0, 1)->setCustomName("§r§lĐẾN TRANG BÊN TRÁI"));
        $menu->getInventory()->setItem(53, Item::get(339, 0, 1)->setCustomName("§r§lĐẾN TRANG BÊN PHẢI"));

        $menu->getInventory()->setItem(18, Item::get(399, 0, 1));
        $menu->getInventory()->setItem(19, Item::get(399, 0, 1));
        $this->menu = $menu;
    }
}