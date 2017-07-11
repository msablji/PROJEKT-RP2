<!doctype html>
<head>
	<meta charset="utf8">

</head>
<body>
<?php
session_start();
$db = new PDO( 'mysql:host=rp2.studenti.math.hr;dbname=martinek;charset=utf8','student','pass.mysql' );
$st = $db->prepare( 'SELECT * from Igraci');
$st->execute();
?><h1>Odaberi što želiš urediti:</h1>
<?php
$boja="#ffff99";
echo '<form action="profil.php" method="post">'.'<tr><td>'.'<button type="submit" name="'."uredi".'"  style=" height: 50px; width: 100px; background-color:'.$boja.';">'."Uredi profil".'</button>'.'</td></tr>'.'</form>';
echo ' <aside class="dropdown">
  <button onclick="myFunction()" class="dropbtn" style=" height: 50px; width: 100px; background-color:'.$boja.';">Pogledaj listiće:</button>
  <aside id="myDropdown" class="dropdown-content">'.
    '<form action="profil.php" method="post">'.'<tr><td>'.'<button type="submit" name="loto"  style=" height: 50px; width: 100px; background-color:'.$boja.';">Loto</button>
    <form action="profil.php" method="post">'.'<tr><td>'.'<button type="submit" name="klad"  style=" height: 50px; width: 100px; background-color:'.$boja.';">Kladionica</button>
  <form action="profil.php" method="post">'.'<tr><td>'.'<button type="submit" name="bingo"  style=" height: 50px; width: 100px; background-color:'.$boja.';">Bingo</button>
  <form action="profil.php" method="post">'.'<tr><td>'.'<button type="submit" name="euro"  style=" height: 50px; width: 100px; background-color:'.$boja.';">Eurojack</button></form>

  </aside>
</aside> ';

if(isset($_POST['uredi'])){?>
<div>
<?php while( $row = $st->fetch() )
{
	if($row['nadimak']===$_SESSION['korisnik'])
	{?>
<form method="post" action="profil.php">
	Ime korisnika:
	<input type="text" name="ime" value="<?php echo $row['ime']; ?>" id="ime"></br>
	Prezime korisnika:
	<input type="text" name="prezime" value="<?php echo $row['prezime']; ?>" id="prezime"></br>
	Nadimak korisnika:
	<input type="text" name="nadimak" value="<?php echo $row['nadimak']; ?>" id="nadimak"></br>
	Račun korisnika:
	<input type="text" name="racun" value="<?php echo $row['racun']; ?>" id="lokacija"></br>
	Kontrolni broj računa korisnika:
	<input type="text" name="kontrol" value="<?php echo $row['kontr_br']; ?>"></br>
	Email korisnika:
	<input type="text" name="mail" value="<?php echo $row['email']; ?>"></br>
	OIB korisnika:
	<input type="text" name="oib" value="<?php echo $row['OIB']; ?>"></br>
	Password korisnika:
	<input type="text" name="pass" value="<?php echo $row['lozinka']; ?>"></br>
	Stanje računa:
	<input type="text" name="upl" value="<?php echo $row['uplata']; ?>"></br>

	<input type="submit" name="uredi2" value="Uredi" id="sub" style=" height: 50px; width: 100px;">

</form></div>
<?php
}
}
}
if(isset($_POST['uredi2']))
{
	try
        {
			$niz = '';
    		for( $i = 0; $i < 20; ++$i )
    			$niz .= chr( rand(0, 25) + ord( 'a' ) );

        $hash=password_hash($_POST['pass'], PASSWORD_DEFAULT);
          $st = $db->prepare( 'UPDATE `Igraci` SET `ime`=:im,`prezime`=:pr,`nadimak`=:nad,`lozinka`=:loz,`OIB`=:oib,`email`=:em,`racun`=:rac,`kontr_br`=:kontr,`uplata`=:upl,`niz`=:ni WHERE nadimak="'.$_SESSION['korisnik'].'"' );

          $st->execute( array( 'im' => $_POST['ime'],
                                'pr' => $_POST['prezime'],
                                'nad' => $_POST['nadimak'],
                                'loz' => $hash,
                                'em' => $_POST['mail'],
                                'oib' => $_POST['oib'],
                              'rac' => $_POST['racun'],
                             'kontr' => $_POST['kontrol'],
							 'upl' => $_POST['upl'],
                             'ni'  => $niz ) );
        }
        catch( PDOException $e ) { exit( 'Greška u bazi: ' . $e->getMessage() ); }

        echo '<script type="text/javascript">alert("Promijenili ste svoje podatke!");</script>';

}

