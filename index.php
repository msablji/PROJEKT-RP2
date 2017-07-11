ne<?php
require_once 'lutrija_db.php';

$status = session_status();
if($status == PHP_SESSION_NONE){
    //There is no active session
    session_start();

}

if(isset($_POST['admin']))
{
  header('Location: admin.html');
  exit;
}
if(isset($_POST['uredi_korisnike']))
{
	header('Location: uredi_korisnik.php');
  exit;
}

if(isset($_POST['bingo']))
{
  $_SESSION['ukupno']=0;
    $_SESSION['uvjet'] = 0;
    $_SESSION['kombinacija']=array();
    $_SESSION['oznaceni']=array();
    for($i=0;$i<50; $i++)
    {
        $_SESSION['oznaceni'][$i]=0;
    }
  header('Location: bingo.php');
  exit;
}

if(isset($_POST['loto']))
{
  $_SESSION['kraj']=0;
    $_SESSION['kliknuto']=array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0, 0, 0,0);
    $_SESSION['ajde_uplati'] = array(0,0,0,0,0,0,0);
    header('Location: loto.php');
    exit;
}

if(isset($_POST['euro']))
{
  $_SESSION['jesmo_gotovi']=0;
  $_SESSION['jesmo_gotovi_dodatno']=0;
  $_SESSION['stisli']=array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
  $_SESSION['stisli1']=array(0,0,0,0,0,0,0,0,0,0);
  $_SESSION['koji_su'] = array(0,0,0,0,0);
  $_SESSION['dodatni1'] = array(0,0);
  header('Location: euro.php');
  exit;
}

if(isset($_POST['kladenje']))
{
  header('Location: kladenje.html');
  exit;
}

if(!isset($_SESSION))
  {
      session_start();
      $_SESSION['polje']=array(); //za spremanje datuma zadnjeg posta
      $_SESSION['polje'][] = '';
	   $_SESSION['iznos_u_kn']='';
	$_SESSION['daj_jos_novaca']=0;
    $_SESSION['jel_admin']=0;

  }

if(!isset($_SESSION['logiran']))
{
    $_SESSION['logiran']=0;
    $_SESSION['korisnik']='';
    $_SESSION['iznos_u_kn']='';
	$_SESSION['jel_admin']=0;
}

//ako je korisnik ulogiran te je kliknuo na "LOGOUT"
if(isset($_POST['logout']))
{
  $_SESSION['logiran']=0;
  $_SESSION['korisnik']='';
  $_SESSION['iznos_u_kn']='';
  header('Location: index.php');
  exit;
}


if(isset($_POST['uplati_pare2']))
{

	if(isset($_POST['iznos']) && isset($_POST['br_rac']) && isset($_POST['kontrol_br']) )
	{

		$novci=$_POST['iznos'];
		$kolko=$_POST['br_rac'];
		$k=$_POST['kontrol_br'];
		$naziv=$_SESSION['korisnik'];
		$provjera=0;
		$db=DB::getConnection();
    try
  	{
  		$st = $db->prepare( 'SELECT * FROM Igraci WHERE nadimak=:im' );
  		$st->execute( array( 'im' => $_SESSION['korisnik'] ) );
  	}
    catch( PDOException $e ) { echo 'Greška:' . $e->getMessage() ; return; }
    foreach ($st->fetchAll() as $row)
    {
      if( $row['racun'] === $kolko && $row['kontr_br'] === $k )
    	{
			$novci+=$row['uplata'];

    		 $st = $db->exec( "UPDATE Igraci SET uplata = '$novci' WHERE nadimak ='$naziv' " );

              $poruka= "Uspješno ste uplatili novce na račun. Novo stanje računa je ".$novci."kn";
              $_SESSION['iznos_u_kn']=$novci;
             $provjera=1;
          echo '<script type="text/javascript">alert("'.$poruka.'");</script>';
	}

}if($provjera===0) echo '<script type="text/javascript">alert("Unijeli ste krive podatke!");</script>';
}}

