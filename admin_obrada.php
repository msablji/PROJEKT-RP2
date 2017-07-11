<?php

if(!isset($_SESSION))
	 {
			 session_start();
				 $_SESSION['ukupno']=0;
	 }
class DB
{
	private static $db = null;

	final private function __construct() {}
	final private function __clone() {}

	public static function getConnection()
	{
		// Ako još nismo spajali na bazu, uspostavi konekciju.
		if( DB::$db === null )
		{

			try
			{
				DB::$db = new PDO('mysql:host=rp2.studenti.math.hr; dbname=martinek; charset=utf8', 'student', 'pass.mysql');
				// Ako želimo da funkcije poput prepare, execute bacaju exceptione:
				DB::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		    	DB::$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			}
			catch( PDOException $e ) { exit( 'Greška prilikom spajanja na bazu:' . $e->getMessage() ); }
		}

		// Vrati novostvorenu ili od ranije postojeću konekciju.
		return DB::$db;
	}
};

function sendJSONandExit( $message )
{
    // Kao izlaz skripte pošalji $message u JSON formatu i prekini izvođenje.
    header( 'Content-type:application/json;charset=utf-8' );
    echo json_encode( $message );
    flush();
    exit( 0 );
}


function sendErrorAndExit( $messageText )
{
	$message = [];
	$message[ 'error' ] = $messageText;
	sendJSONandExit( $message );
}

//________________________________________obrada listića prošlosti
if(isset($_GET['obrada']))
{
	$db=DB::getConnection();
  $message = [];
  $st = $db->query( 'SELECT ime_igre, pobjednik, datum, vrijeme, dobiveno FROM Proslost ' );
	$message['proslost']=[];

	foreach ($st->fetchAll() as $row)
  {
		if($row['dobiveno']==='0')
		{
			$message[ 'proslost' ][] = array( 'ime_igre' => $row['ime_igre'], 'pobjednik' => $row['pobjednik'], 'datum' => $row['datum'] ,
		 'vrijeme' => $row['vrijeme'],  'dobiveno' => 'Nema dobitnika!');
		}
		if($row['dobiveno']==='1' || $row['dobiveno']==='10' || $row['dobiveno']==='11')
		{
			$message[ 'proslost' ][] = array( 'ime_igre' => $row['ime_igre'], 'pobjednik' => $row['pobjednik'], 'datum' => $row['datum'] ,
		 'vrijeme' => $row['vrijeme'],  'dobiveno' => 'Dobitnik je izvučen!');
		}
	}

sendJSONandExit( $message );

  }



//_______________LOTO ____________________


