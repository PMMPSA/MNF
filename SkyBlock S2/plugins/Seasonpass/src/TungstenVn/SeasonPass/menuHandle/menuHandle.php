<?php

namespace TungstenVn\SeasonPass\menuHandle;

use pocketmine\Player;

use TungstenVn\SeasonPass\commands\commands;

use pocketmine\item\Item;
use pocketmine\event\Listener;

use pocketmine\inventory\PlayerCursorInventory;
use pocketmine\event\inventory\InventoryTransactionEvent;

use pocketmine\inventory\transaction\action\DropItemAction;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\inventory\transaction\action\CreativeInventoryAction;

use pocketmine\event\inventory\InventoryCloseEvent;
use pocketmine\event\player\PlayerQuitEvent;

use TungstenVn\SeasonPass\libs\muqsit\invmenu\inventory\InvMenuInventory;
use TungstenVn\SeasonPass\sounds\soundHandle;

class menuHandle implements Listener
{

    public $cmds;
    public $name; //player's name
    public $corner = [0, 0]; //slot 2 in chest is corner
    public $menu;
    public $matrix;
    public function __construct(commands $cmds,Player $sender)
    {
        $this->cmds = $cmds;

        $matrix = new createMatrix($this, $sender);
        $this->matrix = $matrix->getMatrix();

        $menu = new createDefaultMenu($this, $sender);
        $this->menu = $menu->menu;
        $this->menu->send($sender);

        $this->name = $sender->getName();

        new loadMenu($this, $sender, [0, 0], $this->matrix);

    }