//ako se korisnik želi ulogirati
if(isset($_POST['log']))
{
	if($_POST['nadimak1']!=='' && $_POST['lozinka1']!=='') //ukoliko je unos ispravan, provjerava se u bazi postojanje tog korisnika
	{
    $ima=false;
    $db=DB::getConnection();
    try
  	{
  		$st = $db->prepare( 'SELECT nadimak, lozinka, kliknuo, uplata,admin FROM Igraci WHERE nadimak=:im' );
  		$st->execute( array( 'im' => $_POST['nadimak1'] ) );
  	}
    catch( PDOException $e ) { echo 'Greška:' . $e->getMessage() ; return; }
    foreach ($st->fetchAll() as $row)
    {
      if( $row['kliknuo'] === '0' )
    	{
    		echo 'Korisnik s tim imenom se nije još registrirao. Provjerite e-mail.';
    		exit(1);
    	}
      else
      {
        $hashiran = $row['lozinka'];

        if(password_verify($_POST['lozinka1'], $hashiran))
        {
         // echo "Uspješno ste se ulogirali!";
          $ima=true;
		  if($row['admin'] === '0')
          {
		  $_SESSION['logiran']=1;
          $_SESSION['korisnik']=$_POST['nadimak1'];
		  $_SESSION['iznos_u_kn']=$row['uplata'];
	      $_SESSION['daj_jos_novaca']=0;
		  $_SESSION['jel_admin']=0;
		  }
		  if($row['admin'] === '1')
		  {
		  $_SESSION['jel_admin']=1;
		  $_SESSION['logiran']=1;
		  $_SESSION['korisnik']=$_POST['nadimak1'];}
        }
      }

    }
    //ukoliko tog korisnika nema...
    if($ima===false)
    {
      $_SESSION['logiran']=0;
      echo"Niste uneseni u bazu";
    }
  }

  //ukoliko korisnik nije unio podatke u sva polja
  else echo "Niste unijeli sve podatke!";

}


if(isset($_POST['novi']))
{

  if($_POST['ime'] && $_POST['prezime'] && $_POST['oib'] &&
    $_POST['nadimak2']!=='' && $_POST['lozinka2']!=='' && $_POST['email']!=''
    && $_POST['racun'] && $_POST['kontr_br'] && $_POST['uplata']) //provjera unosa, te ako je zadovoljeno, provjerava se u bazi
  {
    if( !filter_var( $_POST['email'], FILTER_VALIDATE_EMAIL) )
  	{
  		echo "Unesena e-mail adresa nije ispravna! Pokušajte ponovo";
  		exit(1);
  	}
    else
    {
      $postoji=false;
      $db=DB::getConnection();
      try
  		{
  			$st = $db->prepare( 'SELECT * FROM Igraci WHERE oib=:oib' );
  			$st->execute( array( 'oib' => $_POST['oib'] ) );
  		}
      catch( PDOException $e ) { exit( 'Greška u bazi: ' . $e->getMessage() ); }

      if( $st->rowCount() !== 0 )
  		{
  			// Taj user u bazi već postoji
  			echo "Taj korisnik već postoji!";
        $postoji=true;
        $_SESSION['logiran']=0;
  			exit(1);
  		}

      if($postoji===false)
      {
        //generiraj slučajno odabrani niz koji ćemo zalijepiti na link za registraciju:
        if(!preg_match('/^[0-9]{11}$/', $_POST['oib']) && !preg_match('/^[0-9]{16}$/', $_POST['racun']) &&
          !preg_match('/^[0-9]{16}$/',$_POST['kontr_br']) && !preg_match('/^[0-9]{16}$/',$_POST['uplata']))
          {
              echo '<script type="text/javascript">alert("Podaci koje Ste unijeli nisu ispravni!");</script>';
          }
          else
         {

        $niz = '';
    		for( $i = 0; $i < 20; ++$i )
    			$niz .= chr( rand(0, 25) + ord( 'a' ) );

        $hash=password_hash($_POST['lozinka2'], PASSWORD_DEFAULT);
        $db=DB::getConnection();

        try
        {
          $st = $db->prepare( 'INSERT INTO Igraci(ime,prezime, nadimak, lozinka, email,oib, racun, kontr_br,uplata, niz, kliknuo ) VALUES ' .
                            '(:im,:pr,:nad, :loz, :em,:oib, :rac,:kontr,:upl, :ni, 0)' );

          $st->execute( array( 'im' => $_POST['ime'],
                                'pr' => $_POST['prezime'],
                                'nad' => $_POST['nadimak2'],
                                'loz' => $hash,
                                'em' => $_POST['email'],
                                'oib' => $_POST['oib'],
                              'rac' => $_POST['racun'],
                             'kontr' => $_POST['kontr_br'],
                             'upl' => $_POST['uplata'],
                             'ni'  => $niz ) );
        }
        catch( PDOException $e ) { exit( 'Greška u bazi: ' . $e->getMessage() ); }

        echo '<script type="text/javascript">alert("Dodani ste u bazu! Klinite na registracijski link u e-mail poruci.");</script>';
        $_SESSION['logiran']=1;
        $_SESSION['korisnik']=$_POST["nadimak2"];


      $primatelj       = $_POST['email'];
      $naslov  = 'Registracijski e-mail';
      $poruka  = 'Poštovani ' . $_POST['nadimak2'] . "!\nZa dovršetak registracije kliknite na sljedeći link: " .
     'http://' . $_SERVER['SERVER_NAME'] . htmlentities( dirname( $_SERVER['PHP_SELF'] ) ) . '/register.php?niz=' . $niz;
    $poruka=$poruka . " Ukoliko ga ne možete otvoriti hiperlinkom, kopirajte u Vaš trenutni pretraživač te pokrenite.";
      $zaglavlje  = 'From: rp2@studenti.math.hr' . "\r\n" .
                  'Reply-To: rp2@studenti.math.hr' . "\r\n" .
                  'X-Mailer: PHP/' . phpversion();

      $valjano = mail($primatelj, $naslov, $poruka, $zaglavlje);

      if( !$valjano )
        exit( 'Greška: ne mogu poslati mail. (Pokrenite na rp2 serveru.)' );
      }
    }
  }


}
//ukoliko je neko polje neispunjeno
else echo "Niste unijeli sve podatke!";
}


