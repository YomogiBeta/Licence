<?php

namespace Product\key;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;

use pocketmine\utils\Config;

use pocketmine\Player;
use pocketmine\Server;

use pocketmine\event\player\PlayerJoinEvent;

use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\event\server\DataPacketReceiveEvent;

class licence extends PluginBase implements Listener{

    public function onEnable(){
      $this->getServer()->getPluginManager()->registerEvents($this, $this);
      if (!file_exists($this->getDataFolder()))
          mkdir($this->getDataFolder(), 0775, true);
      $this->player = new Config($this->getDataFolder() . "Player.yml", Config::YAML);
      if (!file_exists($this->getDataFolder().'Licence.txt'))
         copy( __DIR__ .'/../../../resorce/Licence.txt', $this->getDataFolder().'Licence.txt');
      $this->licenceString = file_get_contents($this->getDataFolder().'Licence.txt');

      $this->formId = mt_rand(1000, 999999);
      $this->getLogger()->info("§cI loaded myself");
    }

  public function onJoin(PlayerJoinEvent $event){
    if(!$this->player->exists($event->getPlayer()->getName())){
      $this->sentLicence($event->getPlayer());
    }
  }

  public function sentLicence($player){
    $data = [
           "type" => "form",
           "title" => "§l§fLicence",
           "content" => $this->licenceString,
           "buttons"=>[
             [
              "text" => "§lI accept"//0
             ],
             [
               "text" => "§lDisagree"//1
             ]
           ]
    ];
    $pk = new ModalFormRequestPacket();
    $pk->formId = $this->formId;
    $pk->formData = json_encode($data);
    $player->dataPacket($pk);
  }

  public function onData(DataPacketReceiveEvent $event)
  {
    $pk = $event->getPacket();
    if (!$pk instanceof ModalFormResponsePacket) {
      return;
  }
  if($pk->formId === $this->formId){
    $formData = json_decode($pk->formData);
    $name = $event->getPlayer()->getName();
    if(!isset($formData)){
      if(!isset($this->able[$name])){
        $this->sentLicence($event->getPlayer());
      }else{
        unset($this->able[$name]);
      }
      return;
    }
   switch ($formData) {
     case 0:
            $this->player->set($name,true);
            $this->player->save();
            $event->getPlayer()->sendMessage("§dYou have agreed to the terms of service");
            $this->able[$name] = true;
            return;
            break;
     case 1:
            $event->getPlayer()->kick("I'm sorry. If you do not agree to the terms of use, we can not use this server",false);
            return;
            break;
   }
  }
}
}//fin class
