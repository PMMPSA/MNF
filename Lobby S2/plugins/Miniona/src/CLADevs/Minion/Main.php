<?php

declare(strict_types=1);

namespace CLADevs\Minion;

use CLADevs\Minion\minion\Minion;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as C;
use pocketmine\utils\Config;
use pocketmine\tile\Chest;
use onebone\economyapi\EconomyAPI;
use pocketmine\Server;
use muqsit\invmenu\InvMenuHandler;
use CLADevs\Minion\database\Database;
use CLADevs\Minion\database\Vault;
use pocketmine\entity\Skin;
use jojoe77777\FormAPI\{SimpleForm, CustomForm};
use pocketmine\block\Block;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\item\Tool;
use pocketmine\item\Pickaxe;
//use phuongaz\coin\Coin;
use onebone\pointapi\PointAPI;
class Main extends PluginBase{

    CONST SELL = 0;
    CONST ORE = 1;
    CONST DOUBLE_CHEST = 2;

    private $database;
    private static $instance;
    private $sell;

    public function onLoad(): void{
        self::$instance = $this;
    }

    public function onEnable(): void{
        $this->initVirions();
        $this->createDatabase();
        $this->saveResource("img/geometry.json");
        $this->saveResource("img/Minion.png");
        $this->saveResource("sell.yml");
        $this->sell = new Config($this->getDataFolder() . "sell.yml", Config::YAML);     
        Entity::registerEntity(Minion::class, true);
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
        Vault::setNameFormat((string) $this->getConfig()->get("inventory-name"));
    }

    public function getDatabase() : Database{
        return $this->database;
    }

    public function onDisable() : void{
        $this->getDatabase()->close();
    }

    private function initVirions() : void{
        if(!InvMenuHandler::isRegistered()){
            InvMenuHandler::register($this);
        }
    }

    private function createDatabase() : void{
        $this->saveDefaultConfig();
        $this->database = new Database($this, $this->getConfig()->get("database"));
        Vault::setNameFormat((string) $this->getConfig()->get("inventory-name"));
    }

    public static function get(): self{
        return self::$instance;
    }

    public function sell(Player $player, $tile) {
        $items = $tile->getContents();
        $prices = 0;
        foreach($items as $item){
            if($this->sell->get($item->getId()) !== null && $this->sell->get($item->getId()) > 0){
                $price = $this->sell->get($item->getId()) * $item->getCount();
                EconomyAPI::getInstance()->addMoney($player, $price);
                $money = $this->sell->get($item->getId());
                $count = $item->getCount();
                $iname = $item->getName();
                $prices = $prices + $price;
                $tile->remove($item);
            }
        }
        $player->sendMessage("??l??e???> ??6Minion ???? b??n ???????c:??a $prices xu");
    }

    public function getBanBlocks() :array {
        if(($blocks = (array)$this->getConfig()->get("ban-blocks")) !== null){
            return $blocks;
        }
        return $null = [];
    }

    public function getSkin(){
        $file = glob($this->getDataFolder() . "img" . DIRECTORY_SEPARATOR . "*.png");
        foreach($file as $file){
             $fileName = pathinfo($file, PATHINFO_FILENAME);
            $path = $this->getDataFolder() . "img" . DIRECTORY_SEPARATOR . "$fileName.png";
            $img = @imagecreatefrompng($path);
            $skinbytes = "";
            $s = (int)@getimagesize($path)[1];
            for($y = 0; $y < $s; $y++){
                for($x = 0; $x < 64; $x++){
                    $colorat = @imagecolorat($img, $x, $y);
                    $a = ((~((int)($colorat >> 24))) << 1) & 0xff;
                    $r = ($colorat >> 16) & 0xff;
                    $g = ($colorat >> 8) & 0xff;
                    $b = $colorat & 0xff;
                    $skinbytes .= chr($r) . chr($g) . chr($b) . chr($a);
                }
            }
            @imagedestroy($img);
            return new Skin("Standard_CustomSlim", $skinbytes, "", "geometry.$fileName", file_get_contents($this->getDataFolder() . "img" . DIRECTORY_SEPARATOR ."geometry.json"));           
        }
    }
    
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
     