if(isset($_GET['vrijeme_0']) && $_GET['datum'])
{
	$message = [];
  $brojevi=  [];
	$message['ima']=0;
	$message['i_slovo']='';
	$message['slovo']='';
	$message['razlika']=[];
	$polu_dobitak=0;
	$nadimak='';
		$kontr_broj;
		$brojevi;
		$niz;
		$ima='0';
		$ima_slovo='';

		$brojevi = range(1, 49);
		shuffle($brojevi );
		$brojevi = array_slice($brojevi ,0,7);
	sort($brojevi);
  $message['dobitna_kombinacija'] = $brojevi;


	$str = 'AB';
$slovo = $str[rand(0, strlen($str)-1)];
$message['slovo']= $slovo;

$db=DB::getConnection();
try
{
	$st = $db->prepare( 'SELECT kombinacija, id, korisnicko_ime FROM Loto WHERE proslo = :proslo' );
	$st->execute( array( 'proslo' => '0') );   //od onih koji još nisu prosli krug izvlacenja
}
catch( PDOException $e ) { echo 'Greška:' . $e->getMessage() ; return; }

foreach ($st->fetchAll() as $row)
{
	$niz=explode(" ",$row['kombinacija']);
	$razlika = array_diff($brojevi, $niz);
	$message['razlika']=$razlika;

	if($razlika===null)  //ako imamo poklapanje svih 7 brojeva
	{
		if($row['slovo']===$slovo)
		{
			$message['i_slovo']='11';
			$ima='11';
			$polu_dobitak=7;  //svih 7 je pogođeno
		}
		else {
			$message['i_slovo']='10';
			$ima='10';
			$polu_dobitak=0;  //svih 7 je pogođeno
		}

		$message[ 'dobitni_listic' ][] = array( 'korisnicko_ime' => $row['korisnicko_ime'], 'id' => $row['id'], 'kombinacija' => $row['kombinacija'], 'slovo' =>$slovo, 'polu_dobitak' =>$polu_dobitak);
		$message['ima']=1;
		$nadimak = $row['korisnicko_ime'];
		$id = $row['id'];
		$kombinacija=$row['kombinacija'] ;

	}
	if(sizeof($razlika)===1)  //6 brojeva je pogođeno
	{
		if($row['slovo']===$slovo)
		{
			$message['i_slovo']='11';
			$ima='11';
			$polu_dobitak= 6; //6 je pogođeno
		}
		else {
			$message['i_slovo']='10';
			$ima='10';
			$polu_dobitak= 0; //6 je pogođeno
		}

		$message[ 'dobitni_listic' ][] = array( 'korisnicko_ime' => $row['korisnicko_ime'], 'id' => $row['id'], 'kombinacija' => $row['kombinacija'], 'slovo' =>$slovo, 'polu_dobitak' =>$polu_dobitak);
		$message['ima']=1;
		$nadimak = $row['korisnicko_ime'];
		$id = $row['id'];
		$kombinacija=$row['kombinacija'] ;
	}

	if(sizeof($razlika)===2)  //5 brojeva je pogođeno
	{
		if($row['slovo']===$slovo)
		{
			$message['i_slovo']='11';
			$ima='11';
			$polu_dobitak= 5; //6 je pogođeno
		}
		else {
			$message['i_slovo']='10';
			$ima='10';
			$polu_dobitak= 0; //5 je pogođeno
		}

		$message[ 'dobitni_listic' ][] = array( 'korisnicko_ime' => $row['korisnicko_ime'], 'id' => $row['id'], 'kombinacija' => $row['kombinacija'], 'slovo' =>$slovo, 'polu_dobitak' =>$polu_dobitak );
		$message['ima']=1;
		$nadimak = $row['korisnicko_ime'];
		$id = $row['id'];
		$kombinacija=$row['kombinacija'] ;
	}
	if(sizeof($razlika)===3)  //4 broja je pogođeno
	{
		if($row['slovo']===$slovo)
		{
			$message['i_slovo']='11';
			$ima='11';
			$polu_dobitak= 4; //5 je pogođeno
		}
		else {
			$message['i_slovo']='10';
			$ima='10';
			$polu_dobitak= 0; //5 je pogođeno
		}

		$message[ 'dobitni_listic' ][] = array( 'korisnicko_ime' => $row['korisnicko_ime'], 'id' => $row['id'], 'kombinacija' => $row['kombinacija'], 'slovo' =>$slovo, 'polu_dobitak' =>$polu_dobitak );
		$message['ima']=1;
		$nadimak = $row['korisnicko_ime'];
		$id = $row['id'];
		$kombinacija=$row['kombinacija'] ;
	}

	else {
	$message['ima']=0;
	$ima='0';
	$message['slovo']=$slovo;
	$polu_dobitak= 0; //nema nikakvog dobitka
	}



}

//update baze prošlih izvlačenja
$niz=implode(",",$brojevi);
$niz.= " - " . $slovo;

$db=DB::getConnection();
$st= $db->exec("INSERT INTO Proslost (ime_igre, pobjednik, datum, vrijeme, dobiveno) VALUES ('Loto 7/49', '".$niz."', '".$_GET['datum']."', '".$_GET['vrijeme_0']."', '".$ima."')");

//stavi da je listić dobitna_kombinacijadb=DB::getConnection();

	$db=DB::getConnection();
	if($ima==='11')  //ako ima slovo i brojeve, onda je 11
	{
		$st = $db->exec(  "UPDATE Loto SET dobitan = '11' WHERE korisnicko_ime = '".$nadimak."' " );

		if($polu_dobitak==='7')
		{
				$st = $db->exec(  "UPDATE Loto SET poludobitak = '7' WHERE korisnicko_ime = '".$nadimak."' " );
		}
		if($polu_dobitak==='6')
		{
				$st = $db->exec(  "UPDATE Loto SET poludobitak = '6' WHERE korisnicko_ime = '".$nadimak."' " );
		}
		if($polu_dobitak==='5')
		{
				$st = $db->exec(  "UPDATE Loto SET poludobitak = '5' WHERE korisnicko_ime = '".$nadimak."' " );
		}
		if($polu_dobitak==='4')
		{
				$st = $db->exec(  "UPDATE Loto SET poludobitak = '4' WHERE korisnicko_ime = '".$nadimak."' " );
		}
	}

	if($ima==='10') // ako ima slovo ali nema brojeve, onda je 10
	{
		$st = $db->exec(  "UPDATE Loto SET dobitan = '10' WHERE korisnicko_ime = '".$nadimak."' " );
		$st = $db->exec(  "UPDATE Loto SET poludobitak = '0' WHERE korisnicko_ime = '".$nadimak."' " );

	}





//stavi da su listići zastarjeli
$db=DB::getConnection();
	$st = $db->exec( "UPDATE Loto SET proslo = '1'" );

	sendJSONandExit( $message );
}


