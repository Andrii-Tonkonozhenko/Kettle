<?php

class KettleException extends Exception
{

}

class NotEnoughEmptyCapacityException extends KettleException
{
    protected $message = 'Not enough space for capacity';
}

class KettleDoesNotContainThisAmountOfLiquidException extends KettleException
{
    protected $message = 'The kettle does not contain this amount of liquid';
}

class KettleIsOfflineException extends KettleException
{
    protected $message = 'The Kettle is offline';
}

class ElectricityModel
{
    public $isElectricityOn;
    public $kW;
    public $kilowattPerHour;
    public $price1kW;

    public function __construct(float $price1kW, float $kW, bool $isElectricityOn)
    {
        $this->price1kW = $price1kW;
        $this->kW = $kW;
        $this->kilowattPerHour = 0;
        $this->isElectricityOn = $isElectricityOn;
    }

    public function getIsElectricityOn() : bool
    {
        return $this->isElectricityOn;
    }

    public function getKw() : float
    {
        return $this->kW;
    }

    public function setKilowattPerHour($kilowattPerHour) : void
    {
        $this->kilowattPerHour += $kilowattPerHour;
    }

    public function getKilowattPerHour() : float
    {
        return $this->kilowattPerHour;
    }

    public function getPrice1kW() : float
    {
        return $this->price1kW;
    }
}

class Kettle
{
    private $totalLitersCapacity;
    private $litersFilledOut = 0;
    private $secondsToBoil;
    private $electricity;

    public function __construct(
        float $totalLitersCapacity,
        float $secondsToBoil,
        ElectricityModel $electricity
    ) {
        $this->totalLitersCapacity = $totalLitersCapacity;
        $this->secondsToBoil = $secondsToBoil;
        $this->electricity = $electricity;
    }

    private function calculateTotalKilowattPerHour(): void
    {
        $boilingTime = ($this->secondsToBoil * $this->litersFilledOut) * 2;
        $this->electricity->setKilowattPerHour($this->electricity->getKw() * ($boilingTime / 3600));
    }

    public function addLiquid(float $litersFilledOut): void
    {
        if (!$this->checkIfLiquidFits($litersFilledOut)) {
            throw new NotEnoughEmptyCapacityException();
        }

        $this->litersFilledOut += $litersFilledOut;
    }

    public function checkIfLiquidFits(float $liters): bool
    {
        return $liters + $this->litersFilledOut <= $this->totalLitersCapacity;
    }

    public function containsLitersOfLiquid(float $liters): bool
    {
        return $this->litersFilledOut - $liters >= 0;
    }


    public function drainBoiledLiquid(float $liters): void
    {
        if (!$this->containsLitersOfLiquid($liters)) {
            throw new KettleDoesNotContainThisAmountOfLiquidException();
        }

        $this->litersFilledOut -= $liters;
    }

    public function getKilowattCost(): float
    {
        $money = $this->electricity->getKilowattPerHour() * $this->electricity->getPrice1kW();

        return ceil($money * 100) / 100;
    }

    public function startBoilProcess(): string
    {
        if (!$this->electricity->getIsElectricityOn()) {
            throw new KettleIsOfflineException();
        }

        $this->calculateTotalKilowattPerHour();

        return 'The kettle boiled';
    }
}

$electricity = new ElectricityModel(1.4, 2, true);
$kettle = new Kettle(6,100, $electricity);

try {
    $kettle->addLiquid(2);
    $kettle->addLiquid(2);
    $kettle->addLiquid(2);

    echo $kettle->startBoilProcess() . "</br>";

    $kettle->drainBoiledLiquid(5);
    $kettle->addLiquid(2);
    $kettle->addLiquid(1);
    $kettle->addLiquid(1.5);

    echo $kettle->startBoilProcess() . "</br>";

    $kettle->drainBoiledLiquid(3);
    echo $kettle->getKilowattCost() . 'UAH spent' . "</br>";
} catch (KettleException $e) {
    die($e->getMessage());
}
