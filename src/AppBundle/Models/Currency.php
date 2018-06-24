<?php


namespace AppBundle\Models;


class Currency
{
    public function parseCurrencyArray($currencyArrayOfObjs)
    {
        $i = 0;
        $currency = [];

        // iterate the data array
        foreach ($currencyArrayOfObjs as $currencyObjElement) {
            // convert the object to array
            $flattenCurrencies = get_object_vars($currencyObjElement);
            foreach ($flattenCurrencies as $flattenElement) {
                // skip every even property, because values are in an array and 1st element (index 0) is always currency name
                if ($i % 2 !== 0) {
                    // convert the value to float
                    $floatElement = floatval($flattenElement);
                    array_push($currency, $floatElement);
                }
                $i++;
            }
        }

        // return currency array
        return $currency;
    }
}