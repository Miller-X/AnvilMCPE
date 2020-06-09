<?php

namespace anvil;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\Config;


//EVENTS
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\block\Block;
use pocketmine\item\Item;


class Main extends PluginBase implements Listener{


public function onEnable(){
	$this->getLogger()->info("Amboss Plugin geladen");
	$this->getServer()->getPluginManager()->registerEvents($this, $this);

@mkdir($this->getDataFolder());
$this->saveResource("config.yml");
$this->saveDefaultConfig();
}


public function onDisable(){
	$this->getLogger()->info("Amboss Plugin konnte nicht geladen werden");
}


public function onTouch(PlayerInteractEvent $event){

$player = $event->getPlayer();
$block = $event->getBlock();



if($event->getBlock()->getID() === 145){
	if(!$this->getConfig()->get("anvil.click") == "true"){
		$player->sendMessage($this->getConfig()->get("anvil.click.false.message"));
		return true;
}

	$event->setCancelled(true);
	$this->openAnvilForm($player);
}
}


public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args):bool{
	switch($cmd->getName()){

case "anvil":
if(!$sender->hasPermission("anvil.use")){
	$sender->sendMessage($this->getConfig()->get("anvilmenu.noperms.message"));
	return true;
}

if(!$sender instanceof Player){
	$sender->sendMessage("Bitte benutze diesen Befehl In-Game");
	return true;
}

$this->openAnvilForm($sender);
break;
}
return true;
}


//ANVIL MENÜ FORM
public function openAnvilForm($player){
$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
$form = $api->createSimpleForm(function (Player $player, int $data = null){

$result = $data;
if($result === null){
return true;
}
switch($result){


case 0;
if(!$player->hasPermission("anvil.repair.use")){
	$player->sendMessage($this->getConfig()->get("anvilrepair.noperms.message"));
	return true;
}

$item = $player->getInventory()->getItemInHand();
$item->setDamage(0);

$player->getInventory()->setItemInHand($item);

$player->sendMessage($this->getConfig()->get("anvilrepair.succes.message"));
break;


case 1;
if(!$player->hasPermission("anvil.rename.use")){
	$player->sendMessage($this->getConfig()->get("anvilrename.noperms.message"));
	return true;
}

$this->openAnvilRenameForm($player);
break;


case 2;
if(!$player->hasPermission("anvil.sign.use")){
	$player->sendMessage($this->getConfig()->get("anvilsign.noperms.message"));
	return true;
}

$this->openAnvilSignForm($player);
break;


case 3;
$player->sendMessage($this->getConfig()->get("anvil.close.message"));
break;

}
});


$form->setTitle("§l§4--Amboss--");
$form->setContent("§eSelect a function");
$form->addButton("§cRepair");
$form->addButton("§cRename");
$form->addButton("§cSign");
$form->addButton("§1Close");
$form->sendToPlayer($player);
return $form;
}


//ANVIL RENAME FORM
public function openAnvilRenameForm(Player $player){
	$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
	$form = $api->createCustomForm(function(Player $player, array $data = null){
	if($data === null){
		return true;
}

$name = $data[1];


$item = $player->getInventory()->getItemInHand();
$item->setCustomName($name);

$player->getInventory()->setItemInHand($item);


$cfgmessagesucces = $this->getConfig()->get("anvilrename.succes.message");
$message = str_replace("{name}", $name, $cfgmessagesucces);

$player->sendMessage($message);


});


$form->setTitle("§l§4--Amboss--");
$form->addLabel("Write the new name of the Item");
$form->addInput("Item-Name");
$form->sendToPlayer($player);
return $form;
}


//ANVIL Sign FORM
public function openAnvilSignForm(Player $player){
	$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
	$form = $api->createCustomForm(function(Player $player, array $data = null){
	if($data === null){
		return true;
}


$name = $player->getName();
$lore = $data[1];


$item = $player->getInventory()->getItemInHand();
$lore = $data[1];
$name = $player->getName();
$item->setLore(["$lore §r\nSign of $name"]);

$player->getInventory()->setItemInHand($item);

$lore = $data[1];
$cfgmessagesucces = $this->getConfig()->get("anvilsign.succes.message");
$message = str_replace("{lore}", $lore, $cfgmessagesucces);

$player->sendMessage($message);


});


$form->setTitle("§l§4--Amboss--");
$form->addLabel("Write the sign of the Item");
$form->addInput("Item-Sign");
$form->sendToPlayer($player);
return $form;
	}
}