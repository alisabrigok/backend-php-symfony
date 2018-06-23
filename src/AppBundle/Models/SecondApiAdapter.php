<?php

namespace AppBundle\Models;


class SecondApiAdapter implements iCurrencyAdapter
{

    private $currency;

    public function __construct(Currency $currency)
    {
        $this->currency = $currency;
    }

    //this method will pass expected array of objects to the adapted class
    public function getCurrencyArray($arrayData)
    {
        return $this->currency->parseCurrencyArray($arrayData);
    }
}