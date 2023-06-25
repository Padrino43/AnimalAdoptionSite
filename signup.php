<?php
    session_start();
    if(isset($_SESSION['logged']))
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
    <title>Zarejestruj się</title>
</head>
<body>

<div id="banner">
    <a href="/Adopcja"><i><h2>Ogłoszenia adopcji</h2></i></a>
  </div>
<div style="height: 950px;" id="container">
<?php
if(isset($_POST['fname']))
{
  $pf=true;
  $fname=$_POST['fname'];
  $lname=$_POST['lname'];
  $email=$_POST['email'];
  $login=$_POST['login'];
  $pass=$_POST['pass'];
  $pass1=$_POST['pass1'];
  $_SESSION['afname'] = $fname;
  $_SESSION['alname'] = $lname;
  $_SESSION['aemail'] = $email;
  $_SESSION['alogin'] = $login;
  if(!(strlen($fname)>2 && strlen($fname)<=32))
  {
    $pf=false;
    $_SESSION['e_fname'] = 'Imię musi się zawierać w przedziale od 3 do 32 znaków.';
  }
  if(!(strlen($lname)>2 && strlen($lname)<=40))
  {
    $pf=false;
    $_SESSION['e_lname'] = 'Nazwisko musi się zawierać w przedziale od 3 do 40 znaków.';
  }
  if(!(strlen($login)>2 && strlen($login)<=30))
  {
    $pf=false;
    $_SESSION['e_login'] = 'Login musi się zawierać w przedziale od 3 do 40 znaków.';
  }
  $email1 = filter_var($email,FILTER_SANITIZE_EMAIL);
  if($email != $email1 || filter_var($email,FILTER_VALIDATE_EMAIL)==false)
  {
    $pf=false;
    $_SESSION['e_email'] = 'Podany email jest nieprawidłowy.';
  }
  $uppercase = preg_match('@[A-Z]@', $pass);
  $lowercase = preg_match('@[a-z]@', $pass);
  $number    = preg_match('@[0-9]@', $pass);
  $specialChars = preg_match('@[^\w]@', $pass);

if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($pass) < 8 || strlen($pass) > 32)
{
    $_SESSION['e_pass'] = 'Hasło musi zawierać od 8 do 32 znaków w tym małą i wielką literę oraz znak specjalny.';
    $pf = false;
}
if($pass!=$pass1)
{
  $pf=false;
  $_SESSION['e_pass1'] = 'Hasła różnią się od siebie';

}
if(!(isset($_POST['accept'])))
{
  $pf=false;
  $_SESSION['e_regulamin'] = 'Należy zaznaczyć zgodę na regulamin.';
}
else {
  $_SESSION['aregulamin']=0;
}
    $q = $sql -> query("SELECT * FROM users WHERE Login='$login'; ");
    if(($q -> num_rows)>0)
    {
      $pf=false;
      $_SESSION['e_logine'] = 'Istnieje login w bazie';
    }
    $q = $sql -> query("SELECT * FROM users WHERE Email='$email'; ");
    if(($q -> num_rows)>0)
    {
      $pf=false;
      $_SESSION['e_emaile'] = 'Istnieje email w bazie';
    }
if($pf)
{
  do{
  $token = bin2hex(random_bytes(25));
  $q = $sql -> query("SELECT * FROM users WHERE Token='$token';");
}while(($q -> num_rows)>0);
  $passhash = password_hash($pass,PASSWORD_BCRYPT);
  if($sql -> query("INSERT into users (Login,Password,Imię,Nazwisko,Email,Token,Active) values ('$login','$passhash','$fname','$lname','$email','$token',0);"))
  {
    $_SESSION['rejestracja_ok']=0;
    $message = '
Dziękujemy za rejestrację na naszym portalu
Poniżej znajduje się link aktywacyjny:
localhost/Adopcja/Potwierdz?Token='.$token;
    mail($email,'Schronisko - Rejestracja',$message,'From: pehaptest@gmail.com');
    }
}
}
echo '<form id="signup" method="post">';

