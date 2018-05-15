<?php
if(!isset($_SESSION['username'])){header('Location: index.php');}
$current_page='register_seller';
require_once('templates/header.php');
require_once('templates/connect.php');
$username = $_SESSION['username'];


function createVerificationCodeSeller($username, $random_password) {
	global $dbh;
	global $error;

    try {
		$userdata = $dbh->prepare("insert into VerificatieVerkoper(gebruikersnaam, code) Values(?, ?)");
		$userdata->execute(array($username, $random_password));
    } catch (PDOException $e) {
		$error=$e;
    }
}

function registerSeller($username, $checkoption, $creditcard, $bank, $banknumber){
  global $dbh;
  global $errors;
  $errors = array();

  try {
    $data = $dbh->prepare("select verkoper from Gebruiker where gebruikersnaam = ?");
    $data->execute(array($username));
    $seller = $data->fetch();

  } catch (PDOException $e) {
    $error=$e;
    echo $error;
  }
  if($seller[0] == 1)//checks if user is seller
  {
    $errors['seller'] = "U bent al een verkoper.";
  }
  else {
    if(empty($checkoption))//checks if checkoption is not empty
    {
      $errors['checkoption'] = "Dit is een verplicht veld.";
    }
    if($checkoption == "creditcard" && empty($creditcard))//checks if title is not empty and if creditcardoption is selected
    {
      $errors['creditcard'] = "Dit is een verplicht veld.";
    }
    if(empty($bank) && empty($creditcard) && $checkoption != "creditcard"){
      $errors['bank'] = "Dit is een verplicht veld.";
    }
    if(empty($banknumber) && empty($creditcard) && $checkoption != "creditcard"){
      $errors['banknumber'] = "Dit is een verplicht veld.";
    }

    if(count($errors) == 0){//checks if there are errors
      if($checkoption == "creditcard"){
				try {
					$data = $dbh->prepare("insert into Verkoper(gebruikersnaam, controleoptienaam, creditcardnummer, banknaam, rekeningnummer) values(?, ?, ?, ?, ?)");
					$data->execute(array($username, $checkoption, $creditcard,  null, null));
					$userdata = $dbh->prepare("update Gebruiker set verkoper = 1 where gebruikersnaam = ?;");
	        $userdata->execute(array($username));
				}
				catch (PDOException $e) {
					$error=$e;
					echo $error;
				}
      }
      else {
				$data = $dbh->prepare("insert into Verkoper(gebruikersnaam, controleoptienaam, creditcardnummer, banknaam, rekeningnummer) values(?, ?, ?, ?, ?)");
				$data->execute(array($username, $checkoption, null,  $bank, $banknumber));
        $code = random_password(6);
  			createVerificationCodeSeller($username, $code);
      }
    }
  }
}

if(isset($_POST['registerseller'])){
  registerSeller($username, $_POST['checkoption'],$_POST['creditcard'],$_POST['bank'],$_POST['banknumber']);
}

?>



<form action="" method="post">
	<div class="userpage-form-header">
		<h1>Verkoper worden</h1>
	</div>
	<div class="form-row">
		<div class="col-md-12">
		<div class="form-group">
			<label style="black-text" for="checkoption">Selecteer een controleoptie</label>
  <select name="checkoption" class="form-control dropdown-userpage">
    <option value="post">Code via post</option>
    <option value="creditcard">Creditcard</option>
  </select>
</div>
</div>
</div>
	<div class="form-row">
		<div class="col-md-12">
			<div class="md-form">
	  		<label class="black-text" for="creditcard">Creditcardnummer</label>
	  		<input type="text" class="form-control" name="creditcard" value="<?php if(isset($_POST['creditcard'])){echo $_POST['creditcard'];}?>">
				<?php if(isset($errors['creditcard'])){echo $errors['creditcard'];}?>
			</div>
		</div>
	</div>
	<div class="form-row">
		<div class="col-md-12">
			<div class="md-form">
				<label style="black-text" for="bank">Banknaam</label>
			  <input type="text" class="form-control" name="bank" value="<?php if(isset($_POST['bank'])){echo $_POST['bank'];}?>">
			  <?php if(isset($errors['bank'])){echo $errors['bank'];}?>
			</div>
		</div>
	</div>
	<div class="form-row">
		<div class="col-md-12">
			<div class="md-form">
				<label style="black-text" for="banknumber">Rekeningnummer</label>
			  <input type="text" class="form-control" name="banknumber" value="<?php if(isset($_POST['banknumber'])){echo $_POST['banknumber'];}?>">
			  <?php if(isset($errors['banknumber'])){echo $errors['banknumber'];}?>
			</div>
		</div>
	</div>



	<div class="py-1 mt-3 text-center">
  <button class="btn elegant" type="submit" name="registerseller">Word verkoper</button>
</div>
</form>
