<?php
    session_start();
    if(!(isset($_GET['id'])))
    {
      header("location: /Adopcja");
      exit();
    }
    require_once "php/dbconnect.php";
    $sql = @new mysqli($addr,$user,$pass,$db);
    mysqli_report(MYSQLI_REPORT_STRICT);
    if ($sql -> connect_errno) {
  echo "Failed to connect to MySQL: ";
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
    <title>Ogłoszenia o Adopcji</title>
</head>
<body>

<div id="banner">
    <a href="/Adopcja"><i><h2>Ogłoszenia adopcji</h2></i></a>
    <?php
    if(!isset($_SESSION['logged']))
    {
      echo '<a href="Zarejestruj"><button type="button">Zarejestruj się</button></a>';
    echo '<a href="Zaloguj"><button type="button">Zaloguj</button></a>';
    echo '<div class="cls"></div>';
    }
    else {
      echo '<a href="/Adopcja/Wyloguj"><button type="button">Wyloguj</button></a>';
            echo '<a href="/Adopcja/Panel"><button type="button">Panel użytkownika</button></a>';
      echo '<p>Witaj '.$_SESSION['Imię'].'!</p>';
    }
    ?>
</div>
<div id="container" style="height: auto;">
    <?php
    if(isset($_GET['id']))
    {
      $id = $_GET['id'];
      $danesz=$sql -> query("SELECT * FROM zwierzeta WHERE id=$id;");
      $oglo=$danesz -> fetch_assoc();
    echo "Born to code";
    if($oglo['Active']==true)
    {
      echo '<div id="anon">';
    echo '<h4>ZWIERZAK POSZUKUJE DOMU</h4>';
    }
    else
    {
      echo '<div class="na">';
      echo '<div id="anon">';
      echo '<h4>ZWIERZAK ZNALAZŁ DOM</h4>';
    }
  echo'<img src="img/zwierzeta/'.$oglo["Source"].'">
    <div id="info">
      <h1>Imię:</h1><p> '.$oglo["Name"].' </p>
      <h1>Typ:</h1><p>'.$oglo["Type"].'</p>
      <h1>Płeć:</h1><p>'.$oglo["Sex"].' </p>
      <h1>Wiek:</h1><p>'. $oglo["Age"].'</p>
      <h1>Data znalezienia:</h1><p>'.$oglo["Date"].'</p>
    </div>
    <div class="cls"></div>
    <h1 class="desc">Opis:</h1>
    <div id="description">
      <p>'.$oglo["Description"].'</p>';
    }
      echo '</div> <h1 class="desc" style="margin-top: 2%;">Kontakt: </h1>';
      echo '<div id="contact"><p> Nr Telefonu:</p>';
      echo '<p style="margin-left: 17%;"><a href="tel:'.$oglo['Tel'].'">'.$oglo['Tel'].'</a></p>';
      echo '<p> Adres e-mail:</p>';
      echo '<p style="margin-left: 17%;"><a href="mailto:'.$oglo['OwnEmail'].'">'.$oglo['OwnEmail'].'</a></p>';
      echo '</div><a href="/Adopcja"><button>Powrót</button></a>';
      ?>
</div>
</div>
  </div>
<div id="footer">
<h3>Zaadoptuj zwierzaka już dziś!</h3>
<form method="post">
<label style="color:white;"for="name">Newsletter: </label>
<input type="email" name="newsletter" placeholder="email">
<button type="submit"><b>ZAPISZ SIĘ</b></button>
</form>
<?php
  if(isset($_POST['newsletter']))
  {
    $email = $_POST['newsletter'];
  $q = $sql -> query("Select Email from newsletter where Email='$email';");
  if($q -> num_rows > 0)
  {
    echo '<p><div class="red"> Taki e-mail jest już zapisany </span> </p>';
  }
  else if(!(filter_var($email,FILTER_VALIDATE_EMAIL)))
  {
    echo '<p><div class="red"> Adres e-mail jest nieprawidłowy! </span> </p>';
  }
  else
  {
    if($q = $sql -> query("Insert into newsletter values ('$email');"))
    {
    echo '<p><span id="green"> Zapisano pomyślnie! </span> </p>';
    mail($email,'Schronisko - Newsletter','Dziekujemy za zapisanie się na newsletter!','From: pehaptest@gmail.com');
    }
    else
    {
    echo '<p><div class="red"> Nie udało się zapisać </span> </p>';
    }
  }
  unset($_POST['email']);
  unset($email);
}
$sql -> close();
?>
</div>
</body>
</html>
