<?php
$current_page = "index";
require_once('templates/header.php');
if(isset($_GET['key'])){
$voorwerpnummer = $_GET['key'];
try{
$statement = $dbh->prepare("SELECT * FROM Voorwerp WHERE ?");
$data = $statement->execute(array($voorwerpnummer));
$data->FETCH_ASSOC();
if($data['voorwerpnummer']==$voorwerpnummer){
  echo "werkt";
}else{
  echo "werkt niet";
}
}catch(PDOException $e){
  $error = $e;
}
}else{
  header("Location: index.php");
}
$current_page='auction';
require_once('templates/header.php');
?>

<?php

if(empty($_GET['voorwerpnummer'])){
  header("Location: index.php");
}else{
  $auctionInfo = "";
  $voorwerpnummer = $_GET['voorwerpnummer'];
  $data = $dbh->prepare("SELECT * FROM Voorwerp WHERE voorwerpnummer = ?");
  $data->execute(array($voorwerpnummer));
  while($row = $data->fetch()){
    $auctionInfo.="Titel: ".$row['titel'];
    $auctionInfo.="<br>";
    $auctionInfo.="Beschrijving: ".$row['beschrijving'];
  }
}
include "timer.php";
?>

<?php
echo $auctionInfo;
?>




<?php
include("templates/footer.php");
?>
