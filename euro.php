<?php
session_start();


if(isset($_POST['euro']) || isset($_POST["restartaj"]))
{
	pocni();
}


function pocni()
{
  $_SESSION['jesmo_gotovi']=0;
  $_SESSION['jesmo_gotovi_dodatno']=0;
  $_SESSION['stisli']=array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
  $_SESSION['stisli1']=array(0,0,0,0,0,0,0,0,0,0);
  $_SESSION['koji_su'] = array(0,0,0,0,0);
  $_SESSION['dodatni1'] = array(0,0);
}

if(isset($_POST['odi_na_pocetak']))
{
  header('Location: index.php');
  exit;
}

if(isset($_POST['broj1']))
{
$br=$_POST['broj1'];

for( $i = 1; $i <= 50; ++$i )
{

if($i == $br)
{
if($_SESSION['jesmo_gotovi'] < 5 && $_SESSION['stisli'][$br-1]==0 )
{
	$_SESSION['jesmo_gotovi']++;
    $_SESSION['stisli'][$br-1]=1;
	$_SESSION['koji_su'][$_SESSION['jesmo_gotovi']-1]=$br;
}
else if($_SESSION['stisli'][$br-1]==1)
{
	foreach ($_SESSION['koji_su'] as $key => $value){
    if ($value == $br) {
        $_SESSION['koji_su'][$key]=$_SESSION['koji_su'][$_SESSION['jesmo_gotovi']-1];
		$_SESSION['koji_su'][$_SESSION['jesmo_gotovi']-1]=0;
    }
}
$_SESSION['stisli'][$br-1]=0;
$_SESSION['jesmo_gotovi']--;
}

else
{echo '<script language="javascript" type="text/javascript">
                    alert("Unijeli ste sve brojeve.Sada odaberite dopunske brojeve!!");

            </script>';
}}
}

}

if(isset($_POST['broj']))
{
$br=$_POST['broj'];

for( $i = 1; $i <= 10; ++$i )
{

if($i == $br)
{
if($_SESSION['jesmo_gotovi_dodatno'] < 2 && $_SESSION['stisli1'][$br-1]==0 )
{
	$_SESSION['jesmo_gotovi_dodatno']++;
    $_SESSION['stisli1'][$br-1]=1;
	$_SESSION['dodatni1'][$_SESSION['jesmo_gotovi_dodatno']-1]=$br;
}
else if($_SESSION['stisli1'][$br-1]==1)
{
	foreach ($_SESSION['dodatni1'] as $key => $value){
    if ($value == $br) {
        $_SESSION['dodatni1'][$key]=$_SESSION['dodatni1'][$_SESSION['jesmo_gotovi_dodatno']-1];
		$_SESSION['dodatni1'][$_SESSION['jesmo_gotovi_dodatno']-1]=0;
    }
}
$_SESSION['stisli1'][$br-1]=0;
$_SESSION['jesmo_gotovi_dodatno']--;
}

else
{echo '<script language="javascript" type="text/javascript">
                    alert("Unijeli ste sve brojeve. Potvrdite unos.");

            </script>';
}}
}

}