if(isset($_POST['provjera']) && $_POST['provjera_dobitka']!='')  //za provjeru dobitka
{
  $valjan=0;
  $db=DB::getConnection();
  try
  {
    $st = $db->prepare( 'SELECT izvucen FROM Bingo WHERE kontr_broj= :kb AND proslost= :pr' );
    $st->execute( array( 'kb' =>$_POST['provjera_dobitka'], 'pr'=>'1' ) );
  }
  catch( PDOException $e ) { echo 'Greška:' . $e->getMessage() ; return; }


  foreach ($st->fetchAll() as $row)
  {

		if($row['izvucen']==='1')
    {
        $poruka= "IGRA BINGO"."\\nListić je dobitan!\\nDobitak: 9.500.000,00 kuna!";
        echo '<script type="text/javascript">alert("'.$poruka.'");</script>';
        $valjan=1;

    }
    if($row['izvucen']==='0')
    {
      $poruka= "IGRA BINGO"."\\nListić nije dobitan!";
        echo '<script type="text/javascript">alert("'.$poruka.'");</script>';
        $valjan=1;
    }


  }

  $db=DB::getConnection();
  try
  {
    $st = $db->prepare( 'SELECT dobitan, polu_dobitak FROM Euro WHERE id= :kb AND proslo= :pr' );
    $st->execute( array( 'kb' =>$_POST['provjera_dobitka'], 'pr'=>'1' ) );
  }
  catch( PDOException $e ) { echo 'Greška:' . $e->getMessage() ; return; }


  foreach ($st->fetchAll() as $row)
  {

		if($row['dobitan']==='11')
    {
        if($row['polu_dobitak']==='2')
        {
          $poruka= "IGRA EUROJACKPOT"."\\nListić je dobitan!\\nPogođena je i kombinacija i oba dopunska broja!\\nDobitak: 344.448.023,69 kuna! ";
        }
        if($row['polu_dobitak']==='1')
        {
          $poruka= "IGRA EUROJACKPOT"."\\nListić je dobitan!\\nPogođena je i kombinacija i jedan dopunski broj!\\nDobitak: 5.935.366,28kuna! ";
        }

        echo '<script type="text/javascript">alert("'.$poruka.'");</script>';
        $valjan=1;
    }
    if($row['dobitan']==='10')
    {
        $poruka= "IGRA EUROJACKPOT"."\\nListić je dobitan!\\nPogođena je kombinacija, ali ne i dopunski brojevi!\\nDobitak:598.523,85 kuna!";
        echo '<script type="text/javascript">alert("'.$poruka.'");</script>';
        $valjan=1;

    }
    if($row['dobitan']==='0')
    {
      $poruka= "IGRA EUROJACKPOT"."\\nListić nije dobitan!";
        echo '<script type="text/javascript">alert("'.$poruka.'");</script>';
        $valjan=1;
    }


  }

  $db=DB::getConnection();
  try
  {
    $st = $db->prepare( 'SELECT dobitan, polu_dobitak FROM Loto WHERE id= :kb AND proslo= :pr' );
    $st->execute( array( 'kb' =>$_POST['provjera_dobitka'], 'pr'=>'1' ) );
  }
  catch( PDOException $e ) { echo 'Greška:' . $e->getMessage() ; return; }


  foreach ($st->fetchAll() as $row)
  {

		if($row['dobitan']==='11')
    {
      if($row['polu_dobitak']==='7')
      {
        $poruka= "IGRA LOTO 7/49"."\\nListić je dobitan!\\Pogođena je i kombinacija i slovo!\\Dobitak: 14.188.524,98 kuna!";
      }
      if($row['polu_dobitak']==='6')
      {
        $poruka= "IGRA LOTO 7/49"."\\nListić je dobitan!\\Pogođeno je 6 brojeva i slovo!\\Dobitak: 19.479,58 kuna!";
      }
      if($row['polu_dobitak']==='5')
      {
        $poruka= "IGRA LOTO 7/49"."\\nListić je dobitan!\\Pogođeno je 5 brojeva i slovo!\\Dobitak: 338,11 kuna!";
      }
      if($row['polu_dobitak']==='4')
      {
        $poruka= "IGRA LOTO 7/49"."\\nListić je dobitan!\\Pogođena su 4 broja i slovo!\\Dobitak: 42,68 kuna!";
      }

      echo '<script type="text/javascript">alert("'.$poruka.'");</script>';
      $valjan=1;
    }
    if($row['dobitan']==='10')
    {
      $poruka= "IGRA LOTO 7/49"."\\nListić je dobitan!\\nPogođena je kombinacija, ali ne i slovo!\\nDobitak:6.175.631,97 kuna!";
        echo '<script type="text/javascript">alert("'.$poruka.'");</script>';
        $valjan=1;
    }
    if($row['dobitan']==='0')
    {
      $poruka= "IGRA LOTO 7/49"."\\nListić nije dobitan!";
        echo '<script type="text/javascript">alert("'.$poruka.'");</script>';
        $valjan=1;
    }


  }

  $db=DB::getConnection();
  try
  {
    $st = $db->prepare( 'SELECT dobio, dobitak FROM Kladionica WHERE listic= :kb AND proslo= :pr' );
    $st->execute( array( 'kb' =>$_POST['provjera_dobitka'], 'pr'=>'1' ) );
  }
  catch( PDOException $e ) { echo 'Greška:' . $e->getMessage() ; return; }


  foreach ($st->fetchAll() as $row)
  {

		if($row['dobio']==='1')
    {
        $dobitak=$row['dobitak'];

        $poruka= "IGRA KLAĐENJE NA KONJE"."\\nListić je dobitan!\\nDobitak: ".$dobitak." kuna!";
        echo '<script type="text/javascript">alert("'.$poruka.'");</script>';
        $valjan=1;

    }
    if($row['dobio']==='0')
    {
      $poruka= "IGRA KLAĐENJE NA KONJE"."\\nListić nije dobitan!";
        echo '<script type="text/javascript">alert("'.$poruka.'");</script>';
        $valjan=1;
    }


  }

  if($valjan===0)
  {
    echo '<script type="text/javascript">alert("Listić s tim kontrolnim brojem ne postoji ili se nije odigrala Vaša igra! Unesite broj listića nakon izvačenja!");</script>';
  }
}

