<?php
session_start();
  $_SESSION['page']--;
  $_SESSION['results']-=18;
  header('Location: /Adopcja');
 ?>
