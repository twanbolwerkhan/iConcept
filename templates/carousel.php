<?php require_once("functions.php");
global $dbh;
try{
$statement = $dbh->query("SELECT DISTINCT dateadd(day, looptijd, looptijdbegindag) as looptijdeindedag2,* FROM Voorwerp vw LEFT JOIN Bestand b ON vw.voorwerpnummer=b.voorwerpnummer LEFT JOIN Bod bd ON vw.voorwerpnummer=bd.voorwerpnummer");

$carousel= array();
$i=0;
while($row = $statement->fetch()){
	$voorwerpnummer = $row[1];
	$i--;
	$timer="timer".$i;
	$looptijd = $row['looptijd'];
	$looptijdbegindag =strtotime($row['looptijdbegindag']);
	$looptijdbegintijdstip = strtotime($row['looptijdtijdstip']);
	$data = $dbh->query("SELECT * FROM Rubriek");
if(isset($row['bodbedrag']) && $row['startprijs']<$row['bodbedrag']){
	  $huidige_bod=$row['bodbedrag'];
	}else{
	  $huidige_bod=$row['startprijs'];
	}

		 $time = date_create($row['looptijdeindedag2'] . $row['looptijdtijdstip']);
		 $closingtime = date_format($time, "d M Y H:i"); //for example 14 Jul 2020 14:35


		 $countdown = $closingtime;
	$out = '';

$carousel[]=	'			<div class="col-md-3">
					<div class="card auction-card mb-4">
						<div class="view overlay">
						<a href="detailpage.php?id='.$voorwerpnummer.'"><div class="mask flex-center rgba-white-slight waves-effect waves-light"></div>
							<img class="card-img-top" src="'.$row["filenaam"].'" alt="'.$row["titel"].'" />
						</a>
						</div>
						<div class="card-body">
							<span class="small-font">'.$voorwerpnummer.'</span>
							<h4 class="card-title">'.$row["titel"].'</h4>
							<hr>
							<div class="card-text">
								<p>
									'.$row["beschrijving"].'
								</p>
							</div>
							<hr />
							<ul class="list-unstyled list-inline d-flex" style="text-align:center">
								<li class="list-inline-item pr-2 flex-1 ml-5"><i class="fa fa-lg fa-gavel pr-2"></i>&euro;'.$huidige_bod.'</li>
								<div class="card-line"></div>
								<li class="list-inline-item pr-2 flex-1 mr-5"><i class=""></i><div id='.$timer.'></div></li>
							</ul>
						</div>
					</div>
				</div><script>
						 countdown("'.$timer.'","'.$countdown.'");
						 </script>';
}
for($i = 0; $i<count($carousel);$i++){
if($i>11){
	break;
}
	if($i==0){
$out.='<div class="carousel-item active">';

}else
	if($i==4){
$out.='</div>';
$out.='<div class="carousel-item">';

}else
	if($i==8){
		$out.='</div>';
$out.='<div class="carousel-item">';

	}
	$out.=$carousel[$i];


}
}catch(PDOException $e){
	$out = '';

}


?>
<!--Carousel Wrapper-->
<div id="multi-item-example" class="carousel slide carousel-multi-item mt-5" data-ride="carousel">

    <!--Slides-->
    <div class="carousel-inner" role="listbox">
<?=$out?>

  <!--/.Third slide-->

</div>
<!--/.Slides-->

</div>
<!--/.Carousel Wrapper-->
</div>
