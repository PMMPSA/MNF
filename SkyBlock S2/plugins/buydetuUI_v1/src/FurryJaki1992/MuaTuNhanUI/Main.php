<?php

namespace FurryJaki1992\MuaTuNhanUI;

use pocketmine\Server;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\{command\ConsoleCommandSender, Player, utils\TextFormat};

use jojoe77777\FormAPI;

class Main extends PluginBase implements Listener{

    public function onEnable(): void{
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->Info("§cMuaTuNhan §eCode By §aFurryJaki1992");
        $this->pointapi = $this->getServer()->getPluginManager()->getPlugin("PointAPI");
    }

    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool{
        switch($cmd->getName()) {
            case "muadetu":
            if(!($sender instanceof Player)){
                return true;
            }
        $this->Fu1($sender);
        }
        return true;
    }

    
    public function Fu1($sender){
        $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createModalForm(function (Player $sender, $data){
        $result = $data;
        if ($result == null) {
             }
             switch ($result) {
                 case 1:
                 $point = $this->pointapi->myPoint($sender);
                 $cost = 110;
                 if($point >= $cost){
        $id = $sender->getInventory()->getItemInHand()->getId();
     if($id == 0){
                     $this->pointapi->reducePoint($sender, $cost);
                     $this->getServer()->dispatchCommand(new ConsoleCommandSender(), "detu " . $sender->getName());
                     $sender->sendMessage("§e-§a Bạn đã mua thành công x1 đệ tử");
     }else{
         $sender->sendMessage("§aVui lòng cầm tay không trước khi mua đệ");
     }
                     return true;
            }else{
                $sender->sendMessage("§cBạn không đủ points để mua đệ tử");
            }
            break;
                    case 2:
                        break;
            }
        });
        $form->setTitle("§lMENU MUA ĐỆ TỬ ");
        $form->setContent("§eBẠN CÓ MUỐN MUA ĐỆ TỬ GIÁ: 110 POINTS?");
        $form->setButton1("§l§8[§a✔§8] MUA NGAY");
        $form->setButton2("§l§8[§c✘§8] KHÔNG MUA");
        $form->sendToPlayer($sender);
    }
}