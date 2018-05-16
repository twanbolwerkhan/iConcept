<?php
require('connect.php');
function livesearch($post_livesearch){
  global $dbh;
  global $rubrieken;
  global $subrubrieken;
  global $veilingen;

//Getting value of "search" variable from "script.js".
$return ="";
$rubrieken="";
$name = $post_livesearch;
  // $name = $_POST['livesearch'];
  try{
  $statement = $dbh->prepare("SELECT * FROM Rubriek WHERE rubrieknaam LIKE ? AND rubrieknummerOuder=-1");
  $statement->execute(array("%".$name."%"));
}catch(PDOException $e){
  $error = $e;
}

  while($row = $statement->fetch()){
    $rubrieknaam = $row['rubrieknaam'];
    $function = "fill('".$rubrieknaam."')";
    $rubrieken.="<li onclick='".$function."'><a href='#'>".$rubrieknaam."</li></a>";
  }
  //Sub-rubrieken
  $subrubrieken="";
  try{
    $statement = $dbh->prepare("SELECT * FROM Rubriek WHERE rubrieknaam LIKE ? AND rubrieknummerOuder!=-1 AND rubrieknummerOuder!=1");
    $statement->execute(array("%".$name."%"));
  }catch(PDOException $e){
    $error = $e;
  }

  while($row = $statement->fetch()){
    $rubrieknaam = $row['rubrieknaam'];
    $function = "fill('".$rubrieknaam."')";
    $subrubrieken.="<li onclick='".$function."'><a href='#'>".$rubrieknaam."</li></a>";
  }

  $veilingen="";
  try{
    $statement = $dbh->prepare("SELECT * FROM Voorwerp where titel LIKE ?");
    $statement->execute(array("%".$name."%"));
  }catch(PDOException $e){
    $error = $e;
  }

  while($row = $statement->fetch()){
    $voorwerptitel = $row['titel'];
    $function = "fill('".$rubrieknaam."')";
    $veilingen.="<li onclick='".$function."'><a href='#'>".$voorwerptitel."</li></a>";
  }


}

function displayColumn(){
	global $dbh;
	global $column;
  $column = "";
  try{
    $data = $dbh->query("SELECT * FROM Rubriek");
    while($row = $data->fetch()){
			if($row['rubrieknummerOuder'] == NULL){
      $column.="<a href='?rubrieknummer=".$row['rubrieknummer']."'>".$row['rubrieknaam']."</a>";
		}else if(isset($_GET['rubrieknummer'])){
			if($row['rubrieknummerOuder'] == $_GET['rubrieknummer']){
				$column.="<a href='?rubrieknummer=".$row['rubrieknummer']."'>".$row['rubrieknaam']."</a>";
			}
		}
    }
    }catch(PDOException $e){
      $column = $e;
  }
}

/*search function database table database column and search item EXAMPLE: search(bank); will give $searchResults is an array else $error*/
function search($searchKey,$searchType)
{
	global $dbh;
	global $error;
	global $searchResults;
	$searchResults="";
	try {
		if($searchType == 'voorwerp'){
		$data = $dbh->prepare("SELECT * FROM Voorwerp WHERE titel LIKE ?");
		$data->execute(array('%'.$searchKey.'%'));
    while ($row = $data->fetch()) {
		$searchResults.="Titel: ".$row['titel']." Beschrijving: ".$row['beschrijving'];
	}
}else if($searchType == 'rubriek'){
	$data = $dbh->prepare("SELECT * FROM Voorwerp_in_Rubriek vr RIGHT JOIN Voorwerp v ON v.voorwerpnummer=vr.voorwerpnummer WHERE vr.rubrieknummer = ?");
	$data->execute(array($searchKey));
	while($row = $data->fetch()){
	$searchResults.="Titel: ".$row['titel']." Beschrijving: ".$row['beschrijving'];
	}

}
	}catch(PDOException $e){
		$error = $e;
	}
}


