<?php

namespace AppBundle\Service;

use AppBundle\Entity\Currency;
use AppBundle\Models\FirstApiAdapter;
use AppBundle\Models\SecondApiAdapter;
use Doctrine\ORM\EntityManagerInterface;

class ApiOperationService
{
    private $em;

    // constructor
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    function performApiCall($url)
    {
        // make the API calls using cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);
        // in case api call was unsuccessful
        if (!$response) {
            return null;
        }
        // decode the data so it can be processed.
        $data = json_decode($response);

        return $data;
    }

    function resetDb()
    {
        $query = $this->em->createQuery("DELETE FROM AppBundle:Currency");
        $query->execute();
    }

    function compareValues($dataArray)
    {
        // make the first element the starting point
        $comparedResult = $dataArray[0];

        // get the max length between starting point and 2d array
        // so it can be iterated more
        $maxLength = max(sizeof($dataArray), sizeof($dataArray[0]));
        // iterate for first dimension [array0, array1...]
        for ($i = 0; $i < $maxLength; $i++) {
        // iterate within second dimension [element0OfArray0, element1ofArray0...]
            for ($j = 0; $j < $maxLength; $j++) {
                //check if the starting point is bigger, if so remove it, add compared value
                if (array_key_exists($j, $dataArray) && array_key_exists($i, $dataArray[0]) && $comparedResult[$i] > $dataArray[$j][$i]) {
                    array_splice($comparedResult, $i, 1, $dataArray[$j][$i]);
                }
            }
        }

        return $comparedResult;
    }

    function saveToDB($values)
    {
        $currencyNames = ["USD", "EUR", "GBP"];
        // batchSize is used for big bulk database operations
        $batchSize = 3;
        for ($i = 0; $i < 3; $i++) {

            $currency = new Currency();
            // insert currency name
            $currency->setName($currencyNames[$i]);
            // insert currency value
            $currency->setValue($values[$i]);
            $this->em->persist($currency);

            if (($i % $batchSize) === 0) {
                $this->em->flush();
                // Detach all objects from Doctrine
                $this->em->clear();
            }
        }
        //Persist objects that did not make up an entire batch
        $this->em->flush();
        $this->em->clear();
    }

    public function performOperation()
    {
        //this array will be an array of currencies from each apis
        $arrayOfConvertedData = [];
        // get the first api's response
        $firstApiData = $this->performApiCall("http://www.mocky.io/v2/5a74519d2d0000430bfe0fa0");
        // initiate an instance of Currency class to be used for getting the currency values
        $FirstApiAdapter = new FirstApiAdapter(new \AppBundle\Models\Currency());

        $secondApiData = $this->performApiCall("http://www.mocky.io/v2/5a74524e2d0000430bfe0fa3");
        $SecondApiAdapter = new SecondApiAdapter(new \AppBundle\Models\Currency());
        // add each currency array that's obtained from our adapters to an array so each can be compared
        array_push($arrayOfConvertedData, $SecondApiAdapter->getCurrencyArray($secondApiData), $FirstApiAdapter->getCurrencyArray($firstApiData));
        // get the minimum values from the comparison
        $comparedData = $this->compareValues($arrayOfConvertedData);

        // clear the database
        $this->resetDb();
        // register the compared values to database
        $this->saveToDB($comparedData);

    }
}
