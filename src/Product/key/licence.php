<?php

namespace Product\key;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;

use pocketmine\utils\Config;

use pocketmine\event\player\PlayerJoinEvent;

use Product\key\SimpleLicenceForm;

class Licence extends PluginBase implements Listener
{

  private Config $agreePlayers;

  public function onEnable(): void
  {
    $this->getServer()->getPluginManager()->registerEvents($this, $this);

    if (!file_exists($this->getDataFolder())) mkdir($this->getDataFolder(), 0775, true);

    $this->agreePlayers = new Config($this->getDataFolder() . "Player.yml", Config::YAML);

    if (!file_exists($this->getDataFolder() . 'Licence.txt')) copy(__DIR__ . '/../../../resource/Licence.txt', $this->getDataFolder() . 'Licence.txt');
  }

  public function onJoin(PlayerJoinEvent $event): void
  {
    if (!$this->agreePlayers->exists($event->getPlayer()->getName())) {
      $this->askTermsOfService($event->getPlayer());
    }
  }

  public function addAgreeUser(string $name): void
  {
    $this->agreePlayers->set($name, date(DATE_ATOM));
    $this->agreePlayers->save();
  }

  private function askTermsOfService($player): void
  {
    $player->sendForm(new SimpleLicenceForm($this->getLicenceText(), $this));
  }

  private function getLicenceText(): string
  {
    return file_get_contents($this->getDataFolder() . 'Licence.txt');
  }
}//fin class