if(isset($_POST['klikni_na2']))
{
      if($_SESSION['jesmo_gotovi']===5 && $_SESSION['jesmo_gotovi_dodatno']===2)
      {
		   $db = new PDO('mysql:host=rp2.studenti.math.hr; dbname=martinek; charset=utf8', 'student', 'pass.mysql');
			 			sort($_SESSION['koji_su']);
						sort($_SESSION['dodatni1']);
            $niz_je1= implode(" ",$_SESSION['koji_su']);
						$niz_je2= implode(" ",$_SESSION['dodatni1']);
            $kontrolni_broj1 = rand(10000, 99999);




            try
            {

              $st = $db->prepare( "INSERT INTO Euro(kombinacija, dopunski, id, korisnicko_ime,proslo,dobitan, polu_dobitak ) VALUES (:br,:br2,:kb,:nad,'0', '0', '0')" );

              $st->execute( array( 'nad' => $_SESSION['korisnik'],
                                    'kb' => $kontrolni_broj1,
                                    'br' => $niz_je1,
									'br2' => $niz_je2 ) );
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
          	$ostatak= $uplata-15;
            $naziv=$_SESSION['korisnik'];

            if($ostatak>=0)
            {

              $st = $db->exec( "UPDATE Igraci SET uplata = '$ostatak' WHERE nadimak ='$naziv' " );

              $poruka= "Uspješno ste uplatili listić!  "."\\n* Korisnik: " .$_SESSION['korisnik']."\\n* Kombinacija: " .$niz_je1."+ " .$niz_je2."\\n* Vaš kontrolni broj:" .$kontrolni_broj1."\\n* Na računu imate još: ".$ostatak." kuna." . "\\n* PRATITE IZVLAČENJE!";


          echo '<script type="text/javascript">alert("'.$poruka.'");</script>';
		  $_SESSION['iznos_u_kn']=$ostatak;
		  pocni();

            }
            else {
              echo '<script language="javascript" type="text/javascript">
                            alert("Nemate dovoljno novaca na računu!");
                    </script>';
            }




      }
    else {
      echo '<script language="javascript" type="text/javascript">
                    alert("Niste unijeli 7 brojeva. 5 osnovnih i 2 dopunska");
            </script>';
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
		width:33%;
		float: right;
		background-color:#FFEBCD;
}
.pozicija{
	    width:50%;
		float: right;
		height:250px;
        text-align:center;
}
aside {
		float: right;
		text-align:center;
	}

    </style>
<aside>
<img src="eurojack.jpg" alt="slika" >
 </aside>
<h1  style="font-family:courier;">Dobro došli u igru EuroJackpot!</h1>
<p  style="font-family:courier;">Odaberite 5+2 broja za jednu kombinaciju</p>

<div>
  <h2 style="font-family:courier;">ODABRANA JE KOMBINACIJA:</h2>
  <p style="color:#DC143C; font-size:250%;"><?php
$brojimo2=0;
  foreach ($_SESSION['koji_su'] as $key => $value) {
    if($value!=0) {
		$brojimo2++;
		if($brojimo2!==5)
		{
			echo $value." , ";
		}
		else echo $value;
	}
}$brojimo2=0; ?></p>
<h3>Dopunski brojevi su:</h3><p style="color:#DC143C;font-size:150%;">
<?php
  foreach ($_SESSION['dodatni1'] as $key1 => $value1) {
    if($value1!=0){
		$brojimo2++;
		if($brojimo2!==2)
		{
			echo $value1." , ";
		}
		else echo $value1;
	}
}?>
  </p>
</div>

    <form action="euro.php" method="post">
    <table>
    <?php
    $brojac=1;
    for( $j = 0; $j <5; ++$j )
    {?>
    <tr>
    <?php
    for( $i =0; $i <10; ++$i ){
      ?> <th><button class="buttonstyle" type="submit" value="<?php echo $brojac; ?>" name="broj1" style="background-color: <?php if($_SESSION['stisli'][$brojac-1]===1) echo "#DC143C"; else echo "#FFEBCD";?> "><?php echo $brojac; ?></button></th>


    <?php $brojac++; } ?>
    </tr>
    <?php }
    ?> </br>
	<?php
	$br2=1;
    for( $i =0; $i <10; ++$i ){
      ?> <th><button class="buttonstyle" type="submit" value="<?php echo $br2; ?>" name="broj" style="background-color: <?php if($_SESSION['stisli1'][$br2-1]===1) echo "#DC143C"; else echo "gray";?> "><?php echo $br2; ?></button></th>


    <?php $br2++; } ?>

  </table></br></br>
<input style=" height: 40px; width: 110px;" type="submit" name="klikni_na2"  value="Uplati" />
    <input  style=" height: 40px; width: 110px;" type="submit" name="restartaj" value="Restartaj!"  />
	<input  style=" height: 40px; width: 160px;" type="submit" name="odi_na_pocetak" value="Vrati se na početak!"  />


    </form>


    </body>
    </html>
