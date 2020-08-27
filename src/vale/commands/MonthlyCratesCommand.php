<?php

namespace vale\commands;

use vale\MonthlyCrates;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\plugin\Plugin;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\Durable;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\CompoundTag;


class MonthlyCratesCommand extends PluginCommand{

    /** @var array */
    public $plugin;

    public function __construct(MonthlyCrates $plugin) {
        $key = MonthlyCrates::getInstance()->getConfig()->get("CommandName");
        parent::__construct($key, $plugin);
        $this->setDescription("Give players mc");
        $this->setPermission("core.command.admin");
        $this->plugin = $plugin;
    }

    /**
     * @param CommandSender $sender
     * @param string $alias
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
        if (!$sender->hasPermission("core.command.admin")) {
            $sender->sendMessage(TextFormat::RED . "You do not have permission to use this command");
            return false;
        }
        if (count($args) < 1) {
            $key = MonthlyCrates::getInstance()->getConfig()->get("CommandName");
            $sender->sendMessage(TextFormat::GOLD . "Usage:" . TextFormat::GREEN . " /$key <player>");
            return false;
        }
        if (!empty($args[0])) {
            $target = $this->getPlayer($args[0]);
            if ($target == null) {
                $sender->sendMessage(TextFormat::RED . "That player cannot be found");
                return false;
            }
            if ($target == true) {
                $unb = Enchantment::getEnchantment(Enchantment::UNBREAKING);
                $prot = Enchantment::getEnchantment(Enchantment::PROTECTION);
                $leg = Item::get(130, 0, 1);
                $leg->addEnchantment(new EnchantmentInstance($unb,7));
                $leg->addEnchantment(new EnchantmentInstance($prot,5));
                $name = MonthlyCrates::getInstance()->getConfig()->get("MonthlyCrateName");
                $leg->setCustomName($name);
                $leg->setLore([
                    
                    
                    $name,
    
        'Â§rÂ§7Tap to recieve random rewards',
        'Â§rÂ§b* Elite',
        'Â§rÂ§7* Common', 
        'Â§rÂ§e* Ultimate',
        'Â§rÂ§c*  Mythic',
        'Â§rÂ§6* Legendary'
            
            ]);

                $target->getInventory()->addItem($leg);



            }
        }

        return  true;
    }

    /**
     * @param string $player
     * @return null|Player
     */
    public function getPlayer($player): ?Player{
        if (!Player::isValidUserName($player)) {
            return null;
        }
        $player = strtolower($player);
        $found = null;
        foreach($this->plugin->getServer()->getOnlinePlayers() as $target) {
            if (strtolower(TextFormat::clean($target->getDisplayName(), true)) === $player || strtolower($target->getName()) === $player) {
                $found = $target;
                break;
            }
        }
        if (!$found) {
            $found = ($f = $this->plugin->getServer()->getPlayer($player)) === null ? null : $f;
        }
        if (!$found) {
            $delta = PHP_INT_MAX;
            foreach($this->plugin->getServer()->getOnlinePlayers() as $target) {
                if (stripos(($name = TextFormat::clean($target->getDisplayName(), true)), $player) === 0) {
                    $curDelta = strlen($name) - strlen($player);
                    if ($curDelta < $delta) {
                        $found = $target;
                        $delta = $curDelta;
                    }
                    if ($curDelta === 0) {
                        break;
                    }
                }
            }
        }
        return $found;
    }
}
