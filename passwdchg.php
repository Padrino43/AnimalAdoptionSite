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
    <title>Poproś o nowe hasło</title>
</head>
<body>

<div id="banner">
    <a href="/Adopcja"><i><h2>Ogłoszenia adopcji</h2></i></a>
  </div>
<div style="height: 550px;" id="container">
<?php
if(isset($_POST['email']))
{
  $e=$_POST['email'];
  $email1 = filter_var($e,FILTER_SANITIZE_EMAIL);
  if($e == $email1 && filter_var($e,FILTER_VALIDATE_EMAIL)==true){
    require_once "php/dbconnect.php";
    $sql = @new mysqli($addr,$user,$pass,$db);
    mysqli_report(MYSQLI_REPORT_STRICT);
    if ($sql -> connect_errno) 
    {
  echo "Failed to connect to MySQL: ";
  exit();
    }
  $q = $sql ->query("SELECT * FROM users WHERE Email='$e';");
  if(($q -> num_rows)>0)
  {
    $q1 = $sql -> query("SELECT * FROM users WHERE (now() - INTERVAL 1 HOUR) >= ChgDate;");
    if(($q1 -> num_rows) > 0)
    {
    $result = $q ->fetch_assoc();
    $token = $result['Token'];
    mail($e,"Resetowanie hasła",
    "Witaj ".$result['Imię'].",
    właśnie wygenerowano link do zmiany twojego hasła, który znajduje się poniżej:
    /localhost/Adopcja/Nowe-haslo?Token=$token
    Login do logowania:".$result['Login'],"From: pehaptest@gmail.com");
    $sql -> query("UPDATE users SET Passwordchg=1, ChgDate=now()  WHERE Email='$e';");
    unset($_POST['email']);
    unset($e);
    echo '
    <form id="passwdchg" style=" height: 200px;" method="post">
    <p style="margin: 0; text-align: center; width: 100%;"><span style="font-size: 20px;" id="green">Wysłano link na emaila! </span></p>';
    echo '<br><p style="text-align: center;"><a href="/Adopcja"><button>Powrót</button></a></p>';
    }
    else
    {
      $q2 = $sql -> query("SELECT TIMESTAMPDIFF(SECOND,ChgDate, now()) as Seconds FROM users where Email='$e';");
      $result1 = $q2 -> fetch_assoc();
      if ($result1['Seconds'] <= 60)
      {
        $time = '0 minut temu';
      }
      else if($result1['Seconds'] > 60 && $result1['Seconds'] <= 120)
      {
        $time = ' minutę temu';
      }
      else
      {
        $time = floor($result1['Seconds']/60).' minuty temu';
      }
      echo '<form id="passwdchg" style=" height: 200px;" method="post">
      <p style="margin: 0; text-align: center; width: 100%;"><span style="font-size: 20px;" class="red">Twoje ostatnie żądanie o hasło zostało wysłane około '.$time.'! </span></p>';
      echo '<br><p style="text-align: center;"><a  href="/Adopcja"><button>Powrót</button></a></p>';
    }
  }
  else
  {
    echo '<form id="passwdchg" style=" height: 200px;" method="post">
    <p style="margin: 0; text-align: center; width: 100%;"><span style="font-size: 20px;" class="red">Nie znaleziono maila! </span></p>';
    echo '<br><p style="text-align: center;"><a  href="/Adopcja"><button>Powrót</button></a></p>';
  }
}
else {
  echo '<form id="passwdchg" style=" height: 200px;" method="post">
  <p style="margin: 0; text-align: center; width: 100%;"><span style="font-size: 20px;" class="red">Podany mail jest nieprawidłowy! </span></p>';
  echo '<br><a href="/Adopcja"><button class="button">Powrót</button></a>';
}
unset($_POST['email']);
unset($e);
}
else
{
  echo '
<form id="passwdchg" style=" height: 200px;" method="post">
  <h1>Wpisz e-mail aby zresetować hasło: </h1>
<p style="margin-top: 3%;">E-mail:<br><input type="email" name="email" placeholder="E-mail"></p>
<div class="buttons">
<button class="button" type="submit">Zresetuj hasło</button>
<a href="/Adopcja"><button class="button" type="button">Powrót</button></a></div>';
if(isset($_SESSION['e_email']))
{
  echo $_SESSION['e_email'];
  unset($_SESSION['e_email']);
}
}
if(isset($sql))
{
$sql -> close();
}
 ?>


</form>
</div>
<div id="footer">
<h3>Zaadoptuj zwierzaka już dziś!</h3>
</div>
</body>
</html>
