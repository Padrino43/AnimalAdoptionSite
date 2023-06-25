<?php
    session_start();
    if(isset($_SESSION['logged']))
    {
      header("location: /Adopcja");
      exit();
    }
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Adopcja psów">
    <meta name="keywords" content="Psy, Koty, Schronisko, Adopcja, Ogłoszenie, Nowy dom">
    <link rel="icon" href="#">
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <title>Zaloguj się</title>
</head>
<body>

<div id="banner">
    <a href="/Adopcja"><i><h2>Ogłoszenia adopcji</h2></i></a>
  </div>
<div style="height: 600px;" id="container">
<?php
if(isset($_POST['login']))
{
  $l = $_POST['login'];
  $p = $_POST['pass'];
  require_once "php/dbconnect.php";
  $sql = @new mysqli($addr,$user,$pass,$db);
  mysqli_report(MYSQLI_REPORT_STRICT);
  if ($sql -> connect_errno)
  {
    echo "Failed to connect to MySQL: ";
    exit();
  }
  $q= $sql -> query("SELECT * FROM users WHERE Login='$l';");
  $result = $q -> fetch_assoc();
  if(($q-> num_rows)>0)
  {
    if(password_verify($p,$result['Password']))
    {
      if($result['Active']==1)
      {
        $_SESSION['logged']=true;
        $_SESSION['UserId']=$result['UserId'];
        $_SESSION['Imię']=$result['Imię'];
        $_SESSION['Nazwisko']=$result['Nazwisko'];
        $_SESSION['Email']=$result['Email'];
        header("Location: /Adopcja");
      }
      else {
        $_SESSION['e_log1'] = '<div class="red">Konto nie jest aktywowane.</div>';
      }
    }
    else {
      $_SESSION['e_log2'] = '<div class="red">Błędny login lub hasło.</div>';
    }
  }
  else {
    $_SESSION['e_log2'] = '<div class="red">Błędny login lub hasło.</div>';
  }
}
if(isset($sql))
{
$sql -> close();
}
 ?>
<form id="signin" method="post">
  <h1>Zaloguj się: </h1>
<p>Login:<br><input type="text" name="login" placeholder="Login"></p>
<p>Hasło:<br><input type="password" name="pass" placeholder="Hasło"></p><br>
<?php
if(isset($_SESSION['e_log1']))
{
  echo $_SESSION['e_log1'];
  unset($_SESSION['e_log1']);
}
else if(isset($_SESSION['e_log2']))
{
    echo $_SESSION['e_log2'];
    unset($_SESSION['e_log2']);
}
 ?>
 <p class="red" style="color: white;"><a href="Zmiana-hasla">Nie pamiętasz hasła?</a></p>
 <div class="buttons">
<button type="submit">Zaloguj</button>
 <button type="button" onclick="history.back()">Powrót</button>
</div>

</form>
</div>
<div id="footer">
<h3>Zaadoptuj zwierzaka już dziś!</h3>
</div>
</body>
</html>
