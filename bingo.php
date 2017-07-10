<?php
session_start();
  //require_once 'lutrija_db.php';

  if(!isset($_SESSION))
     {
         session_start();
           $_SESSION['ukupno']=0;
     }
  if(!isset($_SESSION['korisnik']))
  $_SESSION['korisnik']='';

if(isset($_POST['broj']) && !isset($_POST['potvrdi']) && !isset($_POST['ponisti']))
  {
    if($_SESSION['korisnik']!=='' )
    {

      $_SESSION['oznaceni'][(int)$_POST['broj']-1] =1;
      array_push($_SESSION['kombinacija'],$_POST['broj'] );
      $_SESSION['ukupno']=(int)$_SESSION['ukupno']+1;

      if((int)$_SESSION['ukupno']===15)
      {
        $_SESSION['uvjet'] = 1;
        echo '<script language="javascript" type="text/javascript">
                      alert("Unijeli ste sve brojeve. Potvrdite unos ili ga poništite.");
              </script>';
      }
      else
      {
          $_SESSION['uvjet'] = 0;
      }

    }
    else {
      echo '<script language="javascript" type="text/javascript">
                    alert("Molimo, ulogirajte se kako biste nastavili igrati");
            </script>';
      $_SESSION['oznaceni'][(int)$_POST['broj']]=array();
    }


  }
  if(isset($_POST['potvrdi']))
  {
    if($_SESSION['korisnik']!=='')
    {
      if($_SESSION['ukupno']===15)
      {
            $db = new PDO('mysql:host=rp2.studenti.math.hr; dbname=martinek; charset=utf8', 'student', 'pass.mysql');
            $niz= implode(" ",$_SESSION['kombinacija']);
            $kontrolni_broj = rand(10000, 99999);




            try
            {
              $st = $db->prepare( "INSERT INTO Bingo(nadimak, kontr_broj, brojevi, izvucen, proslost ) VALUES (:nad,:kb,:br,'0', '0')" );

              $st->execute( array( 'nad' => $_SESSION['korisnik'],
                                    'kb' => $kontrolni_broj,
                                    'br' => $niz ) );
            }
            catch( PDOException $e ) { exit( 'Greška u bazi: ' . $e->getMessage() ); }


            try
            {
              $st = $db->prepare( 'SELECT uplata FROM Igraci WHERE nadimak= :nad' );
              $st->execute( array( 'nad' =>$_SESSION['korisnik'] ) );
            }
            catch( PDOException $e ) { echo 'Greška:' . $e->getMessage() ; return; }
          	foreach ($st->fetchAll() as $row)
            {
          		$uplata=$row['uplata'];
          	}
          	$ostatak= $uplata-10;
            $naziv=$_SESSION['korisnik'];

            if($ostatak>=0)
            {


              $st = $db->exec( "UPDATE Igraci SET uplata = '$ostatak' WHERE nadimak ='$naziv' " );

              $poruka= "Uspješno ste uplatili"."\\nKorisnik: " .$_SESSION['korisnik']."\\nKombinacija: " .$niz."\\nVaš kontrolni broj:" . $kontrolni_broj."\\nNa računu imate još: ".$ostatak." kuna." . "\\nPRATITE IZVLAČENJE!";


          echo '<script type="text/javascript">alert("'.$poruka.'");</script>';
            }
            else {
              echo '<script language="javascript" type="text/javascript">
                            alert("Nemate dovoljno novaca na računu!");
                    </script>';
            }



      }
    else {
      echo '<script language="javascript" type="text/javascript">
                    alert("Niste unijeli 15 brojeva.");
            </script>';
    }
  }
    else {
      echo '<script language="javascript" type="text/javascript">
                    alert("Molimo, ulogirajte se kako biste mogli nastaviti igrati.");
            </script>';
    }
  }


  if(isset($_POST['ponisti']))
  {
      $_SESSION['uvjet'] = 0;
       $_SESSION['ukupno']=0;
       $_SESSION['kombinacija']=array();
       $_SESSION['oznaceni']=array();
       for($i=0;$i<50; $i++)
       {
           $_SESSION['oznaceni'][$i]=0;
       }
    header('Location: bingo.php');
    exit(1);
  }




  if(isset($_POST['odi_na_pocetak']))
  {
    header('Location: index.php');
    exit;
  }


 ?>



 <!DOCTYPE html>
 <html>
   <head>
     <meta charset="utf-8">
     <title></title>
       <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.js"></script>
     <style>

     div.unos
     {
       border-color: black;
       height:250px;
       width:400px;
       text-align:center;
       float: right;
       background-color:#FFEBCD;
       position:absolute;

        border: 1px solid black;
        border-radius: 5px;
        top:40%;
        right:40%;
     }

     aside {
     		float: right;
     		text-align:center;
     	}
      .buttonstyle
      {
      height: 40px;
      width: 60px;
      padding-bottom: 2px;
      }
     </style>
   </head>
   <body>
     <aside>
     <img src="bingo.jpg" alt="bingo" >
      </aside>
      <h1  style="font-family:courier;">Dobro došli u igru BINGO!</h1>
      <p  style="font-family:courier;">Odaberite 15 od 50 brojeva za jednu kombinaciju!</p>
     <table border="1px solid black">
       <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
     <?php
     $id=0;
     for($i=0; $i<10; $i++)
     {?><tr>
       <?php
       for($j=0; $j<5; $j++)
       {
         $id=$id+1;

         ?>

        <td><input type="submit" class="buttonstyle" name="broj" value="<?php echo $id?>" id="<?php echo $id?>"
          style="background-color: <?php if($_SESSION['oznaceni'][$id-1]===1) echo "#DC143C"; else echo "#FFEBCD";?>" <?php if ($_SESSION['oznaceni'][$id-1]===1){ ?> disabled <?php   } ?>/> </td>

          <?php

       }
       ?></tr>
       <?php
     }

      ?>
      <div class="unos">
      </br>
        <p>Kombinacija: <?php
        if(!empty($_SESSION['kombinacija']))
          for($i=0; $i<sizeof($_SESSION['kombinacija']); $i++)
            echo " " .$_SESSION['kombinacija'][$i]. " ";?></p>

        </br>

      <button type="submit" name="ponisti" id="ponisti">Poništi unos</button>
    </br>
      <button type="submit" name="potvrdi" id="potvrdi">Potvrdi unos</button>
    </br>
    <input  style=" height: 40px; width: 160px;" type="submit" name="odi_na_pocetak" value="Vrati se na početak!"  />
        </div>

    </form>

   </body>
 </html>
