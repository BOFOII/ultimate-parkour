<?php

declare(strict_types=1);

namespace bofoiii\parkour\commands\setup;

use bofoiii\parkour\manager\ParkourManager;
use bofoiii\parkour\Parkour;
use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class Save extends BaseSubCommand
{

    protected function prepare(): void
    {
        $this->registerArgument(0, new RawStringArgument("name"));
        $this->setPermission("ultimateparkour.command.set");
    }

    /**
     * @param CommandSender $sender
     * @param string $aliasUsed
     * @param array{
     * name: string
     * } $args
     */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) {
            return;
        }

        $parkourData = Parkour::getInstance()->getManager()->getData($args["name"]);

        if ($parkourData == null) {
            $sender->sendMessage(TextFormat::RED . "Parkour with arena name " . $args["name"] . " not exist");
            return;
        }

        $config = new Config(
            Parkour::getInstance()->getDataFolder() . ParkourManager::PARKOUR_DIR . DIRECTORY_SEPARATOR . $parkourData->getDisplayName() . ".yml",
            Config::YAML
        );
        $config->setAll($parkourData->getSaveData());
        $config->save();
        $sender->sendMessage(TextFormat::GREEN . "Parkour save successfuly");
    }
}
