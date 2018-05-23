<?php
  $current_page='rubriek';
  require_once ('templates/header.php');
  $rubrieknaam = "Rubrieken";
  $statement = $dbh->prepare("SELECT * FROM Rubriek WHERE rubrieknummer=?");
  $statement->execute(array($_GET['rubrieknummer']));
  while($row = $statement->fetch()){
    $rubrieknaam = $row['rubrieknaam'];
  }
  displayAuctionpage();
?>
<div class="view index-header">
  <img src="img/rubriek/car-boats-motorcycles.png" class="" height="350">
  <div class="mask index-banner rgba-niagara-strong">
    <h1 class="white-text banner-text"><?=$rubrieknaam?></h1>
  </div>
</div>
<div class="flex-parent">
  <div class="category-sidebar col-sm-12 col-md-3 flex-1">
    <div class="flypanels-container">
  		<?php include ('templates/rubriek/sidebar-menu.php'); ?>
  	</div>
  </div>

<div class="container-fluid category-page flex-1" id="wrapper">
  <div class="col-sm-12 col-md-9 category-content">
    <div class="row">
      
      <?=$auctionpage?>
      
    </div>
  </div>
</div>
</div>



<?php include('templates/footer.php') ?>