<?php

namespace pickaxe;

use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\math\Vector3;
use pocketmine\item\Item;
use pocketmine\command\{Command, CommandSender, ConsoleCommandSender};
use pocketmine\event\player\{PlayerJoinEvent, PlayerQuitEvent};
use pocketmine\item\enchantment\{Enchantment, EnchantmentInstance};
use pocketmine\level\sound\BatSound;
use pocketmine\level\sound\ClickSound;
use pocketmine\level\sound\DoorSound;
use pocketmine\level\sound\FizzSound;
use pocketmine\level\sound\EndermanTeleportSound;

class Main extends PluginBase implements Listener {
    
    
public function onEnable() {
		$this->getLogger()->info("Plugin Code By LetTIHL And Zero");
        $this->getServer()->getPluginManager()->registerEvents($this ,$this);
        //tự Nung
        $this->ores=array(14,15);
 $this->ingot=array(
 14 => 266,
 15 => 265,);
$this->eco = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
@mkdir($this->getDataFolder(), 0744, true);
       $this->ldlc = new Config($this->getDataFolder()."ldlc.yml",Config::YAML);
       $this->cap = new Config($this->getDataFolder()."cap.yml",Config::YAML);
       $this->kn = new Config($this->getDataFolder()."kinhnghiem.yml",Config::YAML);
       $this->tn = new Config($this->getDataFolder()."tunung.yml",Config::YAML);
       $this->mm = new Config($this->getDataFolder()."memmai.yml",Config::YAML);
       $this->b = new Config($this->getDataFolder()."daoblock.yml",Config::YAML);
       $this->bm = new Config($this->getDataFolder()."blockmoney.yml",Config::YAML);
       $this->eff = new Config($this->getDataFolder()."eff.yml",Config::YAML);
       $this->hp = new Config($this->getDataFolder()."Máu.yml",Config::YAML);
       $this->hs1 = new Config($this->getDataFolder()."haste1.yml",Config::YAML);
       $this->hs2 = new Config($this->getDataFolder()."haste2.yml",Config::YAML);
       $this->hs3 = new Config($this->getDataFolder()."haste3.yml",Config::YAML);
       $this->hs4 = new Config($this->getDataFolder()."haste4.yml",Config::YAML);
       $this->hs5 = new Config($this->getDataFolder()."haste5.yml",Config::YAML);
}

public function onJoin(PlayerJoinEvent $ev) {
if(!$this->ldlc->exists($ev->getPlayer()->getName())) {
			$player = $ev->getPlayer();
			$inv = $player->getInventory();  
			$item = Item::get(278, 0, 1);
			$item->setCustomName("§r§l§a〖§f⚒§a〗§a ＰICKAXE ＭINE ＡND ＦIGHT §a〖§f⚒§a〗\n§r§fCủa ".$player->getName());
			$inv->addItem($item);
			$player->sendMessage("§l§6【⚒】§c Server Ｍine Ａnd Ｆight đã trao cho bạn Cúp của server, hãy cùng đồng hành với nó nhé để nhận nhiều chức năng!");
$this->ldlc->set($ev->getPlayer()->getName());
$this->ldlc->save();
    }
   //kinh Nghiệm 
     if(!$this->kn->exists($ev->getPlayer()->getName())) {
        $this->kn->set($ev->getPlayer ()->getName(), 0);
        $this->kn->save();
    }
  //cấp  
    if(!$this->cap->exists($ev->getPlayer()->getName())) {
        $this->cap->set($ev->getPlayer()->getName(), 1);
        $this->cap->save();
    }
 //Mềm Mại   
    if(!$this->mm->exists($ev->getPlayer()->getName())) {
        $this->mm->set($ev->getPlayer()->getName(), 0);
        $this->mm->save();
    }
    //Tự Nung
    if(!$this->tn->exists($ev->getPlayer()->getName())) {
        $this->tn->set($ev->getPlayer()->getName(), 0);
        $this->tn->save();
    }
    //1 Được 2
    if(!$this->b->exists($ev->getPlayer()->getName())) {
        $this->b->set($ev->getPlayer()->getName(), 0);
        $this->b->save();
    }
    //Đào Block Đc Money
    if(!$this->bm->exists($ev->getPlayer()->getName())) {
        $this->bm->set($ev->getPlayer()->getName(), 0);
        $this->bm->save();
    }
    
    if(!$this->eff->exists($ev->getPlayer()->getName())) {
        $this->eff->set($ev->getPlayer()->getName(), 0);
        $this->eff->save();
    }
    if(!$this->hp->exists($ev->getPlayer()->getName())) {
        $this->hp->set($ev->getPlayer()->getName(), 21);
        $this->hp->save();
    }else{        
       $hp = $this->hp->get($ev->getPlayer()->getName());
        $ev->getPlayer()->setMaxHealth($hp);
    }
    if(!$this->hs1->exists($ev->getPlayer()->getName())) {
        $this->hs1->set($ev->getPlayer()->getName(), 0);
        $this->hs2->save();
    }
    if(!$this->hs2->exists($ev->getPlayer()->getName())) {
        $this->hs2->set($ev->getPlayer()->getName(), 0);
        $this->hs2->save();
    }
    if(!$this->hs3->exists($ev->getPlayer()->getName())) {
        $this->hs3->set($ev->getPlayer()->getName(), 0);
        $this->hs3->save();
    }
    if(!$this->hs4->exists($ev->getPlayer()->getName())) {
        $this->hs4->set($ev->getPlayer()->getName(), 0);
        $this->hs4->save();
    }
    if(!$this->hs5->exists($ev->getPlayer()->getName())) {
        $this->hs5->set($ev->getPlayer()->getName(), 0);
        $this->hs5->save();
    }
}

public function onQuit(PlayerQuitEvent $ev) {
$this->ldlc->save();
$this->kn->save();
$this->cap->save();
$this->mm->save();
$this->tn->save();
$this->b->save();
$this->bm->save();
$this->eff->save();
$this->hs1->save();
$this->hs2->save();
$this->hs3->save();
$this->hs4->save();
$this->hs5->save();
}


public function onbreak(BlockBreakEvent $ev) {
    ///Add 
        $player = $ev->getPlayer();
        $item = $player->getInventory()->getItemInHand()->getCustomName();
        $id = $player->getInventory()->getItemInHand()->getId();
     if($id == 0){
$ev->getPlayer()->sendMessage("");
     return;
     }
      if($item == "§r§l§a〖§f⚒§a〗§a ＰICKAXE ＭINE ＡND ＦIGHT §a〖§f⚒§a〗\n§r§fCủa ".$player->getName()){
		if(!$ev->isCancelled()){
        ////đập Block Đc Money
        $item = $player->getInventory()->getItemInHand();
        $item->setDamage(0);	
        $player->getInventory()->setItemInHand($item);
        $bm = $this->bm->get($ev->getPlayer()->getName());
        if($bm >  0){
     $this->eco->addMoney($ev->getplayer(), 1);
    }
    //Haste

        $hs1 = $this->hs1->get($ev->getPlayer()->getName());
        if($hs1 == 1){
                    $ev->getPlayer()->addEffect(new EffectInstance(Effect::getEffect(3), 3000, 0, false));
        }
        $hs2 = $this->hs2->get($ev->getPlayer()->getName());
        if($hs2 == 1){
                    $ev->getPlayer()->addEffect(new EffectInstance(Effect::getEffect(3), 3000, 1, false));
    }
        $hs3 = $this->hs3->get($ev->getPlayer()->getName());
        if($hs3 == 1){
                    $ev->getPlayer()->addEffect(new EffectInstance(Effect::getEffect(3), 3000, 2, false));
    }
        $hs4 = $this->hs4->get($ev->getPlayer()->getName());
        if($hs4 == 1){
                    $ev->getPlayer()->addEffect(new EffectInstance(Effect::getEffect(3), 3000, 3, false));
    }
        $hs5 = $this->hs5->get($ev->getPlayer()->getName());
        if($hs5 == 1){
                    $ev->getPlayer()->addEffect(new EffectInstance(Effect::getEffect(3), 3000, 4, false));
    }
          //Kim Cương
      if($ev->getBlock()->getId() == 56){
       $this->kn->set($ev->getPlayer()->getName(), ($this->kn->get($ev->getPlayer()->getName()) + 4));
       $this->kn->save();
      }
      //Sắt
      if($ev->getBlock()->getId() == 14){
       $this->kn->set($ev->getPlayer()->getName(), ($this->kn->get($ev->getPlayer()->getName()) + 2));
       $this->kn->save();
      }
      //Vàng
      if($ev->getBlock()->getId() == 15){
       $this->kn->set($ev->getPlayer()->getName(), ($this->kn->get($ev->getPlayer()->getName()) + 2));
       $this->kn->save();
      }
      //Than
      if($ev->getBlock()->getId() == 16){
       $this->kn->set($ev->getPlayer()->getName(), ($this->kn->get($ev->getPlayer()->getName()) + 2));
       $this->kn->save();
      }
      ///Block Vàng
      if($ev->getBlock()->getId() == 41){
       $this->kn->set($ev->getPlayer()->getName(), ($this->kn->get($ev->getPlayer()->getName()) + 4));
       $this->kn->save();
      }
      //Block Sắt
      if($ev->getBlock()->getId() == 42){
       $this->kn->set($ev->getPlayer()->getName(), ($this->kn->get($ev->getPlayer()->getName()) + 2));
       $this->kn->save();
      }
      //Đá
      if($ev->getBlock()->getId() == 4){
       $this->kn->set($ev->getPlayer()->getName(), ($this->kn->get($ev->getPlayer()->getName()) + 2));
       $this->kn->save();
      }
      //Block Kim Cương
      if($ev->getBlock()->getId() == 57){
       $this->kn->set($ev->getPlayer()->getName(), ($this->kn->get($ev->getPlayer()->getName()) + 4));
       $this->kn->save();
      }
      //Đá Đỏ
      if($ev->getBlock()->getId() == 73){
       $this->kn->set($ev->getPlayer()->getName(), ($this->kn->get($ev->getPlayer()->getName()) + 2));
       $this->kn->save();
      }
      //block emrald
      if($ev->getBlock()->getId() == 133){
       $this->kn->set($ev->getPlayer()->getName(), ($this->kn->get($ev->getPlayer()->getName()) + 4));
       $this->kn->save();
      }
      if($ev->getBlock()->getId() == 129){
       $this->kn->set($ev->getPlayer()->getName(), ($this->kn->get($ev->getPlayer()->getName()) + 2));
       $this->kn->save();
      }
      if($ev->getBlock()->getId() == 173){
       $this->kn->set($ev->getPlayer()->getName(), ($this->kn->get($ev->getPlayer()->getName()) + 4));
       $this->kn->save();
      }
      if($ev->getBlock()->getId() == 21){
       $this->kn->set($ev->getPlayer()->getName(), ($this->kn->get($ev->getPlayer()->getName()) + 2));
       $this->kn->save();
      }
      if($ev->getBlock()->getId() == 22){
       $this->kn->set($ev->getPlayer()->getName(), ($this->kn->get($ev->getPlayer()->getName()) + 4));
       $this->kn->save();
      }
      if($ev->getBlock()->getId() == 152){
       $this->kn->set($ev->getPlayer()->getName(), ($this->kn->get($ev->getPlayer()->getName()) + 4));
       $this->kn->save();
      }
		}else{
     $player->sendMessage("§l§c•>§r §e§oVui Lòng Về Đảo Mà Đập Block Nha! (｡ŏ_ŏ)");
		}
        $level = $this->cap->get($ev->getPlayer()->getName());
         $exp = $this->kn->get($ev->getPlayer()->getName());
         $maxexp = $level * 110;
         $ev->getPlayer()->sendPopup("§l§7【§a=============§7[=]§a============§7】\n§r§e•> §l§eＭ§gI§6N§4E §cＡ§2N§aD §bＦ§3I§9G§dH§5T §r§7【 ＳＳＩＩ ⚒ 】 §e<•\n§r§e•>§a Cấp cúp: §e[". $level."]\n§r§e•>§a Exp cúp: §e[".$exp."]§f/§e[".$maxexp."] [X2 - Thứ 7]\n§e•> §aXem Tính Năng: §e/pickaxe §e<•\n§l§7【§a=============§7[=]§a============§7】");
      }
           // lênh Cấp
    $cap = $this->cap->get($ev->getPlayer()->getName());
    $lenhcap = $cap * 110;
     if($this->kn->get($ev->getPlayer()->getName()) >= $lenhcap) {
       $this->kn->set($ev->getPlayer()->getName(), ($this->kn->get($ev->getPlayer()->getName()) - $lenhcap));
       $this->kn->save();
       $this->eco->addmoney($ev->getPlayer(), $cap*500);
       $this->cap->set($ev->getPlayer()->getName(), ($this->cap->get($ev->getPlayer()->getName()) + 1));
       $this->cap->save();
       $money = $cap *500;
       //Enchant
       $cap = $this->cap->get($ev->getPlayer()->getName());
$item = Item::get(278,0,1);
       if($cap < 22){
           $player = $ev->getPlayer();
        $id = 15;
        $lv = $cap - 1;
      $item->setCustomName("§r§l§a〖§f⚒§a〗§a ＰICKAXE ＭINE ＡND ＦIGHT §a〖§f⚒§a〗\n§r§fCủa ".$player->getName());
       $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), $lv));
     $player->sendMessage("§l§c➢§e Enchant §cHiệu xuất cấp độ §e".$lv);
}else{
        $item = Item::get(278,0,1);
        $id = 15;
        $lv = 20;
			$item->setCustomName("§r§l§a〖§f⚒§a〗§a ＰICKAXE ＭINE ＡND ＦIGHT §a〖§f⚒§a〗\n§r§fCủa ".$player->getName());
       $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), $lv));
     $player->sendMessage("§l§c➢§e Enchant §cHiệu xuất cấp độ §e".$lv);
}
       if($cap == 22){
           $player = $ev->getPlayer();
        $id = 18;
        $lv = 1;
			$item->setCustomName("§r§l§a〖§f⚒§a〗§a ＰICKAXE ＭINE ＡND ＦIGHT §a〖§f⚒§a〗\n§r§fCủa ".$player->getName());
       $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), $lv));
     $player->sendMessage("§l§c➢§e Enchant §cGia tài cấp độ §e".$lv);
}
       if($cap == 23){
           $player = $ev->getPlayer();
        $id = 18;
        $lv = 2;
			$item->setCustomName("§r§l§a〖§f⚒§a〗§a ＰICKAXE ＭINE ＡND ＦIGHT §a〖§f⚒§a〗\n§r§fCủa ".$player->getName());
       $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), $lv));
     $player->sendMessage("§l§c➢§e Enchant §cGia tài cấp độ §e".$lv);
       }
            if($cap == 24){
           $player = $ev->getPlayer();
        $id = 18;
        $lv = 3;
			$item->setCustomName("§r§l§a〖§f⚒§a〗§a ＰICKAXE ＭINE ＡND ＦIGHT §a〖§f⚒§a〗\n§r§fCủa ".$player->getName());
       $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), $lv));
     $player->sendMessage("§l§c➢§e Enchant §cGia tài cấp độ §e".$lv);
            }
       if($cap == 25){
           $player = $ev->getPlayer();
        $id = 18;
        $lv = 4;
			$item->setCustomName("§r§l§a〖§f⚒§a〗§a ＰICKAXE ＭINE ＡND ＦIGHT §a〖§f⚒§a〗\n§r§fCủa ".$player->getName());
       $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), $lv));
     $player->sendMessage("§l§c➢§e Enchant §cGia tài cấp độ §e".$lv);
       }
       if($cap == 26){
           $player = $ev->getPlayer();
        $id = 18;
        $lv = 5;
			$item->setCustomName("§r§l§a〖§f⚒§a〗§a ＰICKAXE ＭINE ＡND ＦIGHT §a〖§f⚒§a〗\n§r§fCủa ".$player->getName());
       $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), $lv));
     $player->sendMessage("§l§c➢§e Enchant §cGia tài cấp độ §e".$lv);
       }
       if($cap == 27){
           $player = $ev->getPlayer();
        $id = 18;
        $lv = 6;
			$item->setCustomName("§r§l§a〖§f⚒§a〗§a ＰICKAXE ＭINE ＡND ＦIGHT §a〖§f⚒§a〗\n§r§fCủa ".$player->getName());
       $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), $lv));
     $player->sendMessage("§l§c➢§e Enchant §cGia tài cấp độ §e".$lv);
       }
       if($cap == 28){
           $player = $ev->getPlayer();
        $id = 18;
        $lv = 7;
			$item->setCustomName("§r§l§a〖§f⚒§a〗§a ＰICKAXE ＭINE ＡND ＦIGHT §a〖§f⚒§a〗\n§r§fCủa ".$player->getName());
       $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), $lv));
     $player->sendMessage("§l§c➢§e Enchant §cGia tài cấp độ §e".$lv);
       }
       if($cap == 29){
           $player = $ev->getPlayer();
        $id = 18;
        $lv = 8;
			$item->setCustomName("§r§l§a〖§f⚒§a〗§a ＰICKAXE ＭINE ＡND ＦIGHT §a〖§f⚒§a〗\n§r§fCủa ".$player->getName());
       $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), $lv));
     $player->sendMessage("§l§c➢§e Enchant §cGia tài cấp độ §e".$lv);
       }
       if($cap == 30){
           $player = $ev->getPlayer();
        $id = 18;
        $lv = 9;
			$item->setCustomName("§r§l§a〖§f⚒§a〗§a ＰICKAXE ＭINE ＡND ＦIGHT §a〖§f⚒§a〗\n§r§fCủa ".$player->getName());
       $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), $lv));
     $player->sendMessage("§l§c➢§e Enchant §cGia tài cấp độ §e".$lv);
       }
       if($cap == 31){
           $player = $ev->getPlayer();
        $id = 18;
        $lv = 10;
			$item->setCustomName("§r§l§a〖§f⚒§a〗§a ＰICKAXE ＭINE ＡND ＦIGHT §a〖§f⚒§a〗\n§r§fCủa ".$player->getName());
       $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), $lv));
     $player->sendMessage("§l§c➢§e Enchant §cGia tài cấp độ §e".$lv);
       }
       if($cap == 32){
           $player = $ev->getPlayer();
        $id = 18;
        $lv = 11;
			$item->setCustomName("§r§l§a〖§f⚒§a〗§a ＰICKAXE ＭINE ＡND ＦIGHT §a〖§f⚒§a〗\n§r§fCủa ".$player->getName());
       $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), $lv));
     $player->sendMessage("§l§c➢§e Enchant §cGia tài cấp độ §e".$lv);
       }
       if($cap == 33){
           $player = $ev->getPlayer();
        $id = 18;
        $lv = 12;
			$item->setCustomName("§r§l§a〖§f⚒§a〗§a ＰICKAXE ＭINE ＡND ＦIGHT §a〖§f⚒§a〗\n§r§fCủa ".$player->getName());
       $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), $lv));
     $player->sendMessage("§l§c➢§e Enchant §cGia tài cấp độ §e".$lv);
       }
       if($cap == 34){
           $player = $ev->getPlayer();
        $id = 18;
        $lv = 13;
			$item->setCustomName("§r§l§a〖§f⚒§a〗§a ＰICKAXE ＭINE ＡND ＦIGHT §a〖§f⚒§a〗\n§r§fCủa ".$player->getName());
       $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), $lv));
     $player->sendMessage("§l§c➢§e Enchant §cGia tài cấp độ §e".$lv);
       }
       if($cap == 35){
           $player = $ev->getPlayer();
        $id = 18;
        $lv = 14;
			$item->setCustomName("§r§l§a〖§f⚒§a〗§a ＰICKAXE ＭINE ＡND ＦIGHT §a〖§f⚒§a〗\n§r§fCủa ".$player->getName());
       $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), $lv));
     $player->sendMessage("§l§c➢§e Enchant §cGia tài cấp độ §e".$lv);
       }
       if($cap == 36){
           $player = $ev->getPlayer();
        $id = 18;
        $lv = 15;
			$item->setCustomName("§r§l§a〖§f⚒§a〗§a ＰICKAXE ＭINE ＡND ＦIGHT §a〖§f⚒§a〗\n§r§fCủa ".$player->getName());
       $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), $lv));
     $player->sendMessage("§l§c➢§e Enchant §cGia tài cấp độ §e".$lv);
       }
       if($cap == 37){
           $player = $ev->getPlayer();
        $id = 18;
        $lv = 16;
			$item->setCustomName("§r§l§a〖§f⚒§a〗§a ＰICKAXE ＭINE ＡND ＦIGHT §a〖§f⚒§a〗\n§r§fCủa ".$player->getName());
       $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), $lv));
     $player->sendMessage("§l§c➢§e Enchant §cGia tài cấp độ §e".$lv);
       }
       if($cap == 38){
           $player = $ev->getPlayer();
        $id = 18;
        $lv = 17;
			$item->setCustomName("§r§l§a〖§f⚒§a〗§a ＰICKAXE ＭINE ＡND ＦIGHT §a〖§f⚒§a〗\n§r§fCủa ".$player->getName());
       $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), $lv));
     $player->sendMessage("§l§c➢§e Enchant §cGia tài cấp độ §e".$lv);
       }
       if($cap == 39){
           $player = $ev->getPlayer();
        $id = 18;
        $lv = 18;
			$item->setCustomName("§r§l§a〖§f⚒§a〗§a ＰICKAXE ＭINE ＡND ＦIGHT §a〖§f⚒§a〗\n§r§fCủa ".$player->getName());
       $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), $lv));
     $player->sendMessage("§l§c➢§e Enchant §cGia tài cấp độ §e".$lv);
       }
       if($cap == 40){
           $player = $ev->getPlayer();
        $id = 18;
        $lv = 19;
			$item->setCustomName("§r§l§a〖§f⚒§a〗§a ＰICKAXE ＭINE ＡND ＦIGHT §a〖§f⚒§a〗\n§r§fCủa ".$player->getName());
       $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), $lv));
     $player->sendMessage("§l§c➢§e Enchant §cGia tài cấp độ §e".$lv);
       }
       if($cap == 41){
           $player = $ev->getPlayer();
        $id = 18;
        $lv = 20;
			$item->setCustomName("§r§l§a〖§f⚒§a〗§a ＰICKAXE ＭINE ＡND ＦIGHT §a〖§f⚒§a〗\n§r§fCủa ".$player->getName());
       $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), $lv));
	   $player->sendMessage("§l§c➢§e Enchant §cGia tài cấp độ §e".$lv);
       }
       if($cap > 41){
           $player = $ev->getPlayer();
        $id = 18;
        $lv = 20;
			$item->setCustomName("§r§l§a〖§f⚒§a〗§a ＰICKAXE ＭINE ＡND ＦIGHT §a〖§f⚒§a〗\n§r§fCủa ".$player->getName());
       $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), $lv));
	   $player->sendMessage("§l§c➢§e Enchant §cGia tài cấp độ §e".$lv);
       }
       if($cap >= 30 && $cap < 51){
			$player->getInventory()->setItemInHand($item);
			       $this->hp->set($ev->getPlayer()->getName(), ($this->hp->get($ev->getPlayer()->getName()) + 1));
       $this->hp->save();
       }
		$cap = $this->cap->get($ev->getPlayer()->getName());
		$money = $cap*500;
       $player = $ev->getPlayer();
       $hp = $this->hp->get($ev->getPlayer()->getName());
       $player->setMaxHealth($hp);
			 $this->getServer()->broadcastMessage("§l§c➢§e Người Chơi §f".$player->getName()."§e Vừa Lên Cúp Cấp:§f ".$cap);
			 $player->sendTitle("§l§cCÚP ĐÃ LÊN CẤP:§7 ".$cap);
			 $player->sendSubTitle("§7Nhớ lấy cúp mới để có hiệu ứng!");
        $this->cap->save();
     }
        ///Tự Nung
        $tn = $this->tn->get($ev->getPlayer()->getName());
        if($tn > 0){
          $p = $ev->getPlayer();
          $block = $ev->getBlock();
          $item = $ev->getItem()->getId();
          $ev->setInstaBreak(true);
        foreach($this->ores as $ore){
          if($block->getId() === $ore && !$ev->isCancelled()){
            $ev->setDrops(array());
            $p->getInventory()->addItem(Item::get($this->ingot[$ore]));
                $x = $p->getX();
                $y = $p->getY();
                $z = $p->getZ();
            $p->getLevel()->addSound(new EndermanTeleportSound(new Vector3($x, $y, $z)));
}
}
}

}

	public function onCommand(CommandSender $player, Command $command, String $label, array $args) : bool {
        switch($command->getName()){
            case "pickaxe":
            $this->menu($player);
            return true;
        }
        return true;
	}
	
	 public function menu($player){
        $formapi = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $formapi->createSimpleForm(function(Player $player, $data){
          $result = $data;
          if($result === null){
          }
          switch($result){
              case 0:
              break;
              case 1:
              $this->tinhnang($player);
              break;
              case 2:
              $this->thongtin($player);
              break;
              case 3:
              $this->effect($player);
              break;
              case 4:
            $inv = $player->getInventory();  
			$item = Item::get(278, 0, 1);
			$item->setCustomName("§r§l§a〖§f⚒§a〗§a ＰICKAXE ＭINE ＡND ＦIGHT §a〖§f⚒§a〗\n§r§fCủa ".$player->getName());
       $cap = $this->cap->get($player->getName());
       if($cap < 22){
        $id = 15;
        $lv = $cap - 1;
       $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), $lv));
}else{
        $id = 15;
        $lv = 20;
       $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), $lv));
}
       if($cap == 22){
           $player = $player->getPlayer();
        $id = 18;
        $lv = 1;
			$item->setCustomName("§r§l§a〖§f⚒§a〗§a ＰICKAXE ＭINE ＡND ＦIGHT §a〖§f⚒§a〗\n§r§fCủa ".$player->getName());
       $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), $lv));
}
       if($cap == 23){
           $player = $player->getPlayer();
        $id = 18;
        $lv = 2;
			$item->setCustomName("§r§l§a〖§f⚒§a〗§a ＰICKAXE ＭINE ＡND ＦIGHT §a〖§f⚒§a〗\n§r§fCủa ".$player->getName());
       $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), $lv));
       }

            if($cap == 24){
           $player = $player->getPlayer();
        $id = 18;
        $lv = 3;
			$item->setCustomName("§r§l§a〖§f⚒§a〗§a ＰICKAXE ＭINE ＡND ＦIGHT §a〖§f⚒§a〗\n§r§fCủa ".$player->getName());
       $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), $lv));
            }
       if($cap == 25){
           $player = $player->getPlayer();
        $id = 18;
        $lv = 4;
			$item->setCustomName("§r§l§a〖§f⚒§a〗§a ＰICKAXE ＭINE ＡND ＦIGHT §a〖§f⚒§a〗\n§r§fCủa ".$player->getName());
       $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), $lv));
       }
       if($cap == 26){
           $player = $player->getPlayer();
        $id = 18;
        $lv = 5;
			$item->setCustomName("§r§l§a〖§f⚒§a〗§a ＰICKAXE ＭINE ＡND ＦIGHT §a〖§f⚒§a〗\n§r§fCủa ".$player->getName());
       $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), $lv));
       }
       if($cap == 27){
           $player = $player->getPlayer();
        $id = 18;
        $lv = 6;
			$item->setCustomName("§r§l§a〖§f⚒§a〗§a ＰICKAXE ＭINE ＡND ＦIGHT §a〖§f⚒§a〗\n§r§fCủa ".$player->getName());
       $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), $lv));
       }
       if($cap == 28){
           $player = $player->getPlayer();
        $id = 18;
        $lv = 7;
			$item->setCustomName("§r§l§a〖§f⚒§a〗§a ＰICKAXE ＭINE ＡND ＦIGHT §a〖§f⚒§a〗\n§r§fCủa ".$player->getName());
       $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), $lv));
       }
       if($cap == 29){
           $player = $player->getPlayer();
        $id = 18;
        $lv = 8;
			$item->setCustomName("§r§l§a〖§f⚒§a〗§a ＰICKAXE ＭINE ＡND ＦIGHT §a〖§f⚒§a〗\n§r§fCủa ".$player->getName());
       $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), $lv));
       }
       if($cap == 30){
           $player = $player->getPlayer();
        $id = 18;
        $lv = 9;
			$item->setCustomName("§r§l§a〖§f⚒§a〗§a ＰICKAXE ＭINE ＡND ＦIGHT §a〖§f⚒§a〗\n§r§fCủa ".$player->getName());
       $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), $lv));
       }
       if($cap == 31){
           $player = $player->getPlayer();
        $id = 18;
        $lv = 10;
			$item->setCustomName("§r§l§a〖§f⚒§a〗§a ＰICKAXE ＭINE ＡND ＦIGHT §a〖§f⚒§a〗\n§r§fCủa ".$player->getName());
       $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), $lv));
       }
       if($cap == 32){
           $player = $player->getPlayer();
        $id = 18;
        $lv = 11;
			$item->setCustomName("§r§l§a〖§f⚒§a〗§a ＰICKAXE ＭINE ＡND ＦIGHT §a〖§f⚒§a〗\n§r§fCủa ".$player->getName());
       $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), $lv));
       }
       if($cap == 33){
           $player = $player->getPlayer();
        $id = 18;
        $lv = 12;
			$item->setCustomName("§r§l§a〖§f⚒§a〗§a ＰICKAXE ＭINE ＡND ＦIGHT §a〖§f⚒§a〗\n§r§fCủa ".$player->getName());
       $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), $lv));
       }
       if($cap == 34){
           $player = $player->getPlayer();
        $id = 18;
        $lv = 13;
			$item->setCustomName("§r§l§a〖§f⚒§a〗§a ＰICKAXE ＭINE ＡND ＦIGHT §a〖§f⚒§a〗\n§r§fCủa ".$player->getName());
       $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), $lv));
       }
       if($cap == 35){
           $player = $player->getPlayer();
        $id = 18;
        $lv = 14;
			$item->setCustomName("§r§l§a〖§f⚒§a〗§a ＰICKAXE ＭINE ＡND ＦIGHT §a〖§f⚒§a〗\n§r§fCủa ".$player->getName());
       $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), $lv));
       }
       if($cap == 36){
           $player = $player->getPlayer();
        $id = 18;
        $lv = 15;
			$item->setCustomName("§r§l§a〖§f⚒§a〗§a ＰICKAXE ＭINE ＡND ＦIGHT §a〖§f⚒§a〗\n§r§fCủa ".$player->getName());
       $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), $lv));
       }
       if($cap == 37){
           $player = $player->getPlayer();
        $id = 18;
        $lv = 16;
			$item->setCustomName("§r§l§a〖§f⚒§a〗§a ＰICKAXE ＭINE ＡND ＦIGHT §a〖§f⚒§a〗\n§r§fCủa ".$player->getName());
       $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), $lv));
       }
       if($cap == 38){
           $player = $player->getPlayer();
        $id = 18;
        $lv = 17;
			$item->setCustomName("§r§l§a〖§f⚒§a〗§a ＰICKAXE ＭINE ＡND ＦIGHT §a〖§f⚒§a〗\n§r§fCủa ".$player->getName());
       $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), $lv));
       }
       if($cap == 39){
           $player = $player->getPlayer();
        $id = 18;
        $lv = 18;
			$item->setCustomName("§r§l§a〖§f⚒§a〗§a ＰICKAXE ＭINE ＡND ＦIGHT §a〖§f⚒§a〗\n§r§fCủa ".$player->getName());
       $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), $lv));
       }
       if($cap == 40){
           $player = $player->getPlayer();
        $id = 18;
        $lv = 19;
			$item->setCustomName("§r§l§a〖§f⚒§a〗§a ＰICKAXE ＭINE ＡND ＦIGHT §a〖§f⚒§a〗\n§r§fCủa ".$player->getName());
       $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), $lv));
       }
       if($cap == 41){
           $player = $player->getPlayer();
        $id = 18;
        $lv = 20;
			$item->setCustomName("§r§l§a〖§f⚒§a〗§a ＰICKAXE ＭINE ＡND ＦIGHT §a〖§f⚒§a〗\n§r§fCủa ".$player->getName());
       $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), $lv));
       }
       if($cap > 41){
           $player = $player->getPlayer();
        $id = 18;
        $lv = 20;
			$item->setCustomName("§r§l§a〖§f⚒§a〗§a ＰICKAXE ＭINE ＡND ＦIGHT §a〖§f⚒§a〗\n§r§fCủa ".$player->getName());
       $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($id), $lv));
       }

			$player->getInventory()->addItem($item);

			$player->sendMessage("§l§c➢§a Đã Nhận Cúp Level Thành Công");
              break;
              case 5:
                  $this->Toplevel($player);
              break;
         }
        });
        $form->setTitle("§l§8【§c Pickaxe Level §r§8⚒ §l§8】");
        $form->setContent("§c↣ §eVui lòng lựa chọn§r");
		$form->addButton("§l§8===【§c ⇐ Thoát §8】===");
		$form->addButton("§l§8===【§c Tính Năng §8】===");
		$form->addButton("§l§8===【§c Thông Tin §8】===");
		$form->addButton("§l§8===【§c Hiệu Ứng §8】===");
		$form->addButton("§l§8===【§c Nhận Cúp §8】===");
		$form->addButton("§l§8===【§c Top Cúp §8】===");
        $form->sendToPlayer($player);
	 }

	 public function effect($player){
        $formapi = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $formapi->createSimpleForm(function(Player $player, $data){
          $result = $data;
          if($result === null){
			  $this->menu($player);
          }
          switch($result){
              case 0:
        $level = $this->cap->get($player->getName());
        if($level >= 5){
        $hs1 = $this->hs1->get($player->getName());
            if($hs1 == 1){
        $this->hs1->set($player->getName(), 0);
              $player->sendMessage("§c➢§a Bạn Đã Bật Haste Level 1");
        }elseif($hs1 == 0){
        $this->hs1->set($player->getName(), 1);
        $this->hs2->set($player->getName(), 0);
        $this->hs3->set($player->getName(), 0);
        $this->hs4->set($player->getName(), 0);
        $this->hs5->set($player->getName(), 0);
              $player->sendMessage("§c➢§a Bạn Đã Tắt Haste Level 1");
        }
        }else{
              $player->sendMessage("§l§c➢§e Bạn Không Đủ Cấp Để Sử Dụng Tính Năng Nầy");
        }
              break;
              case 1:
        $level = $this->cap->get($player->getName());
        if($level >= 10){
        $hs2 = $this->hs2->get($player->getName());
            if($hs2 == 1){
        $this->hs2->set($player->getName(), 0);
              $player->sendMessage("§c➢§a Bạn Đã Bật Haste Level 2");
        }elseif($hs2 == 0){
        $this->hs2->set($player->getName(), 1);
        $this->hs1->set($player->getName(), 0);
        $this->hs3->set($player->getName(), 0);
        $this->hs4->set($player->getName(), 0);
        $this->hs5->set($player->getName(), 0);
              $player->sendMessage("§c➢§a Bạn Đã Tắt Haste Level 2");
        }
        }else{
              $player->sendMessage("§l§c➢§e Bạn Không Đủ Cấp Để Sử Dụng Tính Năng Nầy");
        }
              break;
              case 2:
        $level = $this->cap->get($player->getName());
        if($level >= 20){
        $hs3 = $this->hs3->get($player->getName());
            if($hs3 == 1){
        $this->hs3->set($player->getName(), 0);
              $player->sendMessage("§c➢§a Bạn Đã Bật Haste Level 3");
        }elseif($hs3 == 0){
        $this->hs3->set($player->getName(), 1);
        $this->hs2->set($player->getName(), 0);
        $this->hs1->set($player->getName(), 0);
        $this->hs4->set($player->getName(), 0);
        $this->hs5->set($player->getName(), 0);
              $player->sendMessage("§c➢§a Bạn Đã Tắt Haste Level 3");
        }
        }else{
              $player->sendMessage("§l§c➢§e Bạn Không Đủ Cấp Để Sử Dụng Tính Năng Nầy");
        }
        break;
        case 3:
        $level = $this->cap->get($player->getName());
        if($level >= 30){
        $hs4 = $this->hs4->get($player->getName());
            if($hs4 == 1){
        $this->hs4->set($player->getName(), 0);
              $player->sendMessage("§c➢§a Bạn Đã Bật Haste Level 4");
        }elseif($hs4 == 0){
        $this->hs4->set($player->getName(), 1);
        $this->hs2->set($player->getName(), 0);
        $this->hs3->set($player->getName(), 0);
        $this->hs1->set($player->getName(), 0);
        $this->hs5->set($player->getName(), 0);
              $player->sendMessage("§c➢§a Bạn Đã Tắt Haste Level 4");
        }
        }else{
              $player->sendMessage("§l§c➢§e Bạn Không Đủ Cấp Để Sử Dụng Tính Năng Nầy");
        }
        break;
        case 4:
                $level = $this->cap->get($player->getName());
        if($level >= 40){
        $hs5 = $this->hs5->get($player->getName());
            if($hs5 == 1){
        $this->hs5->set($player->getName(), 0);
              $player->sendMessage("§c➢§a Bạn Đã Bật Haste Level 5");
        }elseif($hs5 == 0){
        $this->hs5->set($player->getName(), 1);
        $this->hs2->set($player->getName(), 0);
        $this->hs3->set($player->getName(), 0);
        $this->hs4->set($player->getName(), 0);
        $this->hs1->set($player->getName(), 0);
              $player->sendMessage("§c➢§a Bạn Đã Tắt Haste Level 5");
        }
        }else{
              $player->sendMessage("§l§c➢§e Bạn Không Đủ Cấp Để Sử Dụng Tính Năng Này");
        }
        break;
        case 5:
		$this->menu($player);
            break;
        
         }
        });
        $level = $this->cap->get($player->getName());
        if($level >= 5){
            $lv5 = "Đã Mở";
        }else{
            $lv5 = "Level 5 Mở";
        }
        
        if($level >= 10){
            $lv10 = "Đã Mở";
        }else{
            $lv10 = "Level 10 Mở";
        }
        if($level >= 20){
            $lv20 = "Đã Mở";
        }else{
            $lv20 = "Level 20 Mở";
        }
        if($level >= 30){
            $lv30 = "Đã Mở";
        }else{
            $lv30 = "Level 30 Mở";
        }
        if($level >= 40){
            $lv40 = "Đã Mở";
        }else{
            $lv40= "Level 40 Mở";
        }
        $form->setTitle("§l§8【§c Thông Tin §8】");
	    $form->addButton("§l§e•§c Haste Lv1 §e•\n §8".$lv5);
	    $form->addButton("§l§e•§c Haste Lv2 §e•\n §8".$lv10);
	    $form->addButton("§l§e•§c Haste Lv3 §e•\n §8".$lv20);
	    $form->addButton("§l§e•§c Haste Lv4 §e•\n §8".$lv30);
	    $form->addButton("§l§e•§c Haste Lv5 §e•\n §8".$lv40);
	    $form->addButton("§l§8【§c ⇐ Thoát §8】");
        $form->sendToPlayer($player);
       return true;
	 }
	 
	 public function thongtin($player){
        $formapi = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $formapi->createSimpleForm(function(Player $player, $data){
          $result = $data;
          if($result === null){
			  $this->menu($player);
          }
          switch($result){
              case 0:
			  $this->menu($player);
              break;
         }
        });
        $level = $this->cap->get($player->getName());
        $exp = $this->kn->get($player->getName());
        $tn = $this->tn->get($player->getName());
        if($tn == 1){
            $tnmsg = "§cBật Lên";
        }else{
            $tnmsg = "§aTắt Đi";
        }
        $bm = $this->bm->get($player->getName());
        if($bm == 1){
            $bmmsg = "§cBật Lên";
        }else{
            $bmmsg = "§aTắt Đi"; 
        }
        $maxexp = $level * 250;
        $form->setTitle("§l§8【§c Thông Tin §8】");
        $form->setContent("§l§c➢§e Name: §f".$player->getName()."\n§l§c➢§e Level: §f".$level."\n§l§c➢§e Exp: §f".$exp."§2/§f".$maxexp."\n§l§c➢§e Tính Năng\n§b-§a Auto Nung: ".$tnmsg."\n§b-§a Đập Bock nhận Money: ".$bmmsg."\n§l§c➢§e Effect:\n§b-§a Tốc Độ Đào: ?");	
        $form->addButton("§l§8【§c ⇐ Thoát §8】");
        $form->sendToPlayer($player);
       return true;
	 }

	 public function tinhnang($player){
        $formapi = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $formapi->createSimpleForm(function(Player $player, $data){
          $result = $data;
          if($result === null){
			  $this->menu($player);
          }
          switch($result){
              case 0:
        $level = $this->cap->get($player->getName());
//Tự Nung
        if($level >= 10){
             $this->autonung($player);
        }else{
              $player->sendMessage("§l§c➢§e Bạn Không Đủ Cấp Để Sử Dụng Tính Năng Này");
        }
               break;
                case 1:
        $level = $this->cap->get($player->getName());
//Block Momey
        if($level >= 20){
             $this->blockmoney($player);
        }else{
              $player->sendMessage("§l§c➢§e Bạn Không Đủ Cấp Để Sử Dụng Tính Năng Này");
        }
              break;
              case 2:
             $level = $this->cap->get($player->getName());
             if($level >= 30){
                 $player->sendMessage("§l§c➢§e Tính Năng Tự Động");
             }else{
                 $player->sendMessage("§l§c➢§e Bạn Không Đủ Cấp Để Sử Dụng Tính Năng Này");
             }
             break;
              case 3:
              $player->sendMessage("§l§c➢§e Tính Năng Đang Cập Nhật");
                  break;
			  case 4:
			  $this->menu($sender);
			  break;
         }
        });
        $level = $this->cap->get($player->getName());
        //Tự Nung
        if($level >= 10){
            $lv10 = "Đã Mở";
        }else{
            $lv10 = "level 10 Mở";
        }
    //Mềm Mại
        if($level >= 20){
            $lv20 = "Đã Mở";
        }else{
            $lv20 = "level 20 Mở";
        }
        //Thêm Hp
        if($level >= 30){
            $lv30 = "Tính Năng Tự Động";
        }else{
            $lv30 = "level 30 Mở";
        }
        
        //Mềm Mại
        //Thêm Hp
        if($level >= 40){
            $lv40 = "Đã Mở";
        }else{
            $lv40 = "level 40 Mở";
        }
        
        $form->setTitle("§l§8【§c Tính Năng Của Cúp §8】");
        $form->addButton("§l§e•§c Tự Nung §e•\n§8".$lv10);
        $form->addButton("§l§e•§c Đập Block Nhận Money §e•\n§8".$lv20);
        $form->addButton("§l§e•§c Thêm Máu §e•\n§8".$lv30);
        $form->addButton("§l§e•§c Mềm Mại §e•\n§8".$lv40);
        $form->sendToPlayer($player);
       return true;
	 }
	 
	 public function autonung($player){
        $formapi = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $formapi->createSimpleForm(function(Player $player, $data){
          $result = $data;
          if($result === null){
			  $this->menu($player);
          }
          switch($result){
              case 0:
        $this->tn->set($player->getPlayer ()->getName(), 1);
              $player->sendMessage("§l§c➢§a Auto Nung Sắt Vàng Mở");
              break;
              case 1:
        $this->tn->set($player->getPlayer ()->getName(), 0);
              $player->sendMessage("§l§c➢§e Auto Nung Sắt Vàng Tắt");
              break;
              case 2:
			  $this->menu($player);
              break;
         }
        });
        $form->setTitle("§l§8【§c Thông Tin §8】");
        $form->setContent("§l§c➢§a Tự Nung Sắt Và Vàng");	
        $form->addButton("§l§e•§c On §e•");
        $form->addButton("§l§e•§c Off §e•");
        $form->addButton("§l§8【§c ⇐ Thoát §8】");
        $form->sendToPlayer($player);
       return true;
	 }
	 
	 public function blockmoney($player){
        $formapi = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $formapi->createSimpleForm(function(Player $player, $data){
          $result = $data;
          if($result === null){
			  $this->menu($player);
          }
          switch($result){
              case 0:
        $this->bm->set($player->getPlayer ()->getName(), 1);
              $player->sendMessage("§l§c➢§a Đào Block Nhận Money Mở");
              break;
              case 1:
        $this->bm->set($player->getPlayer ()->getName(), 0);
              $player->sendMessage("§l§c➢§e Đào Block Nhận Money Tắt");
              break;
              case 2:
			  $this->menu($player);
              break;
         }
        });
        $form->setTitle("§l§8【§c Thông Tin §8】");
        $form->setContent("§l§c➢§a Đập Block Nhận Money");	
        $form->addButton("§l§e•§c On §e•");
        $form->addButton("§l§e•§c Off §e•");
        $form->addButton("§l§8【§c ⇐ Thoát §8】");
        
        $form->sendToPlayer($player);
       return true;
	 }

	public function Toplevel(Player $player){
		$levelplot = $this->cap->getAll();
		$message = "";
		$message1 = "";
		if(count($levelplot) > 0){
			arsort($levelplot);
			$i = 1;
			foreach($levelplot as $name => $level){
				$message .= "§l§3TOP " . $i . ": §6" . $name . " §d→ §f" . $level . " §2Cấp\n\n";
				$message1 .= "§l§3TOP " . $i . ": §6" . $name . " §d→ §f" . $level . " §2Cấp\n";
				if($i >= 10){
					break;
				}
				++$i;
			}
		}
		
		$formapi = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
		$form = $formapi->createSimpleForm(function (Player $player, ?int $data = null){
			$result = $data;
			switch($result){
				case 0:
				$this->menu($player);
				break;
			}
		});
		$form->setTitle("§l§8【§c TOP LEVEL §8】");
		$form->setContent("§l§cDanh Sách TOP 10 Pickaxe:");
		$form->setContent($message);
		$form->addButton("§l§8【§c ⇐ Trở Lại §8】");
		$form->sendToPlayer($player);
		return true;
	}
}