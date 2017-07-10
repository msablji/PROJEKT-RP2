<!doctype html>
<head>
	<meta charset="utf8">
<link rel="stylesheet" type="text/css" href="style.css">
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
	
	
	
	<input type="hidden" name="ime_korisnika" value="<?php echo $row["ime"]; ?>" >
	<input type="submit" name="uredi" value="Uredi" id="sub" style=" height: 50px; width: 100px;">
	
	<input type="submit" value="izbrisi" name="izbrisi" style=" height: 50px; width: 100px;">
</form></div>
<?php	
}

}
if(isset($_POST['uredi']))
{
	try
        {
			$niz = '';
    		for( $i = 0; $i < 20; ++$i )
    			$niz .= chr( rand(0, 25) + ord( 'a' ) );

        $hash=password_hash($_POST['pass'], PASSWORD_DEFAULT);
          $st = $db->prepare( 'UPDATE `Igraci` SET `ime`=:im,`prezime`=:pr,`nadimak`=:nad,`lozinka`=:loz,`OIB`=:oib,`email`=:em,`racun`=:rac,`kontr_br`=:kontr,`uplata`=:upl,`niz`=:ni WHERE ime="'.$_POST['ime_korisnika'].'"' );

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
	
}

/*if(isset($_POST['ime_korisnika'])){
	echo "Uređujemo korisnika: <b>".$_POST['ime_korisnika']."</b>";
}




//promijeni ono što je spremljeno u tablici
if(isset($_POST["uredi"])){
	
	//tu updateamo tablicu sa artiklima
	$st = $db->prepare( 'UPDATE Korisnici SET Ime="'.$_POST["ime"].'", Prezime="'.$_POST["prezime"].'", Nadimak="'.$_POST["nadimak"].'", Lokacija="'.$_POST["lokacija"].'",Password="'.$_POST["pass"].'" WHERE Id='.$_POST["id_korisnika"].' ');
	$st->execute();
	header('Location: uredjivanje_korisnika.php');
	
}


//brisanje korisnika
if(isset($_POST["izbrisi"])){
	$st = $db->prepare('DELETE FROM Korisnici WHERE Id='.$_POST["id_korisnika"].'');
	$st->execute();
	header('Location: uredjivanje_korisnika.php');
}


$st = $db->prepare( 'SELECT * from Korisnici');
$st->execute();
while( $row = $st->fetch() )
{
	if($row['Id']==$_POST['Id_korisnika']) 
	{

?>

<!doctype html>
<head>
	<meta charset="utf8">
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

<form method='post' action='uredjivanje_korisnika.php'>
		Povratak na prethodnu stranicu:
		<input type='submit' name='povratak' value='Povratak'>		
</form>


<form method="post" action="uredi_korisnik.php">
	Ime korisnika:
	<input type="text" name="ime" value="<?php echo $row['Ime']; ?>" id="ime"></br>
	Prezime korisnika:
	<input type="text" name="prezime" value="<?php echo $row['Prezime']; ?>" id="prezime"></br>
	Nadimak korisnika:
	<input type="text" name="nadimak" value="<?php echo $row['Nadimak']; ?>" id="nadimak"></br>
	Lokacija korisnika:
	<input type="text" name="lokacija" value="<?php echo $row['Lokacija']; ?>" id="lokacija"></br>
	Password korisnika:
	<input type="text" name="pass" value="<?php echo $row['Password']; ?>"></br>
	
	
	<input type="hidden" name="id_korisnika" value="<?php echo $row["Id"]; ?>" >
	<input type="submit" name="uredi" value="uredi" id="sub">
	
	<input type="submit" value="izbrisi" name="izbrisi">
</form>

<?php
//zatvaramo php zagrade
}}
?>


<script>

document.getElementById("sub").style.background='#00FFFF';
document.getElementById("sub").disabled = false;

if (document.layers) {
  document.captureEvents(Event.KEYDOWN);
}

document.onkeyup = function (evt) {
	var re = /^([A-Za-z0-9čćšžđŠĐČĆŽ]+)$/;
	var re2 = /^([ A-Za-z0-9čćšžđŠĐČĆŽ,]+)$/;
	var ime = document.getElementById("ime").value;
	var prezime = document.getElementById("prezime").value;
	var lokacija = document.getElementById("lokacija").value;
	var nadimak = document.getElementById("nadimak").value;
	//window.alert(ime+ prezime+lokacija+nadimak);
	if(re.test(ime) && re.test(prezime) && re2.test(lokacija) && re.test(nadimak)){
			//window.alert(ime+" istp");
			document.getElementById("sub").disabled = false;
			document.getElementById("sub").style.background='#00FFFF';
	}
		 
	else{
		//window.alert(ime+" nije");
		document.getElementById("sub").disabled = true;
		document.getElementById("sub").style.background='#FF0000';
	}
  
};
	
</script>
*/
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
</br></br></br><input type="submit" name="izadi" value="IZADI!" id="sub" style=" height: 50px; width: 100px;"></form>
</html> 

