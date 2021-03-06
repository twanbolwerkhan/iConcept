<?php
/*Register function*/
function register($username,$firstname,$lastname,$address1,$address2,$zipcode,$city,$country,$birthdate,$email,$email_check,$password,$password_check,$secretAnswer,$secretQuestion)
{
  global $dbh;
  global $error;
	global $messages;
	$messages = array();

  //Remove any doublequotes and html tags
  $username = str_replace("\"", "", strip_tags($username));
  $firstname = str_replace("\"", "", strip_tags($firstname));
  $lastname = str_replace("\"", "", strip_tags($lastname));
  $address1 = str_replace("\"", "", strip_tags($address1));
  $address2 = str_replace("\"", "", strip_tags($address2));
  $zipcode = str_replace("\"", "", strip_tags($zipcode));
  $city = str_replace("\"", "", strip_tags($city));
  $country = str_replace("\"", "", strip_tags($country));
  $birthdate = str_replace("\"", "", strip_tags($birthdate));
  $email = str_replace("\"", "", strip_tags($email));


  $password = str_replace("\"", "", strip_tags($password));




  $secretAnswer = str_replace("\"", "", strip_tags($secretAnswer));
  $secretQuestion = str_replace("\"", "", strip_tags($secretQuestion));

if(empty($username))//checks if username is not empty
{
  $messages['username'] = "Dit is een verplicht veld.";
}
else{
	try {
			$userdata = $dbh->prepare("select * from Gebruiker where gebruikersnaam=?");
			$userdata->execute(array($username));
	} catch (PDOException $e) {
			$error = $e;
	}
	if (($result = $userdata->fetch(PDO::FETCH_ASSOC))) {
			 $messages['username'] = "Deze gebruikersnaam bestaat al.";
	}
}
if(empty($password) || empty($password_check))//checks if password is not empty
{
  $messages['password'] = "Dit is een verplicht veld.";
}else
if($password != $password_check)//checks if password equils password_check
{
  $messages['password'] = "Het wachtwoord komt niet overeen.";
}
if(empty($email) || empty($email_check))//checks if email is not empty
{
 $messages['email'] = "Dit is een verplicht veld.";
}else
if($email != $email_check)//checks if email equils email_check
{
  $messages['email'] = "Het email-adres komt niet overeen.";
}
else{
	try {
			$userdata = $dbh->prepare("select * from Gebruiker where email=?");
			$userdata->execute(array($email));
	} catch (PDOException $e) {
			$error = $e;
	}
	if (($result = $userdata->fetch(PDO::FETCH_ASSOC))) {
			 $messages['email'] = "Dit email-adres is al in gebruik.";
	}
}
if($secretQuestion == "kies")//checks if username is not empty
{
  $messages['secretQuestion'] = "Dit is een verplicht veld.";
}
if(empty($secretAnswer))//checks if username is not empty
{
  $messages['secretAnswer'] = "Dit is een verplicht veld.";
}
if(empty($firstname))//checks if username is not empty
{
  $messages['firstname'] = "Dit is een verplicht veld.";
}
if(empty($lastname))//checks if username is not empty
{
  $messages['lastname'] = "Dit is een verplicht veld.";
}
if(empty($zipcode))//checks if username is not empty
{
  $messages['zipcode'] = "Dit is een verplicht veld.";
}
if(empty($address1))//checks if username is not empty
{
  $messages['address1'] = "Dit is een verplicht veld.";
}
if(empty($city))//checks if username is not empty
{
  $messages['city'] = "Dit is een verplicht veld.";
}
if(empty($country))//checks if username is not empty
{
  $messages['country'] = "Dit is een verplicht veld.";
}
if(empty($birthdate))//checks if username is not empty
{
  $messages['birthdate'] = "Dit is een verplicht veld.";
}

if(count($messages) == 0){//checks if there are errors
    try {
      $hash=password_hash($password, PASSWORD_DEFAULT);
      $userdata = $dbh->prepare("insert into Gebruiker(gebruikersnaam, voornaam, achternaam, adresregel1, adresregel2, postcode, plaatsnaam, land, geboortedatum, email, wachtwoord, vraagnummer, antwoordtekst, verkoper,geactiveerd)
Values(?, ?, ?, ?,?, ?, ?, ?, ?, ?, ?,?, ?,?,?)");
      $userdata->execute(array($username, $firstname, $lastname, $address1,$address2, $zipcode, $city, $country, $birthdate, $email, $hash, $secretQuestion, $secretAnswer,0,0));
			copy("img/avatar/avatar.png","img/avatar/".$username.".png");
			header("Location: post_register.php?username={$username}");

    } catch (PDOException $e) {
      $error=$e;
    }
  }
}
?>
