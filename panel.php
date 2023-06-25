<?php
    session_start();
    if(!(isset($_SESSION['logged'])))
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
    <title>Panel użytkownika</title>
</head>
<body>

<div id="banner" style="position: static;">
  <i><h2>Panel użytkownika</h2></i>
  <a href="/Adopcja/Wyloguj"><button type="button">Wyloguj</button></a>
<a href="/Adopcja"><button type="button">Powrót</button></a>
      <?php
      echo '<p>Witaj '.$_SESSION['Imię'].'!</p>';
      ?>
</div>
<div id="container" style="height: auto;">
<div id="left">
  <a href="Panel?id=1"><button><p>Ogłoszenia</p></button></a>
  <a href="Panel?id=2"><button><p>Dane osobowe</p></button></a>
  <a href="Panel?id=4"><button><p>Newsletter</p></button></a>
  <a href="Panel?id=5"><button><p>Konto</p></button></a>
</div>
<div id="right">
<?php
  if(!(isset($_GET['id'])))
  {
    $id=1;
  }
  else
  {
    $id = $_GET['id'];
  }
  switch($id)
  {
    case 1: {
      echo '
      <h1> DODAJ OGŁOSZENIE </h1>
      <form method="post" enctype="multipart/form-data">
    <p>  Imię zwierzaka: <input type="text" name="name" required> 
    Typ: <select name="typ" required>
    <option>Pies</option>
    <option>Kot</option>
    </select>
    Płeć: <select name="sex" required> <option>Samiec</option> <option>Samica</option></select>
     Wiek: <input type="number" name="year" required>
      </p>
    <p> Opis:</p><p> <textarea name="desc" rows="5" cols="180" maxlength="1024" required> </textarea>
    </p>
    <p>  Zdjęcie zwierzaka: <input type="file" name="img" required> </p>';
    if(isset($_SESSION['img_e']))
    {
      echo '<span class="red">'.$_SESSION['img_e'].'</span>';
      unset($_SESSION['img_e']);
    }
    echo '<h5> Dane wstawiającego ogłoszenie: </h5>
    <p>  Telefon: <input type="tel" name="tel" required> 
   E-mail: <input type="email" name="email" required> </p>
    <p> <input type="reset"> <input type="submit">
      </form>
      <h1> Moje ogłoszenia </h1>
      ';
      require_once "php/dbconnect.php";
      $sql = @new mysqli($addr,$user,$nowe,$db);
      mysqli_report(MYSQLI_REPORT_STRICT);
      if ($sql -> connect_errno) 
      {
        echo "Failed to connect to MySQL: ";
        exit();
      }
      $zapyt= $sql -> query("SELECT * FROM zwierzeta WHERE AuthorId=".$_SESSION['UserId'].";");
      if($zapyt -> num_rows > 0)
      {
      while($l = $zapyt ->fetch_assoc())
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
    }
    else
    {
       echo '<p><h3>Świeci tutaj pustkami</p><p>Dodaj swoje pierwsze ogłoszenie!</p></h3>';
    }
    
      if(isset($_POST['name']))
      {

      $name=$_POST['name'];
      $typ=$_POST['typ'];
      $seks=$_POST['sex'];
      $year=$_POST['year'];
      $desc=$_POST['desc'];
      $tel=$_POST['tel']; 
      $email=$_POST['email'];
      if($_FILES['img']['size'] > 2000000097152)
      {
        $_SESSION['img_e'] = 'Plik większy niż 2 MB';
      }
      else if($_FILES['img']['type'] != 'image/jpg' && $_FILES['img']['type'] != 'image/png' && $_FILES['img']['type'] != 'image/gif')
      {
        $_SESSION['img_e'] = 'Plik obsługuje formaty png, jpg, gif.';
      }
      else
      {
 
        $type = explode("/",$_FILES['img']['type'] );
        $date = date('Y-m-d');
        $user = $_SESSION['UserId'];
        $sql -> query("Insert into zwierzeta 
        (Name,Type,Sex,Age,Date,Description,Tel,OwnEmail,Active,Source,AuthorId) 
        values 
        ('$name','$typ','$seks', $year,'$date','$desc',$tel,'$email',1,'',$user);");
        $q = $sql -> query("SELECT MAX(Id) FROM zwierzeta;");
        $l= $q -> fetch_row();
        $numer=$l[0];
        $photo = $numer.".".$type[1];
        move_uploaded_file($_FILES['img']['tmp_name'],'img/zwierzeta/'.$photo);
        $sql -> query("UPDATE zwierzeta SET Source = '$photo' WHERE id=$numer;");
        unset($name,$typ,$seks,$year,$date,$desc,$tel,$email,$photo,$user);
        header("Location: /Adopcja/Ogloszenie?id=".$numer);
      }
    }
      break;
  }
    case 2: {
      require_once "php/dbconnect.php";
      $sql = @new mysqli($addr,$user,$pass,$db);
      mysqli_report(MYSQLI_REPORT_STRICT);
      if ($sql -> connect_errno) 
      {
        echo "Failed to connect to MySQL: ";
        exit();
      }
      $u = $sql -> query("SELECT Imię,Nazwisko,Email,Telephone FROM users WHERE UserId=".$_SESSION['UserId'].";");
      $l = $u -> fetch_row();
      $e=$l[2];
     echo ' <form id="edit" method="post">
     <h1>Edytuj swoje dane: </h1>
      <p>  Imię: <input type="text" name="name" required value="'.$l[0].'"> 
       Nazwisko: <input type="text" name="lname" required value="'.$l[1].'">
    Telefon: <input type="tel" name="tel" required value="'.$l[3].'"> 
     E-mail: <input type="email" name="email" required value="'.$l[2].'"> </p>
      <p> <input type="submit" value="Zmień"> <input type="reset"> <div class="cls"></div></form>';
      if(isset($_POST['name']))
      {
        $wszystkook=true;
        $name = $_POST['name'];
        $lname = $_POST['lname'];
        $tel = $_POST['tel'];
        $email = $_POST['email'];
        if(!(filter_var($email,FILTER_VALIDATE_EMAIL)))
        {
          $wszystkook = false;
          echo "<p class=\"red\">Niepoprawny email!</p>";
        }
        else if(strlen($lname) < 3 || strlen($name) < 3)
        {
          $wszystkook = false; 
          echo "<p class=\"red\">Podane Imię lub Nazwisko jest zbyt krótkie.</p>";
        }
        if($wszystkook)
        {
          $sql -> query("UPDATE newsletter SET Email='$email' WHERE Email='$e';");
        $sql -> query("UPDATE users SET Imię='$name',Nazwisko='$lname',Email='$email',Telephone='$tel' WHERE UserId=".$_SESSION['UserId'].";");
          header('refresh:0');
      }
      }
      break;
    }
    case 4: {
      require_once "php/dbconnect.php";
      $sql = @new mysqli($addr,$user,$pass,$db);
      mysqli_report(MYSQLI_REPORT_STRICT);
      if ($sql -> connect_errno) 
      {
        echo "Failed to connect to MySQL: ";
        exit();
      }
     $z = $sql -> query("SELECT Email FROM newsletter WHERE Email='".$_SESSION['Email']."';");
     if($z -> num_rows > 0)
     {
        echo "<div id=\"wyp\"><h1>Wypisz się z newslettera</h1><br><form method=\"POST\"><input type=\"hidden\" name=\"wyp\"><button type=\"submit\">Wypisz się</button></form></div>";
        if(isset($_POST['wyp']))
        {
          $sql -> query("DELETE FROM newsletter WHERE Email='".$_SESSION['Email']."';");
          header('refresh:1');
        }  
     }
     else
     {
      echo '<div id="wyp">
      <h3>Zapisz się na newsletter!</h3>
      <form method="post">
      <input type="hidden" name="wp">
      <button type="submit"><b>ZAPISZ SIĘ</b></button></div>';
      if(isset($_POST['wp']))
      {
        $email = $_SESSION['Email'];
        if($q = $sql -> query("Insert into newsletter values ('$email');"))
        {
        mail($email,'Schronisko - Newsletter','Dziekujemy za zapisanie się na newsletter!','From: pehaptest@gmail.com');
        }
        header('refresh:1');
     }}
      
    break;
    }
    case 5: {
      echo '
      <h1>Zmień ustawienia konta</h1>
      <form method="POST">
      <p></p>
      <p>Stare hasło: <input type="password" name="oldp" required></p>
      <p>Nowe hasło: <input type="password" name="newp1" required></p>
      <p>Powtórz nowe hasło: <input type="password" name="newp2" required></p>
      <input type="submit" value="Ustaw">
      </form>';
      if(isset($_POST['oldp']))
      {
      $sql = new mysqli("localhost","root","","schronisko");
      $stare1 = $sql ->query("SELECT password from users where UserId=".$_SESSION['UserId'].";");
      $old = $stare1 -> fetch_row();
      $stare=$_POST['oldp'];
      $nowe=$_POST['newp1'];
      if(password_verify($stare,$old[0]))
      {
        $pf=true;
        $nowe=$_POST['newp1'];
        $nowe1=$_POST['newp2'];
        $uppercase = preg_match('@[A-Z]@', $nowe);
        $lowercase = preg_match('@[a-z]@', $nowe);
        $number    = preg_match('@[0-9]@', $nowe);
        $specialChars = preg_match('@[^\w]@', $nowe);
        if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($nowe) < 8 || strlen($nowe) > 32)
        {
            echo '<p></p><p><span class="red">Hasło musi zawierać od 8 do 32 znaków w tym małą i wielką literę oraz znak specjalny.</span></p>';
            $pf = false;
        }
        if($nowe!=$nowe1)
        {
          $pf=false;
          echo '<p></p><p><span class="red">Hasła różnią się od siebie</span></p>';
        }
        if(password_verify($nowe,$old[0]))
        {
          echo '<p></p><p><span class="red">Hasło użyte poprzednio</span></p>';
        }
        else
        {
          echo '<p></p><p><span style="font-size: 20px; margin-left: 2%;" id="green"><b>Hasło zmienione pomyślnie!</b></span></p>';
          $hasz=password_hash($nowe,PASSWORD_BCRYPT);
          $sql -> query("UPDATE users SET password = '$hasz' where UserId=".$_SESSION['UserId'].";");
        }
      }
      else
      {
        echo '<p><span class="red">Złe poprzednie hasło</span></p>';
      }

    }
    break;
    }
    default: 
      {
        echo '
        <h1> DODAJ OGŁOSZENIE </h1>
        <form method="post" enctype="multipart/form-data">
      <p>  Imię zwierzaka: <input type="text" name="name" required> 
      Typ: <select name="typ" required>
      <option>Pies</option>
      <option>Kot</option>
      </select>
      Płeć: <select name="sex" required> <option>Samiec</option> <option>Samica</option></select>
       Wiek: <input type="number" name="year" required>
        </p>
      <p> Opis:</p><p> <textarea name="desc" rows="5" cols="180" maxlength="1024" required> </textarea>
      </p>
      <p>  Zdjęcie zwierzaka: <input type="file" name="img" required> </p>';
      if(isset($_SESSION['img_e']))
      {
        echo '<span class="red">'.$_SESSION['img_e'].'</span>';
        unset($_SESSION['img_e']);
      }
      echo '<h5> Dane wstawiającego ogłoszenie: </h5>
      <p>  Telefon: <input type="tel" name="tel" required> 
     E-mail: <input type="email" name="email" required> </p>
      <p> <input type="reset"> <input type="submit">
        </form>
        <h1> Moje ogłoszenia </h1>
        ';
        require_once "php/dbconnect.php";
        $sql = @new mysqli($addr,$user,$nowe,$db);
        mysqli_report(MYSQLI_REPORT_STRICT);
        if ($sql -> connect_errno) 
        {
          echo "Failed to connect to MySQL: ";
          exit();
        }
        $zapyt= $sql -> query("SELECT * FROM zwierzeta WHERE AuthorId=".$_SESSION['UserId'].";");
        if($zapyt -> num_rows > 0)
        {
        while($l = $zapyt ->fetch_assoc())
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
      }
      else
      {
         echo '<p><h3>Świeci tutaj pustkami</p><p>Dodaj swoje pierwsze ogłoszenie!</p></h3>';
      }
      
        if(isset($_POST['name']))
        {
  
        $name=$_POST['name'];
        $typ=$_POST['typ'];
        $seks=$_POST['sex'];
        $year=$_POST['year'];
        $desc=$_POST['desc'];
        $tel=$_POST['tel']; 
        $email=$_POST['email'];
        if($_FILES['img']['size'] > 2000000097152)
        {
          $_SESSION['img_e'] = 'Plik większy niż 2 MB';
        }
        else if($_FILES['img']['type'] != 'image/jpg' && $_FILES['img']['type'] != 'image/png' && $_FILES['img']['type'] != 'image/gif')
        {
          $_SESSION['img_e'] = 'Plik obsługuje formaty png, jpg, gif.';
        }
        else
        {
   
          $type = explode("/",$_FILES['img']['type'] );
          $date = date('Y-m-d');
          $user = $_SESSION['UserId'];
          $sql -> query("Insert into zwierzeta 
          (Name,Type,Sex,Age,Date,Description,Tel,OwnEmail,Active,Source,AuthorId) 
          values 
          ('$name','$typ','$seks', $year,'$date','$desc',$tel,'$email',1,'',$user);");
          $q = $sql -> query("SELECT MAX(Id) FROM zwierzeta;");
          $l= $q -> fetch_row();
          $numer=$l[0];
          $photo = $numer.".".$type[1];
          move_uploaded_file($_FILES['img']['tmp_name'],'img/zwierzeta/'.$photo);
          $sql -> query("UPDATE zwierzeta SET Source = '$photo' WHERE id=$numer;");
          unset($name,$typ,$seks,$year,$date,$desc,$tel,$email,$photo,$user);
          header("Location: /Adopcja/Ogloszenie?id=".$numer);
        }
      }
        break;
    }
  }
?>
</div>
<div class="cls"></div>
</div>
<?php
if(isset($sql))
{
  $sql -> close();
}
?>
</body>
</html>