//___________________BINGO____________

if(isset($_GET['vrijeme_2']))
{
  $message = [];
  $brojevi=  [];
	$message['ima']=0;
	$nadimak='';
		$kontr_broj;
		$brojevi;
		$niz='';

		$brojevi = range(1, 50);
		shuffle($brojevi );
		$brojevi = array_slice($brojevi ,0,15);
	sort($brojevi);
  $message['dobitna_kombinacija'] = $brojevi;

	$ima=0;
	$db=DB::getConnection();
  try
  {
    $st = $db->prepare( 'SELECT nadimak, kontr_broj, brojevi FROM Bingo WHERE proslost = :proslost' );
    $st->execute( array( 'proslost' => '0' ) );   //od onih koji još nisu prosli krug izvlacenja
  }
  catch( PDOException $e ) { echo 'Greška:' . $e->getMessage() ; return; }
	$message[ 'dobitni_listic' ] = [];
	foreach ($st->fetchAll() as $row)
  {
		$niz=explode(" ",$row['brojevi']);
		$razlika = array_diff($brojevi, $niz);  //ako postoji razlika, listić nije dobitan
		if($razlika===null)
		{
			$message[ 'dobitni_listic' ][] = array( 'nadimak' => $row['nadimak'], 'kontr_broj' => $row['kontr_broj'], 'brojevi' => $row['brojevi'] );
			$message['ima']=1;
			$ima=1;
			$nadimak = $row['nadimak'];
			$kontr_broj = $row['kontr_broj'];
			$brojevi=$row['brojevi'] ;

			$db=DB::getConnection();
			$st = $db->exec(  "UPDATE Bingo SET izvucen = '1' WHERE kontr_broj = '".$kontr_broj."'");

		}
		else {
		$message['ima']=0;
		$ima=0;
		}



  }


	//stavi da su listići zastarjeli
	$db=DB::getConnection();
	  $st = $db->exec( "UPDATE Bingo SET proslost = '1'" );

		//update baze prošlih izvlačenja
		$niz=implode(",",$brojevi);
$db=DB::getConnection();
$st= $db->exec("INSERT INTO Proslost (ime_igre, pobjednik, datum, vrijeme, dobiveno) VALUES ('Bingo', '".$niz."', '".$_GET['datum']."', '".$_GET['vrijeme_2']."', '".$ima."')");

  sendJSONandExit( $message );
}

//______________EUROJACKPOT _________________

