<?php

declare(strict_types=1);

namespace CLADevs\Minion\minion;

use pocketmine\block\Block;
use pocketmine\inventory\CustomInventory;
use pocketmine\item\Item;
use pocketmine\level\Position;
use pocketmine\nbt\NetworkLittleEndianNBTStream;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\BlockActorDataPacket;
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use pocketmine\Player;
use pocketmine\utils\TextFormat as C;

class HopperInventory extends CustomInventory{

    protected $holder;
    protected $entity;

    public function __construct(Position $position, Minion $entity){
        parent::__construct($position);
        $this->entity = $entity;
        $this->setItem(0, $this->getDestoryItem());
        $this->setItem(1, $this->getAutoFixItem());
        $this->setItem(2, $this->getChestItem());
        $this->setItem(3, $this->getEterItem());        
        $this->setItem(4, $this->getLevelItem());
    }

    public function getName(): string{
        return "Hopper";
    }

    public function getDefaultSize(): int{
        return 5;
    }

    public function getNetworkType(): int{
        return WindowTypes::HOPPER;
    }

    public function onOpen(Player $who): void{
        $block = Block::get(Block::HOPPER_BLOCK);
        $block->x = $this->getHolder()->getX();
        $block->y = $this->getHolder()->getY();
        $block->z = $this->getHolder()->getZ();
        $block->level = $this->getHolder()->getLevel();
        $who->getLevel()->sendBlocks([$who], [$block]);
        $w = new NetworkLittleEndianNBTStream;
        $nbt = new CompoundTag("", []);
        $nbt->setString("id", "Hopper");
        $nbt->setString("CustomName", C::GOLD . "§r§l TÙY CHỈNH / NÂNG CẤP ĐỆ");
        $pk = new BlockActorDataPacket();
        $pk->x = $this->getHolder()->getX();
        $pk->y = $this->getHolder()->getY();
        $pk->z = $this->getHolder()->getZ();
        $pk->namedtag = $w->write($nbt);
        $who->dataPacket($pk);
        parent::onOpen($who);
    }

    public function onClose(Player $who): void{
        $block = Block::get(Block::AIR);
        $block->x = $this->getHolder()->getX();
        $block->y = $this->getHolder()->getY();
        $block->z = $this->getHolder()->getZ();
        $block->level = $this->getHolder()->getLevel();
        $who->getLevel()->sendBlocks([$who], [$block]);
        parent::onClose($who);
    }
    
    public function onClose2(Player $who): bool{
        $block = Block::get(Block::AIR);
        $block->x = $this->getHolder()->getX();
        $block->y = $this->getHolder()->getY();
        $block->z = $this->getHolder()->getZ();
        $block->level = $this->getHolder()->getLevel();
        $who->getLevel()->sendBlocks([$who], [$block]);
        parent::onClose($who);
        return true;
    }

    public function getHolder(): Position{
        return $this->holder;
    }

    public function getEntity(): Minion{
        return $this->entity;
    }

    public function getDestoryItem(): Item{
        $item = Item::get(Item::REDSTONE_DUST);
        $item->setCustomName(C::RED . "§r§l§c  BẾ ĐỆ TỬ LÊN\n\n\n§r§o§9Bế đệ tử vào túi đồ.");
        return $item;
    }

    public function getChestItem(): Item{
        $islinked = $this->entity->isChestLinked() ? "Có" : "Không";
        $item = Item::get(Item::ENDER_CHEST);
        $item->setCustomName(C::DARK_GREEN . "§r§l§2  TÌNH TRẠNG KẾT NỐI RƯƠNG\n\n");
        $item->setLore(["",  C::YELLOW . "Kết nối rương: " . C::WHITE . $islinked, C::YELLOW . "Tọa độ: " . C::WHITE . $this->entity->getChestCoordinates()]);
        return $item;
    }

    public function getLevelItem(): Item{
        $item = Item::get(Item::EMERALD);
        $item->setCustomName(C::LIGHT_PURPLE . "§r§l§e  CẤP ĐỘ ĐỆ: " . C::YELLOW . $this->entity->getLevelM());
        $item->setLore([C::LIGHT_PURPLE . "\n\n§l§fGiá: §r§e" . C::YELLOW . "POINTS " . $this->entity->getCost()]);
        return $item;
    }
    public function getAutoFixItem(): Item{
        $item = Item::get(Item::ANVIL);
        $item->setCustomName("§r§l§b  MUA TỰ ĐỘNG SỬA\n\n");
        if($this->entity->getAutoFix() === "no"){
                $item->setLore(["§9Bạn chưa mua.\n".C::LIGHT_PURPLE . "§l§fGiá: §r§f" . C::YELLOW . "§o30 POINTS"]);
        }else{
        $item->setLore(["§cBạn đã mua."]);
        }
        return $item;
    }
    public function getEterItem(): Item{
        $item = Item::get(Item::DRAGON_EGG);
        $item->setCustomName("§r§l§6  QUẶNG VÔ HẠN\n\n");
        if($this->entity->getEter() === "no"){
        $item->setLore(["§9Bạn chưa mua.\n".C::LIGHT_PURPLE . "§l§fGiá: §r§f" . C::YELLOW . "§o200 POINTS"]);
        }else{
        $item->setLore(["§cBạn đã mua."]);
        }
        return $item;
    }    
}