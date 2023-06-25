<?php
    session_start();
    if(isset($_SESSION['logged'])||(!(isset($_GET['Token']))))
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
    <title>Zmień hasło</title>
</head>
<body>

<div id="banner">
    <a href="/Adopcja"><i><h2>Ogłoszenia adopcji</h2></i></a>
  </div>
<div style="height: 600px;" id="container">
<?php
if(isset($_GET['Token']))
{
  $token=$_GET['Token'];
  require_once "php/dbconnect.php";
  $sql = @new mysqli($addr,$user,$pass,$db);
  mysqli_report(MYSQLI_REPORT_STRICT);
  if ($sql -> connect_errno) 
  {
    echo "Failed to connect to MySQL: ";
    exit();
  }
  $q= $sql -> query("SELECT * FROM users WHERE Token='$token' AND Passwordchg=1;");
  if(($q -> num_rows)>0)
  {
  if(isset($_POST['pass']))
  {
  $np= $_POST['pass'];
  if($np==$_POST['pass1'])
  {
    $wszystko_ok = true;
      $uppercase = preg_match('@[A-Z]@', $_POST['pass']);
      $lowercase = preg_match('@[a-z]@', $_POST['pass']);
      $number    = preg_match('@[0-9]@', $_POST['pass']);
      $specialChars = preg_match('@[^\w]@', $_POST['pass']);
    
    if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($_POST['pass']) < 8 || strlen($_POST['pass']) > 32)
    {
        $wszystko_ok = false;
    }
      if($wszystko_ok)
      {
        $np=password_hash($np,PASSWORD_BCRYPT);
        $sql -> query("UPDATE users SET Passwordchg=0, Password='$np' WHERE Token='$token';");
        unset($_GET['Token']);
        $r = $q -> fetch_assoc();
        $tolkien = bin2hex(random_bytes(25));
        while ($r['Token']==$tolkien)
        {
          $tolkien = bin2hex(random_bytes(25));
        }
      $sql -> query("UPDATE users SET Token='$tolkien' WHERE Token='$token';");
      echo '<form id="passwdchg" style=" height: 200px;" method="post">
          <p style="margin: 0; text-align: center; width: 100%;"><span style="font-size: 20px;" id="green">Pomyślnie zresetowano hasło! </span></p>';
          echo '<br><p style="text-align: center;"><a href="/Adopcja/Zaloguj"<button>Powrót</button></a></p>';
      }
      else
      {
        echo '<form id="signin" method="post">
        <h1>Zresetuj hasło: </h1>
        <p style="margin: 0; text-align: center; width: 100%;"><span style="font-size: 20px;" class="red">Hasło musi zawierać od 8 do 32 znaków w tym małą i wielką literę oraz znak specjalny.</span></p>
      <p>Hasło:<br><input type="password" name="pass" placeholder="Hasło"></p>
      <p>Powtórz hasło:<br><input type="password" name="pass1" placeholder="Powtórz hasło"></p><br>
      <button style=" float: left; margin-left: 45%; " type="submit">Zmień hasło</button>';
      }
    }
    else 
    {
      echo '<form id="signin" method="post">
      <h1>Zresetuj hasło: </h1>
      <p style="margin: 0; text-align: center; width: 100%;"><span style="font-size: 20px;" class="red">Hasła różnią się.</span></p>
    <p>Hasło:<br><input type="password" name="pass" placeholder="Hasło"></p>
    <p>Powtórz hasło:<br><input type="password" name="pass1" placeholder="Powtórz hasło"></p><br>
    <button style=" float: left; margin-left: 45%; " type="submit">Zmień hasło</button>';
    }
  }
  else {
    echo '<form id="signin" method="post">
      <h1>Zresetuj hasło: </h1>
    <p>Hasło:<br><input type="password" name="pass" placeholder="Hasło"></p>
    <p>Powtórz hasło:<br><input type="password" name="pass1" placeholder="Powtórz hasło"></p><br>
    <button style=" float: left; margin-left: 45%; " type="submit">Zmień hasło</button>';
  }
  }
  else {
    echo '<form id="passwdchg" style=" height: 200px;" method="post">
        <p style="margin: 0; text-align: center; width: 100%;"><span style="font-size: 20px;" class="red">Podany link jest nieprawidłowy! </span></p>';
        echo '<br><p style="text-align: center;"><a  href="/Adopcja/Zaloguj"<button>Powrót</button></a></p>';
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
