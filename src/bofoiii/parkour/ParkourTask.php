<?php

declare(strict_types=1);

namespace bofoiii\parkour;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;

class ParkourTask extends Task
{

    public function onRun(): void
    {
        $sessions = Parkour::getInstance()->getManager()->getSessions();
        foreach($sessions as $session) {
            if ($session->getPlayer()->isConnected() && $session->getPlayer()->isOnline()) {
                $session->incrementTimer();
                $session->getPlayer()->sendTip(TextFormat::GREEN . "Timer: " . $session->getTimer() / 20);
            }
        }
    }
}
