<?php

require_once 'lutrija_db.php';

if(!isset($_SESSION))
  {
      session_start();

  }


if( !isset( $_GET['niz'] ) || !preg_match( '/^[a-z]{20}$/', $_GET['niz'] ) )
	exit( 'Greška u definiranju niza. Molimo, ponovno se registrirajte.' );

$db = DB::getConnection();

try
{
	$st = $db->prepare( 'SELECT * FROM Igraci WHERE niz=:ni' );
	$st->execute( array( 'ni' => $_GET['niz'] ) );
}
catch( PDOException $e ) { exit( 'Greška u bazi: ' . $e->getMessage() ); }

$row = $st->fetch();

if( $st->rowCount() !== 1 )
	exit( 'Taj registracijski niz ima ' . $st->rowCount() . 'korisnika, a treba biti točno 1 takav.' );
else
{

	try
	{
		$st = $db->prepare( 'UPDATE Igraci SET kliknuo=1 WHERE niz=:ni' );
		$st->execute( array( 'ni' => $_GET['niz'] ) );
	}
	catch( PDOException $e ) { exit( 'Greška u bazi: ' . $e->getMessage() ); }


?>
  <p>
		Registracija je uspješno provedena.<br />
		Sada se možete ulogirati na ovoj stranici, ili posjetite <a href="index.php">početnu stranicu</a>.
	</p>
<?php
		exit();
}

?>
