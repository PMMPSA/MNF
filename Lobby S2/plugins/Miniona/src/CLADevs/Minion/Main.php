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
        $player->sendMessage("§l§e•> §6Minion đã bán được:§a $prices xu");
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
                $sender->sendMessage(C::RED . "§cBạn không có quyền để dùng lệnh.");
                return false;
            }
            if($sender instanceof ConsoleCommandSender){
                if(!isset($args[0])){
                    $sender->sendMessage("§aSỬ DỤNG: /minion <player>");
                    return false;
                }
                if(!$p = $this->getServer()->getPlayer($args[0])){
                    $sender->sendMessage(C::RED . "§cKhông thể tìm thấy người chơi.");
                    return false;
                }
                if($p->getInventory()->canAddItem($this->getItem($p))){
                    $p->sendMessage("§l§e•> §6Bạn đã mua thành công 1 Minion. Vui lòng tự bảo quản, nếu mất server không chịu trách nhiệm.");
                    $this->giveItem($p);
                      $sender->sendMessage("§6Đã trao minion cho: ". $p->getName());                                        
                } else $sender->sendMessage($p->getName(). " túi đồ đã đầy!");
                return false;
            }elseif($sender instanceof Player){
                if(isset($args[0])){
                    if(!$p = $this->getServer()->getPlayer($args[0])){
                        $sender->sendMessage(C::RED . "Không thể tìm thấy người chơi.");
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
        $item->setCustomName(C::BOLD . C::BLUE . "§r§d【 Trứng Minion 】 §f- §6Xem cách dùng ở túi đồ");
        $item->setLore(
            ["\n\n§r§l§a•> CÁCH DÙNG: §r§fĐứng đối diện với máy farm và đặt minion xuống ở dưới khối bạn đứng",
            "§r§l§c•> LƯU Ý: §r§fMinion là do bạn tự bảo quản, khi xảy ra mất sẽ không giải quyết"]
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
        $form->setTitle("§lMINION - ĐỆ TỬ");
        $form->addButton("§l§8【§l §9THU HỒI LẠI MINION §8】\n§r§8Lấy lại đệ bạn", 1, "https://img.icons8.com/officel/2x/delete-sign.png"); 
        $form->addButton("§l§8【§l §9TÚI ĐỒ CỦA MINION §8】\n§r§8Mở kho của đệ", 1, "https://cdn.iconscout.com/icon/free/png-512/backpack-50-129941.png");
        $form->addButton("§l§8【§l §9MENU CÚP MINION §8】\n§r§8Cho / lấy cúp đệ", 1, "https://art.pixilart.com/a1318a07386930f.png");
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
        $form->setTitle("§lMINION - ĐỆ TỬ");
        $form->addButton("§l§8【§l §9THU HỒI LẠI MINION §8】\n§r§8Lấy lại đệ bạn", 1, "https://img.icons8.com/officel/2x/delete-sign.png"); 
        $form->addButton("§l§8【§l §9TÚI ĐỒ CỦA MINION §8】\n§r§8Mở kho của đệ", 1, "https://cdn.iconscout.com/icon/free/png-512/backpack-50-129941.png");
        $form->addButton("§l§8【§l §9MENU CÚP MINION §8】\n§r§8Cho / lấy cúp đệ", 1, "https://art.pixilart.com/a1318a07386930f.png");
        $form->addButton("§l§8【§l §9NÂNG CẤP MINION §8】\n§r§8Mở tính năng đệ", 1, "https://cdn0.iconfinder.com/data/icons/round-arrows-1/134/Up_blue-512.png");
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
                    $form->addLabel("§cTính năng này đã được nâng cấp!");
                    $form->sendToPlayer($player);
				       return;
				    }
                   $form = new CustomForm(function(Player $player, ?array $data){});
                   if(PointAPI::getInstance()->myPoint($player) >= 30){
                   Server::getInstance()->getCommandMap()->dispatch(new ConsoleCommandSender(), "setuperm ".$player->getName(). " minion.".$data);
                   PointAPI::getInstance()->reducePoint($player, 30);
                   $form->addLabel("§aNâng cấp minion thành công, glhf");
                   $form->sendToPlayer($player);
            }else{
                $player->sendMessage("§l§e•> §cKhông đủ point để nâng cấp đệ, vui lòng kiếm thêm");
                }
            }
        });
        $form->setTitle("§lNÂNG CẤP MINION - ĐỆ TỬ");
        $form->setContent("§l§6Point của bạn:§e ". PointAPI::getInstance()->myPoint($player));
        $form->addButton("§l§8【§9 TỰ ĐỘNG BÁN §8】\n§r§830 Points§8");
        $form->addButton("§l§8【§9 TỰ ĐỘNG NUNG QUẶNG §8】\n§r§830 Points§8");
        //$form->addButton("Mở rộng túi đồ\n(50 LCoin)");
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
        $form->setTitle("§lMINION - ĐỆ TỬ");
        $form->addLabel("");
        $form->addToggle("§a⚫§l§9 BẬT/TẮT DI CHUYỂN", $minion->getMoveType());
        $form->sendToPlayer($player);
    }

    public function flagEntity($player, $entity){
        $hand = $entity->getInventory()->getItemInHand();
        if($hand->getId() !== Item::AIR){
            $player->sendMessage("§l§e•> §cVui lòng lấy cúp ra trước khi cho Minion nghỉ");
            return;
        }
        if($player->getInventory()->canAddItem($this->getItem($player))){
            $block = $entity->getLookingBlock();
            $entity->setDelay(1);
            $entity->flagForDespawn();
            $block = $entity->getLookingBlock();
            $player->sendMessage("§l§e•>§a Minion đã quay trở lại túi của bạn, hãy kiểm tra túi nhé");
            if($block->getId() !== Block::AIR) $this->stopBreakAnimation($block);
            $this->giveItem($player);            
        }else{
           $player->sendMessage("§l§e•> §cTúi đồ của bạn đã đầy, hãy dọn bớt trước khi lấy lại đệ");  
        }
    }

    public function setItemToMinion($player, $minion){
        $form = new SimpleForm(function(Player $player, $data) use ($minion){
            if(is_null($data)){  }
            if($data === 0){
                $handm = $minion->getInventory()->getItemInHand();
                $handp = $player->getInventory()->getItemInHand();
                if(!($handp instanceof Tool) and $handp instanceof Pickaxe){
                    $player->sendMessage("§l§e•>§c Minion chỉ cần công cụ, không phải cái này :v!");
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
                    }else $player->sendMessage("§l§e•> §cTúi đồ của bạn đã đầy, vui lòng dọn bớt");
                }
            }
        });
        $form->setTitle("§lQUẢN LÝ MINION");
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
        $form->setContent("§c↣ §eMinion đang cầm vật phẩm: ". $handitem->getName(). "\n". $contents ."\n\n§c↣ §eBạn có thể cho minion mượn cúp của bạn đấy!");
        $form->addButton("§8§l【§l §9Cho Minion mượn cúp §8】\n§r§8Cho đệ mượn cúp trên tay bạn");
        $form->addButton("§8§l【§l §9Đòi lại cúp của Minion §8】\n§r§8Lấy lại cúp bạn đã cho đệ");
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