if(isset($_SESSION['rejestracja_ok']))
{
  echo '<p style="margin: 0; text-align: center; width: 100%;"><span style="font-size: 20px;" id="green">Pomyślnie zarejestrowano.<br> Potwierdź maila aby odblokować konto! </span></p>';
  echo '<p style=" text-align: center; width: 30%;"><a href="/Adopcja"><button>Powrót</button></a></p>';
  unset($_SESSION['rejestracja_ok']);
}
else {
 echo '<h1>Zarejestruj się:</h1>
 <p>Imię:<br><input type="text" name="fname" placeholder="Imię" value="';
 if(isset($_SESSION['afname']))
 {
   echo $_SESSION['afname'];
   unset($_SESSION['afname']);
 }
  echo '">';
 if(isset($_SESSION['e_fname']))
 {
   echo '<div class="red">'.$_SESSION['e_fname'].'</div>';
   unset($_SESSION['e_fname']);
 }
 echo  '</p><p>Nazwisko:<br><input type="text" name="lname" placeholder="Nazwisko" value="';
 if(isset($_SESSION['alname']))
 {
   echo $_SESSION['alname'];
   unset($_SESSION['alname']);
 }
 echo '">';
 if(isset($_SESSION['e_lname']))
 {
   echo '<div class="red">'.$_SESSION['e_lname'].'</div>';
   unset($_SESSION['e_lname']);
 }
 echo '</p><p>E-mail:<br><input type="email" name="email" placeholder="E-mail" value="';
 if(isset($_SESSION['aemail']))
 {
   echo $_SESSION['aemail'];
   unset($_SESSION['aemail']);
 }
 echo '">';
 if(isset($_SESSION['e_email']))
 {
   echo '<div class="red">'.$_SESSION['e_email'].'</div>';
   unset($_SESSION['e_email']);
 }
 else if(isset($_SESSION['e_emaile']))
 {
   echo '<div class="red">'.$_SESSION['e_emaile'].'</div>';
   unset($_SESSION['e_emaile']);
 }
 echo '</p><p>Login:<br><input type="text" name="login" placeholder="Login" value="';
 if(isset($_SESSION['alogin']))
 {
   echo $_SESSION['alogin'];
   unset($_SESSION['alogin']);
 }
 echo '">';
 if(isset($_SESSION['e_login']))
 {
   echo '<div class="red">'.$_SESSION['e_login'].'</div>';
   unset($_SESSION['e_login']);
 }
 else if(isset($_SESSION['e_logine']))
 {
   echo '<div class="red">'.$_SESSION['e_logine'].'</div>';
   unset($_SESSION['e_logine']);
 }
 echo '</p><p>Hasło:<br><input type="password" name="pass" placeholder="Hasło">';
 if(isset($_SESSION['e_pass']))
 {
   echo '<div class="red">'.$_SESSION['e_pass'].'</div>';
   unset($_SESSION['e_pass']);
 }
 echo '</p><p>Powtórz hasło:<br><input type="password" name="pass1" placeholder="Powtórz Hasło"></p>';
 if(isset($_SESSION['e_pass1']))
 {
   echo '<div class="red">'.$_SESSION['e_pass1'].'</div>';
   unset($_SESSION['e_pass1']);
 }
 echo '<p style="line-height: 1; margin-top: 5px;">Akceptacja <a href="#">regulaminu</a> <br><input type="checkbox" name="accept"></p>';
 if(isset($_SESSION['e_regulamin']))
 {
   echo '<div class="red">'.$_SESSION['e_regulamin'].'</div>';
   unset($_SESSION['e_regulamin']);
 }
 echo '<div class="buttons"><button type="submit">Zarejestruj</button>';
 echo '<a href="Adopcja"><button type="button">Powrót</button></a></div>';
}
echo '</form>';
$sql -> close();
?>
</div>
<div id="footer">
<h3>Zaadoptuj zwierzaka już dziś!</h3>
</div>
</body>
</html>