/*display auction*/
function displayAuction()
{


	global $dbh;
	global $auction;
	$auction = "";

	try{
		$data = $dbh->query("select * from Voorwerp");
		while ($row = $data->fetch()) {

			$auction.="  <div class='col-md-4'>
          <div class='card auction-card'>
            <div class='view overlay'>
              <img class='card-img-top' src='https://mdbootstrap.com/img/Mockups/Lightbox/Thumbnail/img%20(67).jpg' alt='Test Card' />
            </div>
            <div class='card-body'>
              <span class='small-font'>20345322</span>
              <h4 class='card-title'>".$row['titel']." #".$row['voorwerpnummer']."</h4>
              <hr>
              <div class='card-text'>
                <p>
                ".$row['beschrijving']."
                </p>
              </div>
              <hr />
              <ul class='list-unstyled list-inline'>
                <li class='list-inline-item pr-2'><i class='fa fa-lg fa-gavel pr-2'></i>&euro;".$row['startprijs']."</li>
                <li class='list-inline-item pr-2'><i class='fa fa-lg fa-clock pr-2'></i></li>
              </ul>
            </div>

            <div class='view overlay mdb-blue'>
              <a href='auction.php?voorwerpnummer=".$row['voorwerpnummer']."' class='veiling-bieden'><div class='mask flex-center rgba-white-slight waves-effect waves-light'></div>
                  <p style='text-align:center'>Bieden</p>
                </div>
              </a>
            </div>
          </div>";
		}
	}catch(PDOException $e){
		$error = $e;
	}

}

/*verification function*/
function verification($getUsername,$getCode)
{

	global $dbh;
  global $codeValid;
  global $submittedCode;
  global $deltaTime;
  global $results;

	$codeValid = true;//codeValid is true until proven that it's not
  $submittedCode = $getCode;
	$username = $getUsername;

	try {//checks if code exists in database
	$statement = $dbh->prepare("select * from Verificatiecode where gebruikersnaam = ? AND code = ?");
	$statement->execute(array($username,$submittedCode));
	$results = $statement->fetch();
	} catch (PDOException $e) {
	$error= "Code invalid";
	$codeValid = false;
	}

	$storedUsername = $results[0];
	$storedTime = $results[1];
	$storedCode = $results[2];

	$deltaTime = time() - $storedTime;

	if ($deltaTime > 14400) {//14400 seconds = 4 hours
	$codeValid = false;
  $error = "Time has expired";
	}

	if ($codeValid) {
	$statement = $dbh->prepare("update Gebruiker set geactiveerd = 1 where gebruikersnaam = ?");
	$statement->execute(array($storedUsername));
	$statement = $dbh->prepare("delete Verificatiecode where gebruikersnaam = ?");
	$statement->execute(array($storedUsername));
	}
}


