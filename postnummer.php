<?php

// Eksempel på postnummer-lookup iht. https://v5.dk/xcode-swift-programmering/nsdata-json-nsdictionary-valueforkey-swift2-xcode-ios.html

header('Content-Type: application/json');

if(!isset($_GET['postnummer']) || empty($_GET['postnummer'])) {

    $returnArr = array('error' => 'Du skal angive et gyldigt postnummer');
    echo json_encode($returnArr);
    die;

} else {

    $Postnr = intval($_GET['postnummer']);

}

include("app.php");
$app = new minAwesomeApp;

if(!$Postdata = $app->DatabasePrepareQueryReturnFirstField("SELECT * FROM Data_Postnumre WHERE Postnr = ?", array($Postnr))) {

    $returnArr = array('postnummer' => $Postnr, 'bynavn' => 'Ukendt postnummer');
    echo json_encode($returnArr);

} else {

    $returnArr = array(
        'postnummer' => $Postnr,
        'bynavn' => $Postdata['Bynavn'],
        'kommune' => $Postdata['Kommunenavn'],
        'region' => $Postdata['Regionsnavn']
    );

    echo json_encode($returnArr);


}
