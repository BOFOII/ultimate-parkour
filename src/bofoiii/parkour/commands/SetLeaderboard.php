<?php

declare(strict_types=1);

namespace bofoiii\parkour\commands;

use bofoiii\parkour\Parkour;
use bofoiii\parkour\utils\Utils;
use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class SetLeaderboard extends BaseSubCommand {

    protected function prepare(): void
    {
        $this->setPermission("ultimateparkour.command.set");
    }

    /**
     * @param CommandSender $sender
     * @param string $aliasUsed
     * @param array<string> $args
     */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) {
            return;
        }

        $config = Parkour::getInstance()->getConfig();
        $config->set("leaderboardPos", Utils::posTString($sender->getPosition()));
        $config->save();
        $sender->sendMessage(TextFormat::GREEN . "Leaderboard set successfuly");
        $sender->sendMessage(TextFormat::GREEN . "Rejoin to display the leaderboard");
    }
}