/*Register function*/
function register($username,$firstname,$lastname,$address1,$address2,$zipcode,$city,$country,$birthdate,$email,$email_check,$password,$password_check,$secretAnswer,$secretQuestion)
{
  global $dbh;
  global $error;
	global $errors;
	$errors = array();


if(empty($username))//checks if username is not empty
{
  $errors['username'] = "Dit is een verplicht veld.";
}
else{
	try {
			$userdata = $dbh->prepare("select * from Gebruiker where gebruikersnaam=?");
			$userdata->execute(array($username));
	} catch (PDOException $e) {
			$error = $e;
	}
	if (($result = $userdata->fetch(PDO::FETCH_ASSOC))) {
			 $errors['username'] = "Deze gebruikersnaam bestaat al.";
	}
}
if(empty($password) || empty($password_check))//checks if password is not empty
{
  $errors['password'] = "Dit is een verplicht veld.";
}else
if($password != $password_check)//checks if password equils password_check
{
  $errors['password'] = "Het wachtwoord komt niet overeen.";
}
if(empty($email) || empty($email_check))//checks if email is not empty
{
 $errors['email'] = "Dit is een verplicht veld.";
}else
if($email != $email_check)//checks if email equils email_check
{
  $errors['email'] = "Het email-adres komt niet overeen.";
}
else{
	try {
			$userdata = $dbh->prepare("select * from Gebruiker where email=?");
			$userdata->execute(array($email));
	} catch (PDOException $e) {
			$error = $e;
	}
	if (($result = $userdata->fetch(PDO::FETCH_ASSOC))) {
			 $errors['email'] = "Dit email-adres is al in gebruik.";
	}
}
if($secretQuestion == "kies")//checks if username is not empty
{
  $errors['secretQuestion'] = "Dit is een verplicht veld.";
}
if(empty($secretAnswer))//checks if username is not empty
{
  $errors['secretAnswer'] = "Dit is een verplicht veld.";
}
if(empty($firstname))//checks if username is not empty
{
  $errors['firstname'] = "Dit is een verplicht veld.";
}
if(empty($lastname))//checks if username is not empty
{
  $errors['lastname'] = "Dit is een verplicht veld.";
}
if(empty($zipcode))//checks if username is not empty
{
  $errors['zipcode'] = "Dit is een verplicht veld.";
}
if(empty($address1))//checks if username is not empty
{
  $errors['address1'] = "Dit is een verplicht veld.";
}
if(empty($city))//checks if username is not empty
{
  $errors['city'] = "Dit is een verplicht veld.";
}
if(empty($country))//checks if username is not empty
{
  $errors['country'] = "Dit is een verplicht veld.";
}
if(empty($birthdate))//checks if username is not empty
{
  $errors['birthdate'] = "Dit is een verplicht veld.";
}

if(count($errors) == 0){//checks if there are errors
    try {
      $userdata = $dbh->prepare("insert into Gebruiker(gebruikersnaam, voornaam, achternaam, adresregel1, adresregel2, postcode, plaatsnaam, land, geboortedatum, email, wachtwoord, vraagnummer, antwoordtekst, verkoper,geactiveerd)
Values(?, ?, ?, ?,?, ?, ?, ?, ?, ?, ?,?, ?,?,?)");
      $userdata->execute(array($username, $firstname, $lastname, $address1,$address2, $zipcode, $city, $country, $birthdate, $email, $password, $secretQuestion, $secretAnswer,0,0));
			copy("img/avatar/avatar.png","img/avatar/".$username.".png");
			header("Location: post_register.php?username={$username}");

    } catch (PDOException $e) {
      $error=$e;
    }
  }
}


/*login function */
function login($username_input,$password)
{

    global $dbh;
    global $error;

		$username = $username_input;
		$email = $username_input;

    // $username=trim($username);
    $password=trim($password);

		$error = array();

    if(strlen($username)>=50){
         $error['username'] = "username has more than 50 characters";
    }else
    if(strlen($password)>=20){
         $error['password'] = "password has more than 20 characters";
    }else
    if(empty($username)){
         $error['username'] = "username is empty";
    }else
    if(empty($password)){
         $error['password'] = "password is empty";
    }else {
        try {
            $username_check = $dbh->prepare("select * from Gebruiker where gebruikersnaam=? OR email=?");
            $username_check->execute(array($username,$email));

        } catch (PDOException $e) {
            $error = $e;
        }


        if (!($username_result = $username_check->fetch(PDO::FETCH_ASSOC))) {
             $error['username'] = "gebruikersnaam klopt niet";
        }
				try{
					$password_check = $dbh->prepare("SELECT * FROM Gebruiker WHERE gebruikersnaam=? AND wachtwoord=? OR email=? AND wachtwoord=?");
					$password_check->execute(array($username,$password,$email,$password));
				}catch(PDOException $e){
					$error = $e;
				}
				if(!($password_result = $password_check->fetch(PDO::FETCH_ASSOC))) {
					$error['password'] = "wachtwoord klopt niet";
				}
				if($password_result && $username_result) {
						try {//checks if user needs verification
							$statement = $dbh->prepare("select verkoper from Gebruiker where gebruikersnaam = ?");
							$statement->execute(array($username));
							$results = $statement->fetch();
						} catch (PDOException $e) {
								$error=$e;
								echo $error;
						}
						$_SESSION['seller'] = $results[0];
            $_SESSION['username'] = $username_result['gebruikersnaam'];
						header('Location: index.php');
        }
			}

}

function random_password( $length = 8 ) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $password = substr( str_shuffle( $chars ), 0, $length );
    return $password;
}

function createVerificationCode($username, $random_password) {
	global $dbh;
	global $error;

    try {
		$userdata = $dbh->prepare("insert into Verificatiecode(gebruikersnaam, begintijd, code) Values(?, ?, ?)");
		$userdata->execute(array($username, time(), $random_password));
    } catch (PDOException $e) {
		$error=$e;
    }
}



