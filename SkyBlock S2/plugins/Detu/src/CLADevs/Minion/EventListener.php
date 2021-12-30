<?php

declare(strict_types=1);

namespace CLADevs\Minion;

use CLADevs\Minion\minion\HopperInventory;
use CLADevs\Minion\minion\Minion;
use pocketmine\block\Chest;
use pocketmine\entity\Entity;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\Item;
use pocketmine\nbt\tag\StringTag;
use pocketmine\utils\TextFormat as C;
use pocketmine\scheduler\ClosureTask;

use onebone\pointapi\PointAPI;

class EventListener implements Listener{

    public $linkable = [];
    
    public function onInv(InventoryTransactionEvent $e): void{
        $tr = $e->getTransaction();
        foreach($tr->getActions() as $act){
            if($act instanceof SlotChangeAction){
                $inv = $act->getInventory();
                if($inv instanceof HopperInventory){
                    $player = $tr->getSource();
                    $entity = $inv->getEntity();
                    $e->setCancelled();
                    switch($act->getSourceItem()->getId()){
                        case Item::REDSTONE_DUST:
                            if(isset($this->linkable[$player->getName()])) unset($this->linkable[$player->getName()]);
                            if($entity->isAlive()){
                                    $this->seconds = 5;
                                    $this->entica = $entity->getLevelM();
                                    $this->autofix = $entity->getAutoFix();
                                    $this->eter = $entity->getEter();
                                    $this->playca = $player;
                                    $entity->flagForDespawn();
                                     $this->time = Main::get()->getScheduler()->scheduleRepeatingTask(new ClosureTask(function($_) : void{
            if(--$this->seconds === 0){
                $this->playca->sendMessage("§l§e•> §aBạn đã lấy lại đệ tử");
                Main::get()->getScheduler()->cancelTask($this->time->getTaskId());
                            $this->playca->getInventory()->addItem(Main::get()->getItem($this->playca, $this->entica, $this->eter, $this->autofix));
            }}), 5);
                            }
                            break;
                        case Item::ENDER_CHEST:
                            if($entity->getLookingBehind() instanceof Chest){
                                $player->sendMessage(C::RED . "§l§e•> §bVui lòng loại bỏ rương phía sau đệ tử để thiết lập rương liên kết mới.");
                                return;
                            }
                            if(isset($this->linkable[$player->getName()])){
                                $player->sendMessage(C::RED . "§l§e•> §cBạn đã liên kết rương rồi");
                                return;
                            }
                            $this->linkable[$player->getName()] = $entity;
                            $player->sendMessage(C::LIGHT_PURPLE . "§l§e•> §fHãy nhấn vào rương kết nối để tui có thể bỏ đồ vào đó");
                            break;
                        case Item::EMERALD:
                            if($entity->getLevelM() >= Main::get()->getConfig()->getNested("level.max")){
                                $player->sendMessage(C::RED . "§l§e•> §cBạn đã nâng đệ lên cấp cuối");
                                $inv->onClose($player);
                                return;
                            }
                            if(PointAPI::getInstance()->myPoint($player) < $entity->getCost()){
                                $player->sendMessage(C::RED . "§l§e•> §cBạn không đủ point để nâng cấp");
                                return;
                            }
                            $entity->namedtag->setInt("level", $entity->namedtag->getInt("level") + 1);
                            $player->sendMessage(C::GREEN . "§l§e•>§b Bạn đã nâng đệ lên cấp " . $entity->getLevelM());
                            PointAPI::getInstance()->reducePoint($player, $entity->getCost());
                            break;
                            case Item::ANVIL:
                            if($entity->getAutoFix() === "yes"){
                                $player->sendMessage(C::RED . "§l§e•> §cBạn đã mua tự sửa cúp rồi");
                                $inv->onClose($player);
                                return;
                            }
                            if(PointAPI::getInstance()->myPoint($player) <= 30){
                                $player->sendMessage(C::RED . "§l§e•> §cBạn không đủ point để mua tự sửa cúp");
                                $inv->onClose($player);
                                return;
                            }
                            $entity->namedtag->setString("autofix", "yes");
                            $player->sendMessage(C::GREEN . "§l§e•> §bBạn đã mua tự sửa cúp thành công");
                            PointAPI::getInstance()->reducePoint($player, 30);
                            break;
                             case Item::DRAGON_EGG:
                            if($entity->getEter() === "yes"){
                                $player->sendMessage(C::RED . "§l§e•> §cBạn đã mua vô hạn quặng rồi");
                                $inv->onClose($player);
                                return;
                            }
                            if(PointAPI::getInstance()->myPoint($player) <= 200){
                                $player->sendMessage(C::RED . "§l§e•> §cBạn không đủ point để mua");
                                $inv->onClose($player);
                                return;
                            }
                            $entity->namedtag->setString("eternity", "yes");
                            $player->sendMessage(C::GREEN . "§l§e•> §bBạn đã mua vô hạn quặng thành công");
                                                        PointAPI::getInstance()->reducePoint($player, 200);
                            break;                           
                    }
                    $inv->onClose($player);
                }
            }
        }
    }

    public function onInteract(PlayerInteractEvent $e): void{
        $player = $e->getPlayer();
        $item = $e->getItem();
        $dnbt = $item->getNamedTag();

        if(isset($this->linkable[$player->getName()])){
            if(!$e->getBlock() instanceof Chest){
                $player->sendMessage(C::RED . "§l§e•>§c Hãy nhấn vô rương, không phải " . $e->getBlock()->getName());
                return;
            }
            $entity = $this->linkable[$player->getName()];
            $block = $e->getBlock();
            if($entity instanceof Minion) $entity->namedtag->setString("xyz", $block->getX() . ":" . $block->getY() . ":" . $block->getZ());
            unset($this->linkable[$player->getName()]);
            $player->sendMessage(C::GREEN . "§l§e•>§b Bạn đã liên kết rương thành công");
            return;
        }

        if($dnbt->hasTag("summon", StringTag::class)){
            $nbt = Entity::createBaseNBT($player, null, (90 + ($player->getDirection() * 90)) % 360);
            $nbt->setString("eternity", ($dnbt->getString("eternity") ?? "no"));
            $nbt->setString("autofix", ($dnbt->getString("autofix") ?? "no"));
            $nbt->setInt("level", $dnbt->getInt("level"));
            $nbt->setString("player", $player->getName());
            $nbt->setString("xyz", $dnbt->getString("xyz"));
            $nbt->setTag($player->namedtag->getTag("Skin"));
            $entity = new Minion($player->getLevel(), $nbt);
            $entity->spawnToAll();
            $item->setCount($item->getCount() - 1);
            $player->getInventory()->setItemInHand($item);
        }
    }
    public function getEv($player){
		unset($this->linkable[$player->getName()]);
	}
}