if(isset($_GET['vrijeme_3']) && isset($_GET['datum']))
{
	$message = [];
	$brojevi=  [];
	$dopunski=  [];
	$message['ima']=0;
	$nadimak='';
	$kontr_broj;
	$brojevi;
	$message['i_dop']='';
	$ima_dopunske='';
	$niz1='';
	$niz2='';
	$ima='';
	$message['razlika']=[];
	$polu_dobitak=0;

	$brojevi = range(1, 50);
	shuffle($brojevi );
	$brojevi = array_slice($brojevi ,0,5);
	sort($brojevi);
	$message['dobitna_kombinacija'] = $brojevi;

	$dopunski = range(1, 10);
	shuffle($dopunski );
	$dopunski = array_slice($dopunski ,0,2);
	sort($dopunski);
	$message['dopunski'] = $dopunski;


	$db=DB::getConnection();
  try
  {
    $st = $db->prepare( 'SELECT kombinacija, dopunski, id, korisnicko_ime FROM Euro WHERE proslo = :proslo' );
    $st->execute( array( 'proslo' => '0' ) );   //od onih koji još nisu prosli krug izvlacenja
  }
  catch( PDOException $e ) { echo 'Greška:' . $e->getMessage() ; return; }

	$message[ 'dobitni_listic' ] = [];
	foreach ($st->fetchAll() as $row)
  {

		$niz1=explode(" ",$row['kombinacija']);
		$niz2=explode(" ",$row['dopunski']);
		$razlika1 = array_diff($brojevi, $niz1);
		$razlika2 =array_diff($dopunski, $niz2);

		if($razlika1===null)
		{
			if($razlika2===null)  //pogođeni su i dopunski i kombinacija
			{
				$message['i_dop']='11';
				$ima_dopunske='1';
				$ima='11';
				$message['razlika']=$razlika2;
				$polu_dobitak=2; //oba su pogodena
			}
			if(sizeof($razlika2)===1) //pogođena je kombinacija i jedan dopunski broj
			{
				$message['i_dop']='11';
				$ima_dopunske='1';
				$ima='11';
				$message['razlika']=$razlika2;
				$polu_dobitak=1;
			}
			if(sizeof($razlika2)===2) //pogođena je kombinacija, ali ne i dopunska slova
			{
				$message['i_dop']='10';
				$ima_dopunske='0';
				$ima='10';
			}
			else {
				$message['i_dop']='10';
				$ima_dopunske='0';
				$ima='10';
				$message['razlika']=$razlika2;
				$polu_dobitak=0;
			}
			$message[ 'dobitni_listic' ][] = array( 'korisnicko_ime' => $row['korisnicko_ime'], 'id' => $row['id'], 'kombinacija' => $row['kombinacija'], 'dopunski' =>$row['dopunski'], 'poludobitak'=>$row['polu_dobitak'] );
			$message['ima']=1;
			$ima=1;
			$nadimak = $row['korisnicko_ime'];
			$id = $row['id'];
			$kombinacija=$row['kombinacija'] ;
			$dopunski = $row['dopunski'];

		}
		else {
		$message['ima']=0;
		$ima='0';
	$message['i_dop']='0';
	$message['razlika']=$razlika2;
	$polu_dobitak=0;
		}



  }

	$niz1=implode(",",$brojevi);
	$niz2=implode(",",$dopunski);
	$niz1.=" Dopunski: " .$niz2;

	$db=DB::getConnection();
	$st= $db->exec("INSERT INTO Proslost (ime_igre, pobjednik, datum, vrijeme, dobiveno) VALUES ('Eurojackpot', '".$niz1."', '".$_GET['datum']."', '".$_GET['vrijeme_3']."', '".$ima."')");

	//stavi da je listić dobitna_kombinacijadb=DB::getConnection();

		$db=DB::getConnection();
		if($ima==='11')  //ako ima slovo i brojeve, onda je 11
		{
			$st = $db->exec(  "UPDATE Euro SET dobitan = '11' WHERE korisnicko_ime = '".$nadimak."' " );
			if($polu_dobitak==='2')
			{
					$st = $db->exec(  "UPDATE Loto SET poludobitak = '7' WHERE korisnicko_ime = '".$nadimak."' " );
			}
			if($polu_dobitak==='1')
			{
					$st = $db->exec(  "UPDATE Loto SET poludobitak = '6' WHERE korisnicko_ime = '".$nadimak."' " );
			}

		}
		if($ima==='10') // ako ima slovo ali nema brojeve, onda je 10
		{
			$st = $db->exec(  "UPDATE Euro SET dobitan = '10' WHERE korisnicko_ime = '".$nadimak."' " );
			$st = $db->exec(  "UPDATE Loto SET poludobitak = '0' WHERE korisnicko_ime = '".$nadimak."' " );

		}





	//stavi da su listići zastarjeli
	$db=DB::getConnection();
		$st = $db->exec( "UPDATE Euro SET proslo = '1'" );

	  sendJSONandExit( $message );
}


