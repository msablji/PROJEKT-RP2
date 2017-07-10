<?php

//session_start();
  require_once 'lutrija_db.php';




  if(!isset($_SESSION))
     {
         session_start();

     }
if(isset($_POST['potvrdi']) && $_SESSION['ukupno']===15)
{
  $niz='';
  if(!empty($_SESSION['kombinacija']))
    for($i=0; $i<sizeof($_SESSION['kombinacija']); $i++)
      $niz=$niz.$_SESSION['kombinacija'][$i]. ",";

      $kontrolni_broj = rand(10000, 99999);
      $db=DB::getConnection();



      try
      {
        $st = $db->prepare( "INSERT INTO Bingo(nadimak, kontr_broj, brojevi, izvucen ) VALUES (:nad,:kb,:br,'0')" );

        $st->execute( array( 'nad' => $_SESSION['korisnik'],
                              'kb' => $kontrolni_broj,
                              'br' => $niz ) );
      }
      catch( PDOException $e ) { exit( 'Greška u bazi: ' . $e->getMessage() ); }
      echo "Uspješno ste uplatili";
}

 ?>
