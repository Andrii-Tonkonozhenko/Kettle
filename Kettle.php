<?php

require 'ElectricityModel.php';
require 'KettleException.php';

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
