<?php

declare(strict_types=1);

namespace bofoiii\parkour\utils;

use Exception;
use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\world\Position;

class Utils
{

    public static function vec3TString(Vector3 $vector): string
    {
        return $vector->getX() . ":" . $vector->getY() . ":" . $vector->getZ();
    }

    public static function strTVec3(string $delimeter, string $string): Vector3
    {
        if(empty($delimeter)) {
            throw new Exception("Delimeter cannot be empty");
        }

        $split = explode($delimeter, $string);
        return new Vector3(floatval($split[0]), floatval($split[1]), floatval($split[2]));
    }

    public static function posTString(Position $pos): string
    {
        return $pos->getX() . ":" . $pos->getY() . ":" . $pos->getZ() . ":" . $pos->getWorld()->getFolderName();
    }


    public static function strTPos(string $delimeter, string $string): Position
    {
        if(empty($delimeter)) {
            throw new Exception("Delimeter cannot be empty");
        }
        
        $split = explode($delimeter, $string);
        if (!isset($split[3])) {
            throw new Exception("Invalid format string position");
        }

        if (Server::getInstance()->getWorldManager()->isWorldGenerated($split[3])) {
            Server::getInstance()->getWorldManager()->loadWorld($split[3]);
        }

        $world = Server::getInstance()->getWorldManager()->getWorldByName($split[3]);

        return new Position(floatval($split[0]), floatval($split[1]), floatval($split[2]), $world);
    }

    /**
     * @param array<string> $strings
     * @return Vector3[]
     */
    public static function strArrayTVec3(array $strings): array
    {
        return array_map(function (string $str): Vector3 {
            return self::strTVec3(":", $str);
        }, $strings);
    }

    /**
     * @param Vector3[] $vectors
     * @return array<string>
     */
    public static function vec3ArrayTStr(array $vectors): array
    {
        return array_map(function (Vector3 $vec): string {
            return self::vec3TString($vec);
        }, $vectors);
    }
}
