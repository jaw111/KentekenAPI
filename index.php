<?php

$requestHeaders = apache_request_headers();;
if ((isset($requestHeaders['Accept']) && $requestHeaders['Accept'] == 'application/ld+json') || isset($_GET['json-ld'])) {
    $contentType = 'application/ld+json';
} else {
    $contentType = 'application/json';
}

header('Content-Type: ' . $contentType);
$licensePlate = $_GET['kenteken'];
$requestUrl = "https://api.datamarket.azure.com/Data.ashx/opendata.rdw/VRTG.Open.Data/v1/KENT_VRTG_O_DAT('" . $licensePlate . "')";

if (!$xmldata = @simplexml_load_file($requestUrl)) {
    $data = ['error' => ['code' => 404, 'message' => 'Kenteken niet gevonden.']];
    header('Content-Type: application/json');
    echo json_encode($data);

    return;
}

$mElements = $xmldata->content->children('m', TRUE);
$mProperties = $mElements->properties;
$dElements = $mProperties->children('d', TRUE);
$jsonArray = array();

if ($contentType == 'application/ld+json') {
    $context = 'https://w3id.org/rdw/contexts/vehicles';
}

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
    } elseif ($element == "N.v.t." || $element == "Niet geregistreerd" || $element == "") {
        $element = null;
    }

    if ($contentType == 'application/ld+json') {

        $colors = [
            'BEIGE' => 'Beige',
            'BLAUW' => 'Blue',
            'BRUIN' => 'Brown',
            'CREME' => 'Crème',
            'DIVERSEN' => 'Diverse',
            'GEEL' => 'Yellow',
            'GRIJS' => 'Gray',
            'GROEN' => 'Green',
            'ORANJE' => 'Orange',
            'PAARS' => 'Purple',
            'ROOD' => 'Red',
            'ROSE' => 'Pink',
            'WIT' => 'White',
            'ZWART' => 'Black',

        ];

        $fuelTypes = [
            'Alcohol' => 'Ethanol',
            'Benzine' => 'Benzine',
            'CNG (Compressed Natural Gas)' => 'CNG',
            'Diesel' => 'Diesel',
            'Elektriciteit' => 'Electricity',
            'Liquified Natural Gas (Cryogeen)' => 'Cryogenic',
            'LPG (Liquified Petrol Gas)' => 'LPG',
            'Waterstof' => 'Hydrogen'
        ];

        $vehicleTypes = [
            'Aanhangwagen' => 'Trailer',
            'Autonome aanhangwagen' => '@TODO',
            'Bedrijfsauto' => 'CommercialVehicle',
            'Bromfiets' => 'Moped',
            'Bus' => 'Bus',
            'Driewielig motorrijtuig' => 'ThreeWheeledVehicle',
            'Middenasaanhangwagen' => '@TODO',
            'Motorfiets' => 'Motorcycle',
            'Motorfiets met zijspan',
            'Oplegger' => '@TODO',
            'Personenauto' => 'PassengerVehicle'
        ];

        $inrichtingTypes = [
            'veewagen',
            'caravan',
            'open wagen',
            'voor vervoer boten',
            'open met kraan',
            'afzetbak',
            'brandweerwagen',
            'resteelwagen',
            'achterwaartse kipper',
            'voor vervoer voertuigen',
            'sedan',
            'gesloten opbouw',
            'kipper',
            'detailhandel/expositiedoel.',
            'opleggertrekker',
            'kampeerwagen',
            'vrieswagen',
            'cabriolet',
            'servicewagen',
            'stationwagen',
            'kaal chassis',
            'compressor',
            'afneembare bovenbouw',
            'coupe',
            'geconditioneerd voertuig',
            'betonmixer',
            'vuilniswagen',
            'bus',
            'kolkenzuiger',
            'open laadvloer',
            'gecond. met temperatuurreg.',
            'straatvgr,reiniger,rioolzgr',
            'huifopbouw',
            'chassis cabine',
            'voertuig met haakarm',
            'gecond. zndr temperatuurreg.',
            'hoogwerker',
            'containercarrier',
            'neerklapbare zijschotten',
            'tankwagen',
            'hatchback',
            'MPV',
            'terreinvoertuig',
            'voor rolstoelen toegankelijk voertuig',
            'ambulance',
            'Limousine',
            'lijkwagen',
            'ladderwagen',
            'speciale groep',
            'keetwagen',
            'koelwagen',
            'open wagen met vast dak',
            'pick-up truck',
            'betonpomp',
            'reparatiewagen',
            'tank v.v. gevaarl. Stoffen',
            'niet nader aangeduid',
            'takelwagen',
            'limousine',
            'voor vervoer boomstammen',
            'tweezijdige kipper',
            'driewielig motorrijtuig (L7e)',
            'montagewagen',
            'v.vervoer zweefvliegtuigen',
            'kantoorwagen',
            'driezijdige kipper',
            'gepantserd voertuig',
            'taxi',
            'voor vervoer wissellaadbakken',
            'mobiele kraan',
            'driewielig motorrijtuig (L5e)',
            'dolly',
            'demonstratiewagen',
            'kraanwagen',
            'sproeiwagen',
            'bergingsvoertuig',
            'medische hulpwagen',
            'dieplader',
            'destructorwagen',
            'asfaltkipper',
            'aanhangwagentrekker',
            'woonwagen',
            'truckstationwagen',
            'meetwagen',
            'mobiele zender',
            'aanhangw. Met stijve dissel',
            'gedeeltelijk open wagen',
            'boorwagen',
            'verhuiswagen',
            'geluidswagen',
            'straatveegwagen',
            'voor vervoer personen',
            'faecalienwagen'
        ];
        
        $zuinigheidsLabels = [
            'A' => 'EfficiencyLabelA',
            'B' => 'EfficiencyLabelB',
            'C' => 'EfficiencyLabelC',
            'D' => 'EfficiencyLabelD',
            'E' => 'EfficiencyLabelE',
            'F' => 'EfficiencyLabelF',
            'G' => 'EfficiencyLabelG'
        ];

        $id = null;
        switch ($key) {
            case 'Eerstekleur':
            case 'Tweedekleur':
                if (!isset($colors[$element])) {
                    continue;
                }
                $id = 'rdwv:' . $colors[$element];
                $label = ucfirst(strtolower($element));
                break;
            case 'Handelsbenaming':

                if (!$type = strstr($element, ';', true)) {
                    $type = $element;
                }

                if ($car = lookup((string) $dElements->Merk . ' ' . $type)) {
                    $id = $car['uri'];
                    $label = $car['label'];
                } else {
                    continue;
                }
                break;
            case 'Hoofdbrandstof':
            case 'Nevenbrandstof':
                if (!isset($fuelTypes[$element])) {
                    continue;
                }
                $id = 'rdwv:' . $fuelTypes[$element];
                $label = $element;
                break;
            case 'Milieuclassificatie':
                if ($element == '') {
                    continue;
                }
                $id = 'rdwv:' . str_replace(' ', '', $element);
                $label = $element;
                break;
            case 'Merk':
                if ($brand = lookup((string) $dElements->Merk)) {
                    $id = $brand['uri'];
                    $label = $brand['label'];
                } else {
                    continue;
                }
                break;
            case 'Voertuigsoort':
                if (!isset($vehicleTypes[$element])) {
                    continue;
                }
                $id = 'rdwv:' . $vehicleTypes[$element];
                $label = $element;
                break;
            case 'Zuinigheidslabel':
                if (!isset($zuinigheidsLabels[$element])) {
                    continue;
                }
                $id = 'rdwv:' . $zuinigheidsLabels[$element];
                $label = $element;
                break;
        }

        if (isset($id)) {
            $element = ['@id' => $id, 'label' => $label];
        }
    }

    $element === "" ? $jsonArray[$key] = null : $jsonArray[$key] = $element;
}

if (isset($context)) {
    $data = ['@context' => $context, '@id' => '', 'resource' => ['@id' => '#this'] + $jsonArray];
} else {
    $data = ['resource' => $jsonArray];
}
echo json_encode($data);

function lookup($q)
{
    if ($results = simplexml_load_file('http://lookup.dbpedia.org/api/search/KeywordSearch?QueryString=' . urlencode($q))) {
        if ($results->Result) {
            return [
                'uri' => (string) $results->Result->URI,
                'label' => (string) $results->Result->Label,
            ];
        }
    }

    return false;
}
