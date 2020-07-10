<?php

include "dbinit.php";

$return_arr = array();

$ajax_req = isset($_POST["req"]) ? strip_tags($_POST["req"]) : "";

if($ajax_req === "Model") {
    $make = isset($_POST["make"]) ? strip_tags($_POST["make"]) : "";
    if($make !== "") {
        $sth = $pdo->prepare("SELECT DISTINCT model FROM vehicle WHERE make = :make ORDER BY model");
        $sth->bindParam("make", $make, PDO::PARAM_STR);
        $return_arr = $sth->fetchAll(PDO::FETCH_COLUMN);
    }
}

// Encoding array in JSON format
echo json_encode($return_arr);
?>