if(isset($_POST['profil']))
{
 header('Location: profil.php');
  exit;
}

 ?>


 <!DOCTYPE html>
 <html>
   <head>
     <meta charset="utf-8">
     <title></title>
     <style>
     body{
        font-family:courier;
        font-size:12pt;
     }

     h3 { border-top: 1px solid #000;
       border-bottom: 1px solid #000; }

     div{

         border-color: black;
         height:400px;
         text-align:center;
         border: 1px solid black;
         border-radius: 5px;
         position:absolute;
         top:100px;
         right:0;
 		background-color:  #ffffcc;
 	}
 div.logiran{
   border-color: black;
         height:250px;
 		width:350px;
         text-align:left;
         border: 1px solid black;
         border-radius: 5px;
         position:absolute;
         top:0;
         right:0;
 }
 div.gumbi{
   border-color: black;
   height:50px;
   width: 65%;
   text-align:left;
   border: 1px solid black;
   border-radius: 5px;
 background-color:  #ffffcc;
   position:absolute;
   top:30%;
   left:0%;
 }
 div.gumbi2{
   border-color: black;
   height:50px;
   width: 65%;
   text-align:left;
   border: 1px solid black;
   border-radius: 5px;
 background-color:  #ffffcc;
   position:absolute;
   top:37%;
   left:0%;
 }
 .slika{
 	width: 50%;
 	height:250px;

 }


 article {
 	margin-top:20px;
 	padding:5px;
 	width:80%;
 	float:left;
 	}

 aside {
 	text-align:center;
 	border: 1px solid black;
 	margin-top:2px;
 	margin-left:5px;
 	float:right;
 	width:300px;
 	padding:5px;
 	background-image: url("buba1.jpg");

 	}

 </style>
   </head>
   <body>
     <header>
    <img src="jedan.jpg"  class="slika" alt="slika" >
   </header>

   <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
 <?php
 //ako nije ulogiran, prikazat će formu za logiranje
 if($_SESSION['logiran']===0)
 {?>
   <div>
  <h3>Za postojeće korisnike:</h3>
   Korisničko ime: <input type="text" name="nadimak1"/>
   </br>
   Lozinka: <input type="password" name="lozinka1"/>
   </br>
   <button type="submit" name="log">Ulogiraj se</button>
 </br>
 <h3>Za nove korisnike:</h3>
 Ime: <input type="text" name="ime"/>
 </br>

 Prezime: <input type="text" name="prezime"/>
 </br>

 OIB: <input type="text" name="oib"/>
 </br>

 Korisničko ime: <input type="text" name="nadimak2"/>
 </br>
 Lozinka: <input type="password" name="lozinka2"/>
 </br>
   E-mail: <input type="email" name="email"/>
   </br>
   Broj računa:  <input type="text" name="racun"/>
     </br>
   Kontrolni broj:  <input type="text" name="kontr_br"/>
       </br>
       Iznos uplate: <input type="text" name="uplata"/>
   <button type="submit" name="novi">Stvori novog korisnika</button>
 </div>
   <?php
 }

 //ako je ulogiran, prikazat će ime korisnika i gumb za logout
 if($_SESSION['logiran']===1)
 { if($_SESSION['jel_admin']===0){?>
   <div class="logiran">
   <?php
   echo '<h2 style="text-align:center;">'."  Korisnik: " .$_SESSION['korisnik'] ."  ". '</h2>';
   echo "Na računu imate:".$_SESSION['iznos_u_kn']."kn"."</br>";
 	?><form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
 	Unesi iznos:<input type="text"  name="iznos" value="" /></br>
 	 Broj računa:<input type="text"  name="br_rac" value="" /></br>
 	 Kontrolni br:<input type="text"  name="kontrol_br" value="" /></br></br>
 	 <button type="submit" name="uplati_pare2" style=" height: 40px; width: 80px;">Uplati</button>
 	 <button type="submit" name="profil" style=" height: 40px; width: 80px;">Vidi profil</button>
      <button type="submit" name="logout"  style=" height: 40px; width: 80px;">Logout</button></form>
   </div>
   <aside><nav>
 <ul class="a"><h1 style="font-family:courier">Igraj!</h1>
   <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
   <li> <button type="submit" style=" height: 40px; width: 120px; font-family:courier"id="bingo" name="bingo">Bingo</button></li>
   <li><button type="submit" style=" height: 40px; width: 120px; font-family:courier" id="loto" name="loto">Loto 7/39</button></li>
   <li><button type="submit" style=" height: 40px; width: 120px; font-family:courier" id="kladenje" name="kladenje">Klađenje</button></li>
   <li><button type="submit" style=" height: 40px; width: 120px; font-family:courier" id="euro" name="euro">Eurojackpot</button></li>
 </ul> </form>
 </nav></aside>

 <?php
 }
 else
 {?>
 	<div class="logiran">
   <?php
   echo '<h2 style="text-align:center;">'."Logirani ste kao admin! ". '</h2>';
 	?><form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
      <button type="submit" name="uredi_korisnike"  style="height: 50px; width: 100px;">Uredi korisnike</button></br>
 	 <button type="submit" name="logout"  style=" height: 50px; width: 100px;">Logout</button></form>
   </div><?php
 }
 } ?>
 <div class="gumbi">
 <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">

 Unesite kontrolni broj:  <input type="text" id="provjera_dobitka" name="provjera_dobitka"/>
   <button type="submit" id="provjera" name="provjera" style=" height: 40px; width: 150px;font-family:courier;"  >Provjera dobitka!</button>
   <button type="submit" id="admin" name="admin" style=" height: 40px; width: 300px;font-family:courier;"  >Pogledaj nekoliko zadnjih izvlačenja!</button></form>
 </div>

  <div class="gumbi2" >
  Emisije sa zadnjih izvlačenja:<a href="https://www.youtube.com/watch?v=s9hQBKNoOPo" > Bingo </a> <a href="https://www.youtube.com/watch?v=OFBnQP4jcqo" > Loto 7/39 </a> <a href="https://www.youtube.com/watch?v=tj4r8b9ePUo"> Eurojackpot </a>

 </div>
 </br></br></br></br></br></br>
 <article>

 <img src="eurojack.jpg" class="slikice" alt="slika" >
 <img src="bingo.jpg" class="slikice" alt="slika" >


 </article>
   </body>
 </html>
