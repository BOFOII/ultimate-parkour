<?php

declare(strict_types=1);

namespace bofoiii\parkour\event;

use bofoiii\parkour\manager\ParkourData;
use pocketmine\player\Player;

class PlayerFinishEvent extends ParkourEvent {

    public function __construct(Player $player, ParkourData $parkourData, private float|int $finishTime)
    {
        parent::__construct($player, $parkourData);
    }

    public function getFinishTime(): float|int
    {
        return $this->finishTime;
    }

}