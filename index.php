<?php
header('Content-Type: application/json');
$licensePlate = $_GET['kenteken'];
$requestUrl = "https://api.datamarket.azure.com/Data.ashx/opendata.rdw/VRTG.Open.Data/v1/KENT_VRTG_O_DAT('" . $licensePlate . "')";

if (!$xmldata = @simplexml_load_file($requestUrl)) {
    $data = ['success' => false, 'error' => ['code' => 404, 'message' => 'Kenteken niet gevonden.']];
    header('Content-Type: application/json');
    echo json_encode($data);

    return;
}

$mElements = $xmldata->content->children('m', TRUE);
$mProperties = $mElements->properties;
$dElements = $mProperties->children('d', TRUE);
$jsonArray = array();

foreach ($dElements as $key => $element) {
    $element = (string)$element;
    $regex = '/^[0-9]*\.[0-9]+$/';
    if (ctype_digit($element)) {
        $element = (float)$element;
    } elseif (preg_match($regex, $element)) {
        $element = (float)$element;
    }

    if ($element == "Ja") {
        $element = true;
    } elseif ($element == "Nee") {
        $element = false;
    }
    $element === "" ? $jsonArray[$key] = null : $jsonArray[$key] = $element;
}

$jsonArray['brandDbpedia'] = 'http://dbpedia.org/page/' . ucfirst(strtolower(str_replace(' ', '_', $jsonArray['Merk'])));

$data = ['success' => true, 'resource' => $jsonArray];
echo json_encode($data);
