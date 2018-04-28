<?php

namespace spamban;

use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\Server;

class Main extends PluginBase implements Listener{

	public function onEnable ()
	{
		$this->lc = array();
		$this->spam = array();
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function onChat (PlayerChatEvent $event)
	{
		$this->spam($event->getPlayer());
    }

    public function precommand(PlayerCommandPreProcessEvent $event){
	    //https://github.com/ItzBulkDev/CustomHelp/blob/5145a2670f60715405d726d45c5249ab978ba452/src/CustomHelp/Main.php
        $message = $event->getMessage();
        $command = substr($message, 1);
        $args = explode(" ", $command);
        if($args[0] === "me" || $args[0] === "say"){
            $this->spam($event->getPlayer());
        }
    }

    public function spam(Player $player){
        $name   = $player->getName();
        if(isset($this->lc[$name])){
            if(time() - $this->lc[$name]<=5){
                if(!isset($this->spam[$name])){
                    $this->spam[$name]=0;
                }else {
                    $this->spam[$name]++;
                }
            }else{
                $this->spam[$name]=0;
            }

            if($this->spam[$name]>10){
                $reason = " [SPAMBAN] ".$name."をbanipしてあげました。";
                $ip = $player->getAddress();
                $this->getServer()->broadcastMessage(" [SPAMBAN] ".$name."をbanipしてあげました。".$ip);
                $player->getServer()->getIPBans()->addBan($ip,$reason);//ip-ban
                $player->getServer()->getNameBans()->addBan($name,$reason);//ban
                $player->kick("spamspamspaspamspamwspsamejdjjwjjfjdjdjdjsapmspamm",false);
            }
        }
        $this->lc[$name] = time();
    }
    public function PlayerDeath(PlayerDeathEvent $event){
        $event->setKeepInventory(true);
    }

    public function noTNt(EntityExplodeEvent $event)
    {
        $event->setCancelled(true);
    }
}