            if(!$sender->hasPermission("minion.commands")){
                $sender->sendMessage(C::RED . "??cB???n kh??ng c?? quy???n ????? d??ng l???nh.");
                return false;
            }
            if($sender instanceof ConsoleCommandSender){
                if(!isset($args[0])){
                    $sender->sendMessage("??aS??? D???NG: /minion <player>");
                    return false;
                }
                if(!$p = $this->getServer()->getPlayer($args[0])){
                    $sender->sendMessage(C::RED . "??cKh??ng th??? t??m th???y ng?????i ch??i.");
                    return false;
                }
                if($p->getInventory()->canAddItem($this->getItem($p))){
                    $p->sendMessage("??l??e???> ??6B???n ???? mua th??nh c??ng 1 Minion. Vui l??ng t??? b???o qu???n, n???u m???t server kh??ng ch???u tr??ch nhi???m.");
                    $this->giveItem($p);
                      $sender->sendMessage("??6???? trao minion cho: ". $p->getName());                                        
                } else $sender->sendMessage($p->getName(). " t??i ????? ???? ?????y!");
                return false;
            }elseif($sender instanceof Player){
                if(isset($args[0])){
                    if(!$p = $this->getServer()->getPlayer($args[0])){
                        $sender->sendMessage(C::RED . "Kh??ng th??? t??m th???y ng?????i ch??i.");
                        return false;
                    }
                    $this->giveItem($p);
                    return false;
                }
                $this->giveItem($sender);
                return false;
            }
        
