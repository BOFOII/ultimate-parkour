<?php

declare(strict_types=1);

namespace bofoiii\parkour\manager;

use bofoiii\parkour\CheckPointFloating;
use bofoiii\parkour\utils\Utils;
use Exception;
use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;
use pocketmine\world\World;

class ParkourData
{

    private string $displayName = "";
    private string $world = "";

    private Position $hub;

    private int $cpSlot = 0;

    /** @var Vector3[] $checkPoints */
    private array $checkPoints = [];
    private float|int $bottom = 0;

    private bool $enable = false;

    /**
     * @param null|array{
     * displayName: string,
     * world: string,
     * hub: null|string,
     * bottom: float|int,
     * checkPointSlots: int,
     * checkPoints: array<int, string>
     * } $parkorData
     */
    public function __construct(?array $parkorData)
    {
        if ($parkorData !== null) {
            $this->load($parkorData);
        }
    }

    public function setDisplayName(string $name): void
    {
        $this->displayName = $name;
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function setWorld(World $world): void
    {
        $this->world = $world->getFolderName();
    }

    public function getWorld(): string
    {
        return $this->world;
    }

    public function setHub(Position $pos): void
    {
        $this->hub = $pos;
    } 

    public function getHub(): Position
    {
        return $this->hub;
    }

    public function setCheckPointSlot(int $slot): void
    {
        $this->cpSlot = $slot;
    }

    public function getCheckPointSlots(): int
    {
        return $this->cpSlot;
    }

    public function setCheckPoint(Vector3 $pos, int $slot): void
    {
        $this->checkPoints[$slot] = $pos;
    }

    /**
     * @return Vector3[]
     */
    public function getCheckPoints(): array
    {
        return $this->checkPoints;
    }

    public function getCheckPoint(int $slot): ?Vector3
    {
        return isset($this->checkPoints[$slot]) ? $this->checkPoints[$slot] : null;
    }

    public function setBottom(Vector3 $pos): void
    {
        $this->bottom = $pos->getY();
    }

    public function getBottom(): float|int
    {
        return $this->bottom;
    }

    public function isEnable(): bool
    {
        return $this->enable;
    }

    /**
     * @param array{
     * displayName: string,
     * world: string,
     * hub: null|string,
     * bottom: float|int,
     * checkPointSlots: int,
     * checkPoints: array<int, string>
     * } $parkorData
     */
    public function load(array $parkorData): void
    {
        $this->displayName = $parkorData["displayName"];
        if($parkorData["hub"] !== null) {
            $this->hub =  Utils::strTPos(":", $parkorData["hub"]);
        }
        $this->bottom = $parkorData["bottom"];
        $this->world = $parkorData["world"];
        $this->cpSlot = $parkorData["checkPointSlots"];
        $this->checkPoints = Utils::strArrayTVec3($parkorData["checkPoints"]);
    }

    public function enable(): void
    {
        if ($this->displayName == "") {
            throw new Exception("Displayname cannot be empty");
        }

        if ($this->world == "") {
            throw new Exception("World cannot be empty");
        }
        
        if (!Server::getInstance()->getWorldManager()->isWorldGenerated($this->world)) {
            throw new Exception("World with name {$this->world} not generate");
        }
        
        if (!Server::getInstance()->getWorldManager()->isWorldLoaded($this->world)) {
            Server::getInstance()->getWorldManager()->loadWorld($this->world);
        }

        if (!isset($this->hub) || !$this->hub instanceof Position) {
            throw new Exception("Hub cannot be empty");
        }

        if ($this->cpSlot < 2) {
            throw new Exception("Check point can't more then 2");
        }

        if (count($this->checkPoints) !== $this->cpSlot) {
            throw new Exception("Checkpoint count must be " . $this->cpSlot);
        }

        $this->enable = true;
    }

    public function disable(): void
    {
        $this->enable = false;
    }

    /**
     * @return array{
     * displayName: string,
     * world: string,
     * hub: null|string,
     * bottom: float|int,
     * checkPointSlots: int,
     * checkPoints: array<int, string>
     * }
     */
    public function getSaveData(): array
    {
        return [
            "displayName" => $this->displayName,
            "world" => $this->world,
            "hub" => isset($this->hub) ? Utils::posTString($this->hub) : null,
            "bottom" => $this->bottom,
            "checkPointSlots" => $this->cpSlot,
            "checkPoints" => empty($this->checkPoints) ? [] : Utils::vec3ArrayTStr($this->checkPoints),
        ];
    }
}
