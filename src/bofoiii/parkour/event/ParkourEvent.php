<?php

declare(strict_types=1);

namespace bofoiii\parkour\event;

use bofoiii\parkour\manager\ParkourData;
use pocketmine\event\player\PlayerEvent;
use pocketmine\player\Player;

abstract class ParkourEvent extends PlayerEvent {

    public function __construct(Player $player, private ParkourData $parkourData)
    {
        $this->player = $player;
    }

    public function getParkourData(): ParkourData
    {
        return $this->parkourData;
    }
}