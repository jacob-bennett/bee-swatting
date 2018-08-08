<?php

session_start();
//$_SESSION = [];

echo "Hello world, kill the bees, man!";


if (array_key_exists('gameActive', $_SESSION) && $_SESSION['gameActive']) {
    hitRandomBee();
} else {
    initGame();
}


function initGame()
{
    $bees = [];

    $beeTypes = [
        BeeFactory::QUEEN,
        BeeFactory::WORKER,
        BeeFactory::WORKER,
        BeeFactory::WORKER,
        BeeFactory::WORKER,
        BeeFactory::WORKER,
        BeeFactory::DRONE,
        BeeFactory::DRONE,
        BeeFactory::DRONE,
        BeeFactory::DRONE,
        BeeFactory::DRONE,
        BeeFactory::DRONE,
        BeeFactory::DRONE,
        BeeFactory::DRONE,
    ];

    foreach ($beeTypes as $type) {
        $bees[] = BeeFactory::make($type);
    }

    $_SESSION['gameActive'] = true;
    $_SESSION['bees'] = $bees;
    renderBees($bees);
}

function hitRandomBee() {
    /** @var Bee[] $bees */
    $bees = $_SESSION['bees'];

    $randomArrayKey = array_rand($bees, 1);
    $targetBee = $bees[$randomArrayKey];


    $targetBee->hit();

    if ($targetBee->isDead()) {
        // Not polymorphic... but, i have an hour.... plus probably overengineering for such a simple thing.
        unset($bees[$randomArrayKey]);


        // Its not possible for all bees to be dead without the queen being killed, so this condition will be reached.
        if ($targetBee->getType() === BeeFactory::QUEEN) {
            renderEndGame();
            $_SESSION['gameActive'] = false;
        }
    }

    $_SESSION['bees'] = $bees;
    renderBees($bees);
}

function renderBees($bees)
{
    echo
    "
<br>
<a href='index.php'>Hit Bee!</a>
<ul>";

    foreach ($bees as $key => $bee) {
        echo "
<li>Bee $key 
    <br>Type: {$bee->getType()}
    <br>HP: {$bee->getHitPoints()}  
    <br>   
    <br>   
</li>
";
    }
    echo "</ul>";


}

function renderEndGame()
{
    echo "
<h1>GAME OVER</h1>
<p>Hit refresh to start again!</p>
";
}




class Bee {
    private $type;
    private $hitpoints;
    private $damagePerHit;

    /**
     * Bee constructor.
     * @param $hitPoints
     * @param $damagePerHit
     */
    public function __construct($type, $hitPoints, $damagePerHit)
    {
        $this->type = $type;
        $this->hitpoints = $hitPoints;
        $this->damagePerHit = $damagePerHit;
    }

    public function getType()
    {
        return $this->type;
    }
    public function getHitPoints()
    {
        return $this->hitpoints;
    }

    public function hit()
    {
        $this->hitpoints = $this->hitpoints - $this->damagePerHit;
    }

    public function isDead()
    {
        return $this->hitpoints < 0;
    }
}


class BeeFactory {
    const QUEEN = 'queen';
    const DRONE = 'drone';
    const WORKER = 'worker';

    public static function make($type) {
        $bee = null;

        switch ($type) {
            case (self::QUEEN):
                $bee = new Bee($type, 100, 8);
                break;
            case (self::DRONE):
                $bee = new Bee($type, 75, 10);
                break;
            case (self::WORKER):
                $bee = new Bee($type, 50, 12);
                break;
        }

        return $bee;
    }
}
