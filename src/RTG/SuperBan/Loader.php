<?php

/* 
 * Copyright (C) 2017 RTG
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace RTG\SuperBan;

/* Essentials */
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\Server;
use pocketmine\Player;
use pocketmine\utils\Config;

class Loader extends PluginBase implements Listener {
    
    public $config;
    public $version = 1.0;
    
    public function onEnable() {
        
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        @mkdir($this->getDataFolder());
        $this->config = new Config($this->getDataFolder() . 'config.yml', Config::YAML, array(
            'version' => 1.0,
            'bans' => array()
        ));
        $this->config->save();
        
    }
    
    public function onCommand(\pocketmine\command\CommandSender $sender, \pocketmine\command\Command $command, $label, array $args) {
        
        switch(strtolower($command->getName())) {
            
            case 'sp':
                
                if($sender->isOp() or $sender->hasPermission('superban.command')) {
                    
                    if(isset($args[0])) {
                        
                        switch(strtolower($args[0])) {
                            
                            case 'ban':
                                
                                if(isset($args[1])) {
                                    
                                    $n = $args[1];
                                    $pl = $this->getServer()->getPlayer($n);
                                    
                                    if ($pl === null) {
                                        
                                        $sender->sendMessage('isnt a player!');
                                        
                                    }
                                    else {
                                        
                                        $name = $args[1];
                                    
                                        $this->onBan($sender, $name);
                                        
                                    }
                                       
                                }
                                else {
                                    $sender->sendMessage('/sp ban [name]');
                                }
                                
                                break;
                            
                            case 'list':
                                
                                $this->onGet($sender);
                                
                                break;
                                
                            case 'rm':
                                
                                if(isset($args[1])) {
                                    
                                    $name = $args[1];
                                    
                                    $this->onRemove($sender, $name);
                                    
                                }
                                else {
                                    $sender->sendMessage('/sp rm [name]');
                                }
                                
                                break;
                            
                        }
                        
                    }
                    else {
                        $this->onHelp($sender);
                    }
                    
                    
                }
            
            
        }
          
    }
    
    public function onSave() {
        return $this->config->save();
    }
    
    public function onHelp($sender) {
        $sender->sendMessage('/sp < ban | list | rm |');
    }
    
    public function onBan($sender, $name) {
        
        $pl = $this->getServer()->getPlayer($name);
        
        $uid = $pl->getUniqueId();
        
        $line = "$name|$uid";
        
        $cfg = $this->config->get('bans');
        
        array_push($cfg, $line);
        $this->config->set('bans', $cfg);
        
        $this->onSave();
        
        $sender->sendMessage('Done!');
        
    }
    
    public function onGet($sender) {
        
        if(!empty($this->config->get('bannedplayers'))) {
            
            foreach($this->config->get('bannedplayers') as $p) {
                
                $x = $this->config->get('bannedplayers');
                $im = implode(', ', $x);
                $sender->sendMessage(" -- Banned CID(s) -- ");
                $sender->sendMessage($im);
                
            }
            
        }
        else {
            $sender->sendMessage('Your banned list is empty!');
        }
        
    }
    
    public function onRemove($sender, $name) {
        
        $cfg = $this->config->get('bans');
        
        if(($key = array_search($name, $cfg) != false)) {
            
            unset($cfg[$key]);
            $this->config->set('bans', $cfg);
            
            $this->onSave();
            $sender->sendMessage('Done!');
            
        }
        
    }
    
}