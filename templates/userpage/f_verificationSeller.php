<?php
function verificationSeller($username,$code)
{
	global $dbh;
  global $message;

	try {//gets verificatoncode of user from database
		$statement = $dbh->prepare("SELECT code FROM VerificatieVerkoper WHERE gebruikersnaam = ?");
		$statement->execute(array($username));
		$results = $statement->fetch();
	} catch (PDOException $e) {
		$error = $e;
		echo $error;
	}

	if($results[0] == $code){//checks if filled in code is the same as the code in the database
		try {
			$statement = $dbh->prepare("update Gebruiker set verkoper = 1 where gebruikersnaam = ?");
			$statement->execute(array($username));
			$statement = $dbh->prepare("delete VerificatieVerkoper where gebruikersnaam = ?");
			$statement->execute(array($username));
			$_SESSION['seller'] = 1;
      $message = "<p class='green-text lead'>U bent succesvol registreerd als verkoper.</p>";
		} catch (PDOException $e) {
			$error = $e;
			echo $error;
		}
	}
  else {
    $message = "<p class='red-text lead'>De opgegeven verificatiecode is onjuist.</p>";
  }
}
?>
