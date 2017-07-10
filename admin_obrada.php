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
		if($row['dobiveno']==='1')
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
	$message['i_slovo']=0;
	$message['slovo']='';
	$nadimak='';
		$kontr_broj;
		$brojevi;
		$niz;
		$ima=0;
		$ima_slovo=0;

  for($i=0; $i<7; $i++)
  {
    $brojevi[$i]=rand(1,49);
    shuffle($brojevi);
  }
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

	if($razlika===null)
	{
		if($row['slovo']===$slovo)
		{
			$message['i_slovo']=1;
			$ima_slovo=1;
		}
		else {
			$message['i_slovo']=0;
			$ima_slovo=0;
		}
		$message[ 'dobitni_listic' ][] = array( 'korisnicko_ime' => $row['korisnicko_ime'], 'id' => $row['id'], 'kombinacija' => $row['kombinacija'], 'slovo' =>$slovo );
		$message['ima']=1;
		$ima=1;
		$nadimak = $row['korisnicko_ime'];
		$id = $row['id'];
		$kombinacija=$row['kombinacija'] ;

	}
	else {
	$message['ima']=0;
	$ima=0;
	$message['slovo']=0;
	}



}

//update baze prošlih izvlačenja
$niz=implode(",",$brojevi);
$niz.= " - " . $slovo;

$db=DB::getConnection();
$st= $db->exec("INSERT INTO Proslost (ime_igre, pobjednik, datum, vrijeme, dobiveno) VALUES ('Loto 7/49', '".$niz."', '".$_GET['datum']."', '".$_GET['vrijeme_0']."', '".$ima."')");

//stavi da je listić dobitna_kombinacijadb=DB::getConnection();

	$db=DB::getConnection();
	if($ima_slovo===1)  //ako ima slovo i brojeve, onda je 11
	{
		$st = $db->exec(  "UPDATE Loto SET dobitan = '11' WHERE korisnicko_ime = '".$nadimak."' " );
	}
	if($ima_slovo===0) // ako ima slovo ali nema brojeve, onda je 10
	{
		$st = $db->exec(  "UPDATE Loto SET dobitan = '10' WHERE korisnicko_ime = '".$nadimak."' " );

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


  for($i=0; $i<15; $i++)
  {
    $brojevi[$i]=rand(1,50);
    shuffle($brojevi);
  }
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
	$message['i_dop']=0;
	$ima_dopunske=0;
	$niz1='';
	$niz2='';
	$ima=0;

	for($i=0; $i<5; $i++)
	{
		$brojevi[$i]=rand(1,50);
		shuffle($brojevi);
	}
	  $message['dobitna_kombinacija'] = $brojevi;
	for($i=0; $i<2; $i++)
	{
		$dopunski[$i]=rand(1,10);
		shuffle($brojevi);
	}
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
			if($razlika2===null)
			{
				$message['i_dop']=1;
				$ima_dopunske=1;
			}
			else {
				$message['i_dop']=0;
				$ima_dopunske=0;
			}
			$message[ 'dobitni_listic' ][] = array( 'korisnicko_ime' => $row['korisnicko_ime'], 'id' => $row['id'], 'kombinacija' => $row['kombinacija'], 'dopunski' =>$row['dopunski'] );
			$message['ima']=1;
			$ima=1;
			$nadimak = $row['korisnicko_ime'];
			$id = $row['id'];
			$kombinacija=$row['kombinacija'] ;
			$dopunski = $row['dopunski'];

		}
		else {
		$message['ima']=0;
		$ima=0;
	$message['i_dop']=0;
		}



  }

	$niz1=implode(",",$brojevi);
	$niz2=implode(",",$dopunski);
	$niz1.=" Dopunski: " .$niz2;

	$db=DB::getConnection();
	$st= $db->exec("INSERT INTO Proslost (ime_igre, pobjednik, datum, vrijeme, dobiveno) VALUES ('Eurojackpot', '".$niz1."', '".$_GET['datum']."', '".$_GET['vrijeme_3']."', '".$ima."')");

	//stavi da je listić dobitna_kombinacijadb=DB::getConnection();

		$db=DB::getConnection();
		if($ima_dopunske===1)  //ako ima slovo i brojeve, onda je 11
		{
			$st = $db->exec(  "UPDATE Euro SET dobitan = '11' WHERE korisnicko_ime = '".$nadimak."' " );
		}
		if($ima_dopunske===0) // ako ima slovo ali nema brojeve, onda je 10
		{
			$st = $db->exec(  "UPDATE Euro SET dobitan = '10' WHERE korisnicko_ime = '".$nadimak."' " );

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