function  auctionTimer($voorwerpnummer){
global $dbh;
global $error;
$timer = "3 uur";
	try {
	  $userdata = $dbh->prepare("select * from Voorwerp where ?");
	  $voorwerpdata = $userdata->execute(array($voorwerpnummer));
	  $voorwerpdata->fetch();
	  $looptijd = $voorwerpdata['looptijd'];
	    $looptijdbegindag = $voorwerpdata['looptijdbegindag'];
	      $looptijdbegintijdstip = $voorwerpdata['looptijdbegintijdstip'];
	      $looptijdeindedag = $voorwerpdata['looptijdeindedag'];
	      $looptijdeindetijdstip = $voorwerpdata['looptijdeindetijdstip'];
				$remaining = ($looptijdeindedag+$looptijdeindetijdstip) - time();
				$days_remaining = floor($remaining/86400);
				$hours_remaining = floor(($remaining/86400)/ 3600);
				if($days_remaining>1){
					$timer = $days_remaining;
				}else{
					$timer = $days_remaining + $hours_remaining;
				}
	}catch (PDOException $e) {
	  $error=$e;
	}

	return $timer;
}


function changePassword($new_password)
{
global $error;
global $dbh;
$username=$_SESSION['username'];
try {
	// $dbh->query("update Gebruiker set wachtwoord='$new_password' where gebruikersnaam='$username'");
	$statement=$dbh->prepare("update Gebruiker set wachtwoord = ? where gebruikersnaam=?");
	$statement->execute(array($new_password,$username));

} catch (PDOException $e) {
 	$error =  $e;
}
}

//Takes an image and stores it as {username}.png in /img/avatar
function addAvatar($file, $username){
	global $error;
	global $dbh; //database object

	$error="";

	//If the file is a supported image
	if ((
			 ($file["type"] == "image/jpeg")
		|| ($file["type"] == "image/png")
		|| ($file["type"] == "image/pjpeg")
	) && ($file["size"] < 4000000)) {
		if ($file["error"] > 0) {
			$error.= "Return Code: " . $file["error"] . "<br />";
		} else {
			$error.= "Upload: " . $file["name"] . "<br />";
			$error.= "Type: " . $file["type"] . "<br />";
			$error.=  "Size: " . ($file["size"] / 1024) . " Kb<br />";
			$error.= "Temp file: " . $file["tmp_name"] . "<br />";

			//Move and rename uploaded image
			$filename = "img/avatar/" . $username . ".png";
			move_uploaded_file($file["tmp_name"], $filename);

			$error.= "Stored in: " . $filename;
		}
	} else {
		$error.= $file["type"]."<br />";
		$error.= "Verkeerd bestand, selecteer een nieuwe";
	}
}

function mailUser($username, $soort){
	//
	// global $dbh;
	//
	// $email_address = $dbh->prepare("select * from Gebruiker where gebruikersnaam=?");
	// $fetch_email = $email_addres->execute(array($username));
	// $fetch_email->fetch();


	$to = 'twanbolwerk@gmail.com';

	switch($soort){
	case 'registratie':
		$subject = 'Registratie gelukt!';
		$message = 'Uw registratie is gelukt'. $username .' !';
	break;

	case 'veilingaanmaken':
		$subject = 'Veiling aangemaakt!';
		$message = 'Beste '. $username .', Uw veiling is aangemaakt!';
	break;

	case 'overboden':
		$subject = 'U bent overboden!';
		$message = 'Beste '. $username .', U bent overboden!';
	break;

	case 'veilinggewonnen':
		$veilinggew = $dbh->prepare("select titel from Voorwerp where koper=?");
		$veilinggew->execute(array($username));

		$to = $email_address;
		$subject = 'U heeft de veiling gewonnen!';
		$message = 'Beste '. $username .', U heeft veiling'. $veilinggew.'gewonnen!';
	break;

	case 'wachtwoordvergeten':

	break;


}

	$headers = 'From: webmaster@iproject40.icasites.nl' . "\r\n" .
	    'Reply-To: webmaster@iproject40.icasites.nl' . "\r\n" .
	    'X-Mailer: PHP/' . phpversion();

	mail($to, $subject, $message, $headers);



}



?>