if(isset($_POST['bingo']))
{

	?>
	<article class="a"><table>
<?php	$db = new PDO( 'mysql:host=rp2.studenti.math.hr;dbname=martinek;charset=utf8','student','pass.mysql' );
$st = $db->prepare( 'SELECT * from Bingo');
$st->execute();
while( $row = $st->fetch() )
{
	if($row['nadimak']===$_SESSION['korisnik'])
	{
	echo '<tr><td>'."Bingo:".'</td><td > '.$row['brojevi']; if($row['izvucen']!=0) echo '  </td><td>'."DOBITAN!".'</td></tr>';
		 else echo '  </td><td>  '." Nije".'</td></tr>';
    }

}
?>	</table></article><?php

}
if(isset($_POST['loto']))
{

	?>
	<article class="a"><table>
<?php	$db = new PDO( 'mysql:host=rp2.studenti.math.hr;dbname=martinek;charset=utf8','student','pass.mysql' );
$st = $db->prepare( 'SELECT * from Loto');
$st->execute();
while( $row = $st->fetch() )
{
	if($row['korisnicko_ime']===$_SESSION['korisnik'])
	{
	echo '<tr><td>'."Loto:".'</td><td class="br"> '.$row['kombinacija'] .' </td><td>'.$row['slovo']; if($row['dobitan']!=0) echo '  </td><td>'."DOBITAN!".'</td></tr>';
		 else echo '  </td><td>  '." Nije".'</td></tr>';
    }

}
?>	</table></article><?php

}
if(isset($_POST['euro']))
{

	?>
	<article class="a"><table>
<?php	$db = new PDO( 'mysql:host=rp2.studenti.math.hr;dbname=martinek;charset=utf8','student','pass.mysql' );
$st = $db->prepare( 'SELECT * from Euro');
$st->execute();
while( $row = $st->fetch() )
{
	if($row['korisnicko_ime']===$_SESSION['korisnik'])
	{
	echo '<tr><td>'."Eurojack:".'</td><td class="br"> '.$row['kombinacija'] .' </td><td>'.$row['dopunski']; if($row['dobitan']!=0) echo '  </td><td>'."DOBITAN!".'</td></tr>';
		 else echo '  </td><td>  '." Nije".'</td></tr>';
    }

}
?>	</table></article><?php

}
if(isset($_POST['klad']))
{

	?>
	<article class="a"><table>
<?php	$db = new PDO( 'mysql:host=rp2.studenti.math.hr;dbname=martinek;charset=utf8','student','pass.mysql' );
$st = $db->prepare( 'SELECT * from Kladionica');
$st->execute();
while( $row = $st->fetch() )
{
	if($row['nadimak']===$_SESSION['korisnik'])
	{
	echo '<tr><td>'."Klađenje:".'</td><td class="br"> '.$row['konj'] .' </td><td>'."Mogući dobitak:".$row['dobitak']; if($row['dobio']!=0) echo '  </td><td>'."DOBITAN!".'</td></tr>';
		 else echo '  </td><td>  '." Nije".'</td></tr>';
    }

}
?>	</table></article><?php

}
if(isset($_POST['izadi']))
{
	header('Location: index.php');
  exit;
}


?>
<style>
.dropdown {
    position: relative;
    display: inline-block;
}

.dropdown-content {
    display: none;
    position: absolute;

    min-width: 160px;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    padding: 15px 20px;
    z-index: 1;
}

.dropdown:hover .dropdown-content {
    display: block;
}
table, th, td {
    border: 1px solid black;
	height:50px;
	background-color:  #ffffcc;
}
td.br{
	width:300px;
}
div{

        border-color: black;
        height:400px;
		width: 550px;
        text-align:left;
        border: 1px solid black;
        border-radius: 5px;
        position: absolute;
        top: 100px;
        left: 20%;
		background-color:  #ffffcc;
		font-size: 150%;
	}
article.a{


        height:400px;
		width: 550px;
        text-align:left;
         border-radius: 5px;
        position: absolute;
        top: 100px;
        left: 20%;
		font-size: 150%;
	}

</style>
</body>
<form method="post" action="uredi_korisnik.php">
</br></br></br><input type="submit" name="izadi" value="IZADI!" id="sub" style=" height: 50px; width: 100px;"></form>
</html>
