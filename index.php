<?php
    session_start();
    require_once "php/dbconnect.php";
    $sql = @new mysqli($addr,$user,$pass,$db);
    mysqli_report(MYSQLI_REPORT_STRICT);
    if ($sql -> connect_errno) {
  echo "Failed to connect to MySQL: ";
  exit();
}

if(!(isset($_SESSION['page'])))
{
  $_SESSION['page']=0;
}
if(isset($_POST['type']))
{
$_SESSION['name'] = $_POST['name'];
$_SESSION['type'] = $_POST['type'];
$_SESSION['sex'] = $_POST['sex'];
$_SESSION['age'] = $_POST['age'];
$_SESSION['date'] = $_POST['date'];
if(empty($_POST['name']))
{
  $name = 'like "%"';
}
else
{
  $name = '="'.$_POST['name'].'"';
}
if(empty($_POST['type']))
{
  $type = 'like "%"';
}
else
{
$type = '="'.$_POST['type'].'"';
}
if(empty($_POST['sex']))
{
  $sex = 'like "%"';
}
else
{
$sex = '="'.$_POST['sex'].'"';
}
$age = $_POST['age'];
switch($age)
{
  case "1 rok i poniżej":{$age='<=1'; break;}
  case "1 - 4 lata":{$age='between 1 and 4'; break;}
  case "5 - 10 lat":{$age='between 5 and 10'; break;}
  case "Powyżej 10 lat":{$age='> 10'; break;}
}
$date = $_POST['date'];
if(isset($_POST['active']))
{
$_SESSION['active'] = $_POST['active'];
$active = 'and Active=1';
}
else
{
 $active = '';
}
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
<div id="container">
  <div id="search">
    <h1>Znajdź zwierzaka</h1><hr>
    <form method="post">
      <div class="search">
    <p>Imię:</p>
      <input type="text" name="name" placeholder="Imię" value="<?php
    if(isset($_SESSION['name']))
    {
      echo $_SESSION['name'];
      unset($_SESSION['name']);
    }
    ?>">
    </div>
    <div class="search">
     <p> Typ:</p>
      <select name="type">
        <option></option>
        <option <?php
        if(isset($_SESSION['type']))
    {
    if($_SESSION['type']=='Kot')
    {
      echo "selected";
      unset($_SESSION['type']);
    }
    }
    ?>>Kot</option>
        <option <?php
        if(isset($_SESSION['type']))
    {
    if($_SESSION['type']=='Pies')
    {
      echo "selected";
      unset($_SESSION['type']);
    }
    }
    ?>>Pies</option>
      </select>
  </div>
  <div class="search">
  <p> Płeć:</p>
      <select name="sex">
      <option></option>
        <option <?php
        if(isset($_SESSION['sex']))
    {
    if($_SESSION['sex']=='Samiec')
    {
      echo "selected";
      unset($_SESSION['sex']);
    }
    }
    ?>>Samiec</option>
        <option <?php
        if(isset($_SESSION['sex']))
    {
    if($_SESSION['sex']=='Samica')
    {
      echo "selected";
      unset($_SESSION['sex']);
    }
    }
    ?>>Samica</option>
      </select>
  </div>
  <div class="search">
  <p> Wiek:</p>
      <select name="age">
      <option></option>
        <option <?php
        if(isset($_SESSION['age']))
    {
    if($_SESSION['age']=='1 rok i poniżej')
    {
      echo "selected";
      unset($_SESSION['age']);
    }
    }
    ?>>1 rok i poniżej</option>
        <option <?php
        if(isset($_SESSION['age']))
    {
    if($_SESSION['age']=='1 - 4 lata')
    {
      echo "selected";
      unset($_SESSION['age']);
    }
    }
    ?>>1 - 4 lata</option>
        <option <?php
        if(isset($_SESSION['age']))
    {
    if($_SESSION['age']=='5 - 10 lat')
    {
      echo "selected";
      unset($_SESSION['age']);
    }
    }
    ?>>5 - 10 lat</option>
        <option <?php
        if(isset($_SESSION['age']))
    {
    if($_SESSION['age']=='Powyżej 10 lat')
    {
      echo "selected";
      unset($_SESSION['age']);
    }
    }
    ?>>Powyżej 10 lat</option>
      </select>
  </div>
  <div class="search">
    <p> Przed: </p>
      <input type="date" name="date" value="<?php
    if(isset($_SESSION['date']))
    {
      echo $_SESSION['date'];
      unset($_SESSION['date']);
    }
    ?>">
    </div>
    <div class="search">
      <label for="name"> <p>Aktualne:</p> </label>
      <input type="checkbox" name="active" <?php
      if(isset($_SESSION['active']))
      {
    if($active)
    {
      echo "checked";
      unset($_SESSION['active']);
    }
  }
    ?>>
    </div>
      <button type="submit"><b>SZUKAJ</b></button>
    </form>
    <hr>
  </div>
  <div id="annoucements">
<?php
if(isset($_POST['type']))
{
  $q = $sql -> query("Select * from zwierzeta where Name $name and Type $type and Sex $sex and Age $age and Date>'$date' $active order by date desc;");
}
else
{
    $q = $sql -> query("Select * from zwierzeta order by date desc;");
}
$results = 0;
while($l = $q -> fetch_assoc())
{
  if( $_SESSION['page']*9 <= $results&&$results<9+9*$_SESSION['page'])
  {
    echo '<a href="Ogloszenie?id='.$l['id'].'">';
    if($l['Active']==false)
    {
      echo '<div class="notactive">';
    }
    else
    {
    echo '<div class="window">';
    }
    echo '<img src="img/zwierzeta/'.$l['Source'].'" <p><h1>'.$l['Name'].'</h1></p>';
    echo '<p>'.$l['Sex'].'</p>';
    echo '<p>Lat: '.$l['Age'].'</p>';
    echo '<p> Data publikacji: '.$l['Date'].'</p></div></a>';
  }
  $results++;
}
echo  '<div id="page">';
  echo '<a href="php/deai.php"><button ';
if($_SESSION['page']==0)
{
  echo 'style="visibility: hidden;"';
}
echo '>Poprzednia</button></a>';
echo  ' Strona: '.($_SESSION['page']+1);
if($results/9>$_SESSION['page']+1)
echo '<a href="php/ai.php"><button>Następna</button></a></div>';
echo '</div>';
?>
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
  $email1 = filter_var($email,FILTER_SANITIZE_EMAIL);

  if($q -> num_rows > 0)
  {
    echo '<p><span class="red"> Taki e-mail jest już zapisany </span> </p>';
  }
  else if ($email != $email1 || filter_var($email,FILTER_VALIDATE_EMAIL)==false)
  {
    echo '<p><span class="red" style="padding-top: 0;"> Adres e-mail jest nieprawidłowy! </span> </p>';
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
    echo '<p><span class="red"> Nie udało się zapisać </span> </p>';
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
