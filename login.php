<?php
$current_page='login';
require_once('templates/header.php');
require_once("templates/login/f_login.php");
if(isset($_GET['redirect'])){
  $message=$_GET['redirect'];//message from forgot_password
  $message_array = explode('naar ',$message);
  $get_username = $message_array[1];
}
//login SCRIPTS
if(isset($_POST['submit'])){//if submit pressed
  login($_POST['username'], $_POST['password']);//login function
}

?>

<!--Main Layout-->
<main class="py-5 mask rgba-black-light flex-center">
  <div class="bg bg-login"></div>
  <!-- Card -->
  <div class="container col-md-4">
    <div class="card login-register-card">
      <!-- Card body -->
      <div class="card-body">
        <!-- Login header text -->
        <div class="login-form-header elegant">
          <h3>Inloggen</h3>
        </div>
        <div class="red-text" style="text-align: center;font-weight: bold;">
        <?php
        if(isset($messages)) {
          foreach ($messages as $message) {
            $message;
          }
        }
        ?>
      </div>
        <!-- Material form login -->
        <form action="" method="post" autocomplete="on">
          <!-- Material input username -->
          <div class="md-form">
            <i class="fa fa-user prefix niagara"></i>
            <input type="text" id="username" class="form-control white-text" name="username" <?php if(isset($_GET['redirect'])){echo "value='".$get_username."'";}?> autofocus required>
            <label for="username" class="font-weight-light">Gebruikersnaam</label>
          </div>
          <!-- Material input username -->
          <div class="md-form">

            <i class="fa fa-lock prefix niagara"></i>
            <input type="password" id="password" class="form-control white-text" name="password" aria-describedby="passwordHelp" required>
            <label for="password" class="font-weight-light">Wachtwoord</label>
            <small id="passwordHelp" class="form-text text-muted red-text"><a href="forgot_password.php">Wachtwoord vergeten?</a></small>
          </div>


          <div class="text-center py-1 mt-3">
            <button class="btn elegant" type="submit" name="submit">Inloggen</button>
          </div>
        </form>
      </div>

      <!-- Card body -->
    </div>
    <!-- Card -->
  </div>
</main>
<!--Main Layout-->
<?php include 'templates/footer.php'; ?>
