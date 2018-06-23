<?php

namespace AppBundle\Models;


interface iCurrencyAdapter
{
    public function getCurrencyArray($data);
}