//_______________KLADIONICA__________________________
if(isset($_GET['vrijeme_4']) && isset($_GET['datum']))
{
  $polje=[];
	$nadimak='';
  //svi konji s imenom i identifikatorom
  $db=DB::getConnection();
  try
  {
    $st = $db->prepare( 'SELECT ime, koeficijent, id FROM Utrke WHERE vrijeme= :vr' );
    $st->execute( array( 'vr' => $_GET['vrijeme_4'] ) );
  }
  catch( PDOException $e ) { echo 'Greška:' . $e->getMessage() ; return; }
  $message = [];
  $message[ 'utrka' ] = [];
  foreach ($st->fetchAll() as $row)
  {

		$message[ 'utrka' ][] = array( 'ime' => $row['ime'], 'koeficijent' => $row['koeficijent'], 'id' => $row['id'] );
    array_push($polje,$row['id']);


  }
  $random=$polje[array_rand($polje)];
  $message['generirani']=$random;

  //od random generiranog uzima se ime i koeficijent i vrijeme
  $db=DB::getConnection();
  $message[ 'pobjednik' ] = [];
  try
  {
    $st = $db->prepare( 'SELECT ime, koeficijent, vrijeme FROM Utrke WHERE id= :id' );
    $st->execute( array( 'id' => $random ) );
  }
  catch( PDOException $e ) { echo 'Greška:' . $e->getMessage() ; return; }
  foreach ($st->fetchAll() as $row)
  {

		$message[ 'pobjednik' ][] = array( 'ime' => $row['ime'], 'koeficijent' => $row['koeficijent'],  'vrijeme'=>$row['vrijeme']);
    $pobjednik=$row['ime'];
    $koef=$row['koeficijent'] ;
    $termin=$row['vrijeme'];


  }
  //u bazu statistika unosimo pobjednika
  $db=DB::getConnection();
	try
	{
		$st = $db->exec( "INSERT INTO Statistika(ime_konja, datum, vrijeme) VALUES ('".$pobjednik."', '".$_GET['datum']."', '".$termin."'  )" );

	}
	catch( PDOException $e ) { echo 'Greška:' . $e->getMessage() ; return; }

  //u bazi Kladionica pronalazimo pobjednika
  $db=DB::getConnection();
  $message[ 'dobitnik' ] = [];
  $ima=0;
  try
  {
    $st = $db->prepare( 'SELECT nadimak, dobitak, listic FROM Kladionica WHERE konj= :konj AND proslo= :proslo' );
    $st->execute( array( 'konj' => $pobjednik, 'proslo' =>'0' ) );
  }
  catch( PDOException $e ) { echo 'Greška:' . $e->getMessage() ; return; }
    foreach ($st->fetchAll() as $row)
    {
      	$message[ 'dobitnik' ][] = array( 'nadimak' => $row['nadimak'], 'dobitak' => $row['dobitak'],  'listic'=>$row['listic']);
        $ima=1;
				$nadimak=$row['nadimak'];
    }
    $message['ima']=$ima;
    //promjena svih 0 u 1
    if($ima===1)
    {
      $db=DB::getConnection();
      $st = $db->exec( "UPDATE Kladionica SET dobio = '1' WHERE nadimak ='".$nadimak."' " );
    }

$db=DB::getConnection();
  $st = $db->exec( "UPDATE Kladionica SET proslo = '1'" );

//u bazu proslih izvlačenja
	$db=DB::getConnection();
	$st= $db->exec("INSERT INTO Proslost (ime_igre, pobjednik, datum, vrijeme, dobiveno) VALUES ('Klađenje na konje', '".$pobjednik."', '".$_GET['datum']."', '".$_GET['vrijeme_4']."', '".$ima."')");

sendJSONandExit( $message );
}

 ?>