    public function onTransaction(InventoryTransactionEvent $ev): void
    {
        $player = $ev->getTransaction()->getSource();
        if ($player->getName() != $this->name) {
            return;
        }
        $ev->setCancelled();
        $acts = array_values($ev->getTransaction()->getActions());
        if ($acts[0] instanceof CreativeInventoryAction || $acts[1] instanceof CreativeInventoryAction) {
            return;
        }
        if ($this->check_instance($acts[0], "win10") && $this->check_instance($acts[1], "win10") || $this->check_instance($acts[0], "phone") && $this->check_instance($acts[1], "phone")) {
            if ($acts[0] instanceof SlotChangeAction && $acts[1] instanceof SlotChangeAction) {
                //check mobile move
                if (!$acts[0]->getInventory() instanceof PlayerCursorInventory and !$acts[1]->getInventory() instanceof PlayerCursorInventory) {
                    $sound = new soundHandle($this);
                    $sound->illigelSound($player);
                    $item = Item::get(76, 0, 1)->setCustomName("??r??a??l??? ??fTh??ng b??o ??a???");
                    $item->setLore(["??r??cTh??? m???t m???c ????? x??c nh???n, kh??ng di chuy???n n?? ?????n m???t n??i kh??c tr??n menu"]);
                    $this->menu->getInventory()->setItem(52, $item);
                    $player->getCursorInventory()->setItem(0, Item::get(0, 0, 0));
                    return;
                }
            }
            $slotId = 0;
            //works both win10 and mobile player
            if ($acts[0] instanceof DropItemAction) {
                $slotId = $acts[1]->getSlot();
            } else {
                if ($acts[0]->getInventory() instanceof PlayerCursorInventory) {
                    $slotId = $acts[1]->getSlot();
                } else {
                    $slotId = $acts[0]->getSlot();
                }
            }
            $legalSlot = [2, 3, 4, 5, 6, 7, 8, 29, 30, 31, 32, 33, 34, 35, 45, 53];
            if (!in_array($slotId, $legalSlot)) {
                $sound = new soundHandle($this);
                $sound->illigelSound($player);
                $item = Item::get(76, 0, 1)->setCustomName("??r??a??l??? ??fTh??ng b??o ??a???");
                $item->setLore(["??r??cM???c n??y kh??ng th??? ???????c ch???m v??o"]);
                $this->menu->getInventory()->setItem(52, $item);
                $player->getCursorInventory()->setItem(0, Item::get(0, 0, 0));
                return;
            }

            if ($slotId < 9) {
                $x = 0;
                $y = $slotId - 2 + $this->corner[1];
                $this->takeItem(0, $y, $player, $this->menu);
            } else if ($slotId < 36) {
                $x = 3;
                $y = $slotId - 27 - 2 + $this->corner[1];
                $this->takeItem(1, $y, $player, $this->menu);
            } else {
                $sound = new soundHandle($this);
                if ($slotId == 45) {
                    if ($this->corner[1] == 0) {
                        $sound = new soundHandle($this);
                        $sound->illigelSound($player);
                        $item = Item::get(76, 0, 1)->setCustomName("??r??a??l??? ??fTh??ng b??o ??a???");
                        $item->setLore(["??r??cKh??ng th??? di chuy???n sang tr??i n???a :3"]);
                        $this->menu->getInventory()->setItem(52, $item);
                        $player->getCursorInventory()->setItem(0, Item::get(0, 0, 0));
                        return;
                    }
                    $sound->moveRightLeft($player);
                    $this->corner[1] -= 7;
                    $this->menu->getInventory()->setItem(52, Item::get(0,0,1));
                    $player->getCursorInventory()->setItem(0, Item::get(0, 0, 0));
                    new loadMenu($this, $player, [0, $this->corner[1]], $this->matrix);
                } else {
                    $sound->moveRightLeft($player);
                    $this->corner[1] += 7;
                    $this->menu->getInventory()->setItem(52, Item::get(0,0,1));
                    $player->getCursorInventory()->setItem(0, Item::get(0, 0, 0));
                    new loadMenu($this, $player, [0, $this->corner[1]], $this->matrix);
                }
            }
        }else{
            $item = Item::get(76, 0, 1)->setCustomName("??r??a??l??? ??fTh??ng b??o ??a???");
            $item->setLore(["??r??cH??nh ?????ng ???? kh??ng ???????c ph??p, n???u b???n c?? ?? ?????nh l???y\n??r??c????? trong seasonpass, h??y nh???n v?? v???t ph???m b???n mu???n l???y\n??r??csau ???? nh???n v?? kho???ng tr???ng ngo??i menu seasonpass"]);
            $this->menu->getInventory()->setItem(52, $item);
            $sound = new soundHandle($this);
            $sound->illigelSound($player);
            $player->getCursorInventory()->setItem(0, Item::get(0, 0, 0));
        }
    }
    public  function takeItem(int $type,int $y,Player $player,$menu){
        //dont change $txt;
        $txt = "normalPass";
        if($type != 0){
            $txt = "royalPass";
        }

        $levelApi = $this->cmds->ssp->levelApi;
        $level = $levelApi->getLevel($player);

        //Linh ?????ng gi???a vi???c d??ng perm ho???c d??ng m???ng nh??t t??n v??o
        if($type == 1 and !$player->hasPermission("royalPass.Tungsten.Perm")){
            $item = Item::get(76, 0, 1)->setCustomName("??r??a??l??? ??fTh??ng b??o ??a???");
            $item->setLore(["??r??cB???n kh??ng c?? quy???n l???y v???t ph???m ??? th??? huy???n tho???i"]);
            $menu->getInventory()->setItem(52, $item);
            $sound = new soundHandle($this);
            $sound->dontHavePerm($player);
            $player->getCursorInventory()->setItem(0, Item::get(0, 0, 0));
            return;
        }
        if((int) $level < $this->cmds->ssp->getConfig()->getNested("marker")[$txt][$y]){
            $a = $this->cmds->ssp->getConfig()->getNested("marker")[$txt][$y];
            $item = Item::get(76, 0, 1)->setCustomName("??r??a??l??? ??fTh??ng b??o ??a???");
            $item->setLore(["??r??cB???n ??ang level [$level] nh??ng v???t ph???m n??y y??u c???u m???c ????? [$a]"]);
            $menu->getInventory()->setItem(52, $item);
            $sound = new soundHandle($this);
            $sound->notEnoughLevel($player);
            $player->getCursorInventory()->setItem(0, Item::get(0, 0, 0));
            return;
        }
        if(isset($this->cmds->ssp->getConfig()->getNested($txt)[$y])){

                if(!isset($this->cmds->ssp->getConfig()->getNested("database")[$player->getName()][$type][$y])){
                    $item = $this->cmds->ssp->getConfig()->getNested($txt)[$y];
                    $item = unserialize(utf8_decode($item));
                    $sound = new soundHandle($this);
                    if($player->getInventory()->canAddItem($item)){
                        $player->getInventory()->addItem($item);
                        $this->cmds->ssp->getConfig()->setNested("database.".$this->name.".".$type.".".$y,"taken");
                        $this->cmds->ssp->getConfig()->save();
                        if($type == 0){
                            $sound->normalTaken($player);
                            $sound->water($player);
                            $menu->getInventory()->setItem($y - $this->corner[1] +9 + 2, Item::get(241, 5, 1));
                            $this->matrix[0+1][$y] = "taken";
                            $this->cmds->ssp->getServer()->broadcastMessage("??a??l??????eason ???ass??? ??r??f??? ??eXin ch??c m???ng ??c[".$player->getName()."]??e ???? l???y ??c[Item $y] ??etrong ??fTH??? TH??NG TH?????NG");
                            $item = Item::get(325, 4, 1)->setCustomName("??r??a??l??? ??fCh??c M???ng ??a???");
                            $item->setLore(["??r??6B???n ???? l???y m???t m???c trong th??? ??fth??ng th?????ng"]);
                            $menu->getInventory()->setItem(52,$item);
                            $player->getCursorInventory()->setItem(0, Item::get(0, 0, 0));
                        }else{
                            $sound->royalTaken($player);
                            $sound->water($player);
                            $menu->getInventory()->setItem($y - $this->corner[1] +36 + 2, Item::get(241, 5, 1));
                            $this->matrix[3+1][$y] = "taken";
                            $this->cmds->ssp->getServer()->broadcastMessage("??a??l??????eason ???ass??? ??r??f??? ??eXin ch??c m???ng ??c[".$player->getName()."]??e ???? l???y ??c[Item $y] ??etrong ??6TH??? HUY???N THO???I");
                            $item = Item::get(325, 5, 1)->setCustomName("??r??a??l??? ??fCh??c M???ng ??a???");
                            $item->setLore(["??r??6B???n ???? l???y m???t m???c trong th??? ??ehuy???n tho???i"]);
                            $menu->getInventory()->setItem(52,$item);
                            $player->getCursorInventory()->setItem(0, Item::get(0, 0, 0));
                        }
                        return;
                    }else{
                        $sound->illigelSound($player);
                        $player->removeWindow($menu->getInventory());
                        $player->sendMessage("??r??cKh??ng c?? v??? tr?? tr???ng trong kho c???a b???n, vui l??ng d???n ??t ????? r???i th??? l???i");
                        $this->name = null;
                        return;
                    }
                }else{
                    $sound = new soundHandle($this);
                    $sound->alreadyTaken($player);
                    $item = Item::get(76, 0, 1)->setCustomName("??r??a??l??? ??fTh??ng b??o ??a???");
                    $item->setLore(["??r??cB???n ???? nh???n ???????c v???t ph???m ????"]);
                    $menu->getInventory()->setItem(52, $item);
                    $player->getCursorInventory()->setItem(0, Item::get(0, 0, 0));
                    return;
                }

        }else{
            $sound = new soundHandle($this);
            $sound->illigelSound($player);
            $player->removeWindow($this->menu->getInventory());
            $player->sendMessage("??r??c???? x???y ra l???i, vui l??ng th??ng b??o cho admin. Th??? m??a ID: pass2");
            return;
        }
    }
    public function check_instance($var, $type)
    {
        if ($type == "win10") {
            if ($var instanceof DropItemAction) {
                return false;
            }
            if ($var->getInventory() instanceof PlayerCursorInventory || $var->getInventory() instanceof InvMenuInventory) {
                return true;
            }
            return false;
        } else if ($type == "phone") {
            if ($var instanceof DropItemAction) {
                return true;
            } else {
                if ($var->getInventory() instanceof InvMenuInventory) {
                    return true;
                }
            }
            return false;
        }
    }

    public function onClose(InventoryCloseEvent $ev)
    {
        $player = $ev->getPlayer();
        if ($player->getName() == $this->name) {
            $this->name = null;
            return;
        }
    }

    public function onQuit(PlayerQuitEvent $ev)
    {
        $player = $ev->getPlayer();
        if ($player->getName() == $this->name) {
            $this->name = null;
            return;
        }
    }
}