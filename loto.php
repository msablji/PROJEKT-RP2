<?php

session_start();






if(isset($_POST['loto']) || isset($_POST["restartaj"]))
{
	nova_igra_kreni();
}
if(isset($_POST['odi_na_pocetak']))
{
  header('Location: index.php');
  exit;
}


function nova_igra_kreni()
{
  $_SESSION['kraj']=0;
  $_SESSION['kliknuto']=array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0, 0, 0,0);
  $_SESSION['ajde_uplati'] = array(0,0,0,0,0,0,0);
}

if(isset($_POST['klikni_na']))
{
	if(isset($_POST['slovo']))
	{
		$koje_slovo=$_POST['slovo'];
		$koliko_novaca=4;
	}
	else
	{$koje_slovo="";
     $koliko_novaca=3;
     }

      if($_SESSION['kraj']===7)
      {
		   $db = new PDO('mysql:host=rp2.studenti.math.hr; dbname=martinek; charset=utf8', 'student', 'pass.mysql');
			 			sort($_SESSION['ajde_uplati']);
					  $niz_je= implode(" ",$_SESSION['ajde_uplati']);
            $kontrolni_broj2 = rand(10000, 99999);




            try
            {

              $st = $db->prepare( "INSERT INTO Loto(kombinacija, slovo, id, korisnicko_ime,proslo,dobitan, polu_dobitak ) VALUES (:br,:slov,:kb,:nad,'0', '0', '0')" );

              $st->execute( array( 'nad' => $_SESSION['korisnik'],
                                    'kb' => $kontrolni_broj2,
                                    'br' => $niz_je,
                                    'slov'	=> $koje_slovo ) );
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
          	$ostatak= $uplata-$koliko_novaca;
            $naziv=$_SESSION['korisnik'];

            if($ostatak>=0)
            {

              $st = $db->exec( "UPDATE Igraci SET uplata = '$ostatak' WHERE nadimak ='$naziv' " );

              $poruka= "Uspješno ste uplatili listić!  "."\\n* Korisnik: " .$_SESSION['korisnik']."\\n* Kombinacija: " .$niz_je."\\n* Vaš kontrolni broj:" .$kontrolni_broj2."\\n* Na računu imate još: ".$ostatak." kuna." . "\\n* PRATITE IZVLAČENJE!";


          echo '<script type="text/javascript">alert("'.$poruka.'");</script>';
		  $_SESSION['iznos_u_kn']=$ostatak;
		  nova_igra_kreni();

            }
            else {
              echo '<script language="javascript" type="text/javascript">
                            alert("Nemate dovoljno novaca na računu!");
                    </script>';
            }




      }
    else {
      echo '<script language="javascript" type="text/javascript">
                    alert("Niste unijeli 7 brojeva.");
            </script>';
    }


}



if(isset($_POST['broj']))
{
$br=$_POST['broj'];
for( $i = 1; $i <= 39; ++$i )
{

if($i == $br)
{
if($_SESSION['kraj'] < 7 && $_SESSION['kliknuto'][$br-1]==0 )
{
	$_SESSION['kraj']++;
    $_SESSION['kliknuto'][$br-1]=1;
	$_SESSION['ajde_uplati'][$_SESSION['kraj']-1]=$br;
}
else if($_SESSION['kliknuto'][$br-1]==1)
{
	foreach ($_SESSION['ajde_uplati'] as $key => $value){
    if ($value == $br) {
        $_SESSION['ajde_uplati'][$key]=$_SESSION['ajde_uplati'][$_SESSION['kraj']-1];
		$_SESSION['ajde_uplati'][$_SESSION['kraj']-1]=0;
    }
}
$_SESSION['kliknuto'][$br-1]=0;
$_SESSION['kraj']--;
}
else if($_SESSION['kraj'] > 6)
{
	echo '<script language="javascript" type="text/javascript">
                    alert("Unijeli ste sve brojeve. Potvrdite unos.");

            </script>';
}
}

}
}





?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>
  <body>
    <style type="text/css">
  .buttonstyle
  {
  height: 40px;
  width: 60px;
  padding-bottom: 2px;
  }
  div{

        border-color: black;
        height:250px;
        text-align:center;
        border: 1px solid black;
        border-radius: 5px;
		width:50%;
		float: right;
		background-color:#FFEBCD;
}

aside {
		float: right;
		text-align:center;
	}

    </style>
<aside>
<img src="jedan.jpg" alt="slika" >
<form action="loto.php" method="post">
    <h2  style="font-family:courier;">Za super 7 odaberi slovo: </h2>
	<input type="radio" name="slovo" id="A" value="A" />A
	<input type="radio" name="slovo" id="B" value="B" />B</br></br>
   <input style=" height: 40px; width: 110px;" type="submit" name="klikni_na"  value="Uplati" />
    <input  style=" height: 40px; width: 110px;" type="submit" name="restartaj" value="Restartaj!"  />
	<input  style=" height: 40px; width: 160px;" type="submit" name="odi_na_pocetak" value="Vrati se na početak!"  />
</form>
 </aside>
<h1  style="font-family:courier;">Dobro došli u igru 7/39!</h1>
<p  style="font-family:courier;">Odaberite 7 brojeva za jednu kombinaciju</p>

<div>
  <h2 style="font-family:courier;">ODABRANA JE KOMBINACIJA:</h2>
  <p style="color:#DC143C; font-size:300%;"><?php
  $brojimo=0;
  foreach ($_SESSION['ajde_uplati'] as $key => $value) {
    if($value!=0)
	{
		$brojimo++;
		if($brojimo!==7)
		{
			echo $value." , ";
		}
		else echo $value;
	}
}?>
  </p>
</div>

    <form action="loto.php" method="post">
    <table>
    <?php
    $brojac=1;
    for( $j = 1; $j <= 13; ++$j )
    {?>
    <tr>
    <?php
    for( $i =0; $i <=2; ++$i ){
      ?> <th><button class="buttonstyle" type="submit" value="<?php echo $brojac; ?>" name="broj" style="background-color: <?php if($_SESSION['kliknuto'][$brojac-1]===1) echo "#DC143C"; else echo "#FFEBCD";?> "><?php echo $brojac; ?></button></th>


    <?php $brojac++; } ?>
    </tr>
    <?php }
    ?>

  </table></br></br>

    </form>

    </body>
    </html>
