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
    <title>Ogłoszenia o Adopcji</title>
</head>
<body>

<div id="banner">
    <a href="/Adopcja"><i><h2>Ogłoszenia adopcji</h2></i></a>
  </div>
<div style="height: 800px;" id="container">
<form id="signup" method="post">
<?php
if(isset($_GET['Token']))
{
  $token = $_GET['Token'];
  require_once "php/dbconnect.php";
  $sql = @new mysqli($addr,$user,$pass,$db);
  mysqli_report(MYSQLI_REPORT_STRICT);
  if ($sql -> connect_errno) 
  {
    echo "Failed to connect to MySQL: ";
    exit();
  }
  $q= $sql -> query("SELECT * FROM users WHERE token='$token';");
  if(($q -> num_rows)>0)
  {
    if($sql -> query("UPDATE users set active=1 WHERE token='$token';"))
    {
      $tolkien = bin2hex(random_bytes(25));
      $sql -> query("UPDATE users set token='$tolkien' WHERE token='$token';");
      echo '<span id="green" style="margin: 0; text-align: center; width: 100%;"><span style="font-size: 20px;">Pomyślnie aktywowano konto!</span>';
    }
    else {
      echo '<span class="red" style="margin: 0; text-align: center; width: 100%;"><span style="font-size: 20px;">Nie udało się aktywować konta. Sprawdź czy link jest poprawny.</span>';

    }
  }
  else {
    echo '<span class="red" style="margin: 0; text-align: center; width: 100%;"><span style="font-size: 20px;">Nie udało się znaleźć konta. Sprawdź czy link jest poprawny.</span>';
  }
}
else
{
    header("Location: Adopcja");
    exit();
}
echo '<p style=" text-align: center; width: 30%; margin-top: 3%;"><a href="/Adopcja"><button>Powrót</button></a></p>';
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
