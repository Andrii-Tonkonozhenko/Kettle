<?php

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