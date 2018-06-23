<?php

namespace AppBundle\Models;


class FirstApiAdapter implements iCurrencyAdapter
{

    private $currency;

    public function __construct(Currency $currency)
    {
        $this->currency = $currency;
    }

    //this method will pass expected array of objects to the adapted class
    public function getCurrencyArray($objectData)
    {
        return $this->currency->parseCurrencyArray($objectData->result);
    }
}