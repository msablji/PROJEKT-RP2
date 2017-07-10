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

if(isset($_GET['login']))
{
	if($_GET['login']==="a")
	{
		$message['korisnik'] = $_SESSION['korisnik'];

		sendJSONandExit( $message );
	}
	if($_GET['login']==="b")
	{
		$_SESSION['korisnik']='';
		$message['korisnik'] = $_SESSION['korisnik'];
		$_SESSION['logiran']=0;
		sendJSONandExit( $message );
	}

}

if(isset($_GET['vrijeme']) && !isset($_GET['novac']))
{

  $db=DB::getConnection();
  try
  {
    $st = $db->prepare( 'SELECT ime, koeficijent, id FROM Utrke WHERE vrijeme= :vr' );
    $st->execute( array( 'vr' => $_GET['vrijeme'] ) );
  }
  catch( PDOException $e ) { echo 'Greška:' . $e->getMessage() ; return; }
  $message = [];
  $message[ 'utrka' ] = [];
  foreach ($st->fetchAll() as $row)
  {

		$message[ 'utrka' ][] = array( 'ime' => $row['ime'], 'koeficijent' => $row['koeficijent'], 'id' => $row['id'] );


  }


sendJSONandExit( $message );
}

if(isset($_GET['novac']) && isset($_GET['id_konja']))
{
	$uplata=0;
	$db=DB::getConnection();
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
	$ostatak= $uplata-(int)$_GET['novac'];
	if($ostatak>=0)
	{
		$message = [];
		$message['nema_iznosa']=0;
		$message['ostatak']=$ostatak;
		$naziv=$_SESSION['korisnik'];

	$db=DB::getConnection();

  $st = $db->exec( "UPDATE Igraci SET uplata = '$ostatak' WHERE nadimak ='$naziv' " );

	$db=DB::getConnection();
  try
  {
    $st = $db->prepare( 'SELECT ime, koeficijent FROM Utrke WHERE id= :id' );
    $st->execute( array( 'id' =>$_GET['id_konja'] ) );
  }
  catch( PDOException $e ) { echo 'Greška:' . $e->getMessage() ; return; }


  foreach ($st->fetchAll() as $row)
  {

		$message[ 'konj' ][] = array( 'ime' => $row['ime'], 'koeficijent' => $row['koeficijent'] );
		$koef=$row['koeficijent'];
		$ime=$row['ime'];


  }
	$message['KOEF']=$koef;
	$message['LOVA']=(int)$_GET['novac'];
	$dobitak = $koef * (int)$_GET['novac'];
	$message['DOBITAK']=$dobitak;
	$listic = rand(10000,99999);
	$koristi=$_SESSION['korisnik'];

	$db=DB::getConnection();
	try
	{
		$st = $db->exec( "INSERT INTO Kladionica(nadimak, dobitak, listic, proslo, konj, dobio) VALUES ('".$koristi."', '".$dobitak."', '".$listic."', '0','".$ime."', '0'  )" );

	}
	catch( PDOException $e ) { echo 'Greška:' . $e->getMessage() ; return; }

	$message['listic']=$listic;
	$message['konj']=$ime;
	if($_SESSION['korisnik']===null)
	{
		$message['korisnik']="";
	}
	if($_SESSION['korisnik']!==null)
		$message['korisnik']=$_SESSION['korisnik'];
	$message['dobitak']=$dobitak;

		sendJSONandExit( $message );
	}
	else {

		$message['nema_iznosa']=1;
	}
}

if(isset($_GET['popis']))
{
	$db=DB::getConnection();
  try
  {
    $st = $db->query( 'SELECT ime_konja, datum, vrijeme FROM Statistika' );

  }
  catch( PDOException $e ) { echo 'Greška:' . $e->getMessage() ; return; }
  $message = [];

  foreach ($st->fetchAll() as $row)
  {

		$message[ 'pobjednici' ][] = array( 'ime_konja' => $row['ime_konja'], 'datum' => $row['datum'] , 'vrijeme'=>$row['vrijeme']);



  }
	sendJSONandExit( $message );
}



 ?>