        return true;
    }

    public function giveItem(Player $sender): void{
        $sender->getInventory()->addItem($this->getItem($sender));
    }

    public function getItem(Player $sender, int $level = 1): Item{
        $item = Item::get(Item::SPAWN_EGG);
        $item->setCustomName(C::BOLD . C::BLUE . "??r??d??? Tr???ng Minion ??? ??f- ??6Xem c??ch d??ng ??? t??i ?????");
        $item->setLore(
            ["\n\n??r??l??a???> C??CH D??NG: ??r??f?????ng ?????i di???n v???i m??y farm v?? ?????t minion xu???ng ??? d?????i kh???i b???n ?????ng",
            "??r??l??c???> L??U ??: ??r??fMinion l?? do b???n t??? b???o qu???n, khi x???y ra m???t s??? kh??ng gi???i quy???t"]
        );
        $nbt = $item->getNamedTag();
        $nbt->setString("summon", "miner");
        $nbt->setString("player", $sender->getName());
        $item->setNamedTag($nbt);
        return $item;
    }

    public function sendStaffForm(Player $player, string $owner, Minion $minion) :void {
        $form = new SimpleForm(function(Player $player, $data) use ($minion, $owner){
            if(is_null($data)) return;
            if($data === 0) $this->flagEntity($player, $minion);
            if($data === 1) $this->sendMinionInv($player, $minion, $owner);
            if($data === 2) $this->setItemToMinion($player, $minion); 
        });
        $form->setTitle("??lMINION - ????? T???");
        $form->addButton("??l??8?????l ??9THU H???I L???I MINION ??8???\n??r??8L???y l???i ????? b???n", 1, "https://img.icons8.com/officel/2x/delete-sign.png"); 
        $form->addButton("??l??8?????l ??9T??I ????? C???A MINION ??8???\n??r??8M??? kho c???a ?????", 1, "https://cdn.iconscout.com/icon/free/png-512/backpack-50-129941.png");
        $form->addButton("??l??8?????l ??9MENU C??P MINION ??8???\n??r??8Cho / l???y c??p ?????", 1, "https://art.pixilart.com/a1318a07386930f.png");
        $form->sendToPlayer($player);        
    }

    public function sendForm($player, $minion){
        $form = new SimpleForm(function(Player $player, $data) use ($minion){
            if(is_null($data)) return;
            if($data === 0) $this->flagEntity($player, $minion);
            if($data === 1) $this->sendMinionInv($player, $minion);
            if($data === 2) $this->setItemToMinion($player, $minion); 
            if($data === 3) $this->upgradeMinion($player, $minion);
        });
        $form->setTitle("??lMINION - ????? T???");
        $form->addButton("??l??8?????l ??9THU H???I L???I MINION ??8???\n??r??8L???y l???i ????? b???n", 1, "https://img.icons8.com/officel/2x/delete-sign.png"); 
        $form->addButton("??l??8?????l ??9T??I ????? C???A MINION ??8???\n??r??8M??? kho c???a ?????", 1, "https://cdn.iconscout.com/icon/free/png-512/backpack-50-129941.png");
        $form->addButton("??l??8?????l ??9MENU C??P MINION ??8???\n??r??8Cho / l???y c??p ?????", 1, "https://art.pixilart.com/a1318a07386930f.png");
        $form->addButton("??l??8?????l ??9N??NG C???P MINION ??8???\n??r??8M??? t??nh n??ng ?????", 1, "https://cdn0.iconfinder.com/data/icons/round-arrows-1/134/Up_blue-512.png");
        $form->sendToPlayer($player);
    }

    public function upgradeMinion(Player $player, $minion) :void{
        $form = new SimpleForm(function(Player $player, ?int $data) use ($minion){
            if(is_null($data)){
                $this->sendForm($player, $minion);
                return;
            }
            switch($data){
				case 0:
				    if($this->hasPer($data, $player)){
                    $form->addLabel("??cT??nh n??ng n??y ???? ???????c n??ng c???p!");
                    $form->sendToPlayer($player);
				       return;
				    }
                   $form = new CustomForm(function(Player $player, ?array $data){});
                   if(PointAPI::getInstance()->myPoint($player) >= 30){
                   Server::getInstance()->getCommandMap()->dispatch(new ConsoleCommandSender(), "setuperm ".$player->getName(). " minion.".$data);
                   PointAPI::getInstance()->reducePoint($player, 30);
                   $form->addLabel("??aN??ng c???p minion th??nh c??ng, glhf");
                   $form->sendToPlayer($player);
            }else{
                $player->sendMessage("??l??e???> ??cKh??ng ????? point ????? n??ng c???p ?????, vui l??ng ki???m th??m");
                }
            }
        });
        $form->setTitle("??lN??NG C???P MINION - ????? T???");
        $form->setContent("??l??6Point c???a b???n:??e ". PointAPI::getInstance()->myPoint($player));
        $form->addButton("??l??8?????9 T??? ?????NG B??N ??8???\n??r??830 Points??8");
        $form->addButton("??l??8?????9 T??? ?????NG NUNG QU???NG ??8???\n??r??830 Points??8");
        //$form->addButton("M??? r???ng t??i ?????\n(50 LCoin)");
        $form->sendToPlayer($player);
    }

    public function sendMinionInv($player, $minion, $owner = null){
        if($owner !== null){
            $this->getDatabase()->loadVault($owner, 1, function(Vault $vault) use($player): void{
                $vault->send($player);
            });            
        }else{
            $this->getDatabase()->loadVault($player->getName(), 1, function(Vault $vault) use($player): void{
                $vault->send($player);
            });       
        }
    }

    public function setMoveType($player, $minion) {
        $form = new CustomForm(function(Player $player, $data) use ($minion){
            if(is_null($data)) return;
            if($data[1] == true){
                $minion->setMove(true);

            }else $minion->setMove(false);
        });
        $form->setTitle("??lMINION - ????? T???");
        $form->addLabel("");
        $form->addToggle("??a?????l??9 B???T/T???T DI CHUY???N", $minion->getMoveType());
        $form->sendToPlayer($player);
    }

    public function flagEntity($player, $entity){
        $hand = $entity->getInventory()->getItemInHand();
        if($hand->getId() !== Item::AIR){
            $player->sendMessage("??l??e???> ??cVui l??ng l???y c??p ra tr?????c khi cho Minion ngh???");
            return;
        }
        if($player->getInventory()->canAddItem($this->getItem($player))){
            $block = $entity->getLookingBlock();
            $entity->setDelay(1);
            $entity->flagForDespawn();
            $block = $entity->getLookingBlock();
            $player->sendMessage("??l??e???>??a Minion ???? quay tr??? l???i t??i c???a b???n, h??y ki???m tra t??i nh??");
            if($block->getId() !== Block::AIR) $this->stopBreakAnimation($block);
            $this->giveItem($player);            
        }else{
           $player->sendMessage("??l??e???> ??cT??i ????? c???a b???n ???? ?????y, h??y d???n b???t tr?????c khi l???y l???i ?????");  
        }
    }

    public function setItemToMinion($player, $minion){
        $form = new SimpleForm(function(Player $player, $data) use ($minion){
            if(is_null($data)){  }
            if($data === 0){
                $handm = $minion->getInventory()->getItemInHand();
                $handp = $player->getInventory()->getItemInHand();
                if(!($handp instanceof Tool) and $handp instanceof Pickaxe){
                    $player->sendMessage("??l??e???>??c Minion ch??? c???n c??ng c???, kh??ng ph???i c??i n??y :v!");
                    return;
                }
                $minion->getInventory()->setItemInHand($handp);
                $player->getInventory()->setItemInHand($handm); 
                $block = $minion->getLookingBlock();
                if($block->getId() !== Block::AIR) $this->stopBreakAnimation($block);
                $minion->setDelay(1);
                $minion->respawnToAll();
                $block = $minion->getLookingBlock();
              //  $minion->breakBlock($block);
                $this->stopBreakAnimation($block);
            }
            if($data === 1){
                $handm = $minion->getInventory()->getItemInHand();
                if($handm->getId() !== Item::AIR){
                    if($player->getInventory()->canAddItem($handm)){
                        $player->getInventory()->addItem($handm);
                        $minion->getInventory()->setItemInHand(Item::get(Item::AIR));
                        $block = $minion->getLookingBlock();
                        $minion->setDelay(1);                    
                        if($block->getId() !== Block::AIR) $this->stopBreakAnimation($block);
                        $minion->respawnToAll();                         
                    }else $player->sendMessage("??l??e???> ??cT??i ????? c???a b???n ???? ?????y, vui l??ng d???n b???t");
                }
            }
        });
        $form->setTitle("??lQU???N L?? MINION");
        $handitem = $minion->getInventory()->getItemInHand();
        $contents = "";
        if(($enchantments = $this->getAllEnchantments($handitem)) !== null){
            $content = [];
            foreach ($enchantments as $enchantment) {
                $name = $enchantment->getType()->getName();
                $level = $enchantment->getLevel();
                $content[] = "$name : $level";
            }
            $contents = implode("\n", $content);
        }
        $form->setContent("??c??? ??eMinion ??ang c???m v???t ph???m: ". $handitem->getName(). "\n". $contents ."\n\n??c??? ??eB???n c?? th??? cho minion m?????n c??p c???a b???n ?????y!");
        $form->addButton("??8??l?????l ??9Cho Minion m?????n c??p ??8???\n??r??8Cho ????? m?????n c??p tr??n tay b???n");
        $form->addButton("??8??l?????l ??9????i l???i c??p c???a Minion ??8???\n??r??8L???y l???i c??p b???n ???? cho ?????");
        $form->sendToPlayer($player);
    }

    public function getAllEnchantments($item) :?array {
        if($item->hasEnchantments()){
            $enchantments = [];
            foreach($item->getEnchantments() as $enchantment){
                $enchantments[] = $enchantment;
            }
            return $enchantments;
        }
        return null;
    }

    public function checkName($player){
        $name = $player->getName();
        if($this->hasSpaces($name)){
            $name = str_replace(" ", "_", $name);
        }
        return $name;
    }

    private function hasSpaces(string $string): bool{
        return $string === trim($string) && strpos($string, ' ') !== false;
    }

    public function stopBreakAnimation(Block $block) :void {
        $pos = $block->asPosition();
        $pos->level->broadcastLevelEvent($pos, LevelEventPacket::EVENT_BLOCK_STOP_BREAK);
    }

    public function hasPer(int $type, Player $player) :bool{
        return ($player->hasPermission("minion.".$type));
    }
}
