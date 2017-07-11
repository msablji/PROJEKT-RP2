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
?><h1>Odredi korisnika kojeg želiš urediti:</h1>
<?php
while( $row = $st->fetch() )
{
	if($row['ime']!=='admin')
	{
		$boja="#ffffcc";
	}
	else $boja="#ffff99";
		echo '<form action="uredi_korisnik.php" method="post">'.'<tr><td>'.'<button type="submit" name="'.$row['ime'].'"  style=" height: 50px; width: 100px; background-color:'.$boja.';">'.$row['ime'].'</button>'.'</td></tr>'.'</form>';
	$im=$row['ime'];
	
 	
	
if(isset($_POST[$im])){?>
<div>	
<form method="post" action="uredi_korisnik.php">
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
	
	
	
	<input type="hidden" name="nadimak_korisnika" value="<?php echo $row["nadimak"]; ?>" >
	<input type="submit" name="uredi" value="Uredi" id="sub" style=" height: 50px; width: 100px;">
	
	<input type="submit" value="izbrisi" name="izbrisi" style=" height: 50px; width: 100px;">
</form></div>
<?php	
}

}

if(isset($_POST['dodaj']))
{
	?>
	<div>	
<form method="post" action="uredi_korisnik.php">
	Ime korisnika:
	<input type="text" name="ime2" ></br>
	Prezime korisnika:
	<input type="text" name="prezime2" ></br>
	Nadimak korisnika:
	<input type="text" name="nadimak2" ></br>
	Račun korisnika:
	<input type="text" name="racun2" ></br>
	Kontrolni broj računa korisnika:
	<input type="text" name="kontrol2"></br>
	Email korisnika:
	<input type="text" name="mail2"></br>
	OIB korisnika:
	<input type="text" name="oib2" ></br>
	Password korisnika:
	<input type="text" name="pass2" ></br>
	Stanje računa:
	<input type="text" name="upl2" ></br>
	
	<input type="submit" name="dodaj_ga" value="Dodaj korisnika" id="sub" style=" height: 50px; width: 100px;">
	
	
</form></div>
<?php	}
if(isset($_POST['dodaj_ga']))
{


        try
        {
			$niz = '';
    		for( $i = 0; $i < 20; ++$i )
    			$niz .= chr( rand(0, 25) + ord( 'a' ) );

        $hash=password_hash($_POST['pass2'], PASSWORD_DEFAULT);
          $st = $db->prepare( 'INSERT INTO Igraci(ime,prezime, nadimak, lozinka, email,oib, racun, kontr_br,uplata, niz, kliknuo, admin ) VALUES ' .
                            '(:im,:pr,:nad, :loz, :em,:oib, :rac,:kontr,:upl, :ni, 1, 0)' );

          $st->execute( array( 'im' => $_POST['ime2'],
                                'pr' => $_POST['prezime2'],
                                'nad' => $_POST['nadimak2'],
                                'loz' => $hash,
                                'em' => $_POST['mail2'],
                                'oib' => $_POST['oib2'],
                              'rac' => $_POST['racun2'],
                             'kontr' => $_POST['kontrol2'],
							 'upl' => $_POST['upl2'],
                             'ni'  => $niz ) );
        }
        catch( PDOException $e ) { exit( 'Greška u bazi: ' . $e->getMessage() ); }

        echo '<script type="text/javascript">alert("Dodali ste ubazu novog korisnika.");</script>';
}

if(isset($_POST['uredi']))
{
	try
        {
			$niz = '';
    		for( $i = 0; $i < 20; ++$i )
    			$niz .= chr( rand(0, 25) + ord( 'a' ) );

        $hash=password_hash($_POST['pass'], PASSWORD_DEFAULT);
          $st = $db->prepare( 'UPDATE `Igraci` SET `ime`=:im,`prezime`=:pr,`nadimak`=:nad,`lozinka`=:loz,`OIB`=:oib,`email`=:em,`racun`=:rac,`kontr_br`=:kontr,`uplata`=:upl,`niz`=:ni WHERE nadimak="'.$_POST['nadimak_korisnika'].'"' );

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

        echo '<script type="text/javascript">alert("Promijenili ste podatke korisnika!");</script>';
	
}
if(isset($_POST['izadi']))
{
	header('Location: index.php');
  exit;
}

if(isset($_POST['izbrisi']))
{
	echo '<script type="text/javascript">var ret = confirm("Jeste sigurni da želite izbaciti korisnika iz baze podataka? Ukoliko to napravite, taj korisnik više ne postoji!");
	</script>';
	
	echo "<script>if(ret===true){</script>";
	$st = $db->prepare( 'DELETE FROM `Igraci` WHERE `nadimak`="'.$_POST['nadimak_korisnika'].'" ' );

	$st->execute(); 
	echo "<script>}</script>";
}
?>
<style>
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
</style>
</body>
<form method="post" action="uredi_korisnik.php">
</br></br></br><input type="submit" name="izadi" value="IZADI!" id="sub" style=" height: 50px; width: 100px;">
<input type="submit" name="dodaj" value="Dodaj" id="sub" style=" height: 50px; width: 100px;"></form>
</html> 

