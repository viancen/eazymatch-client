<?php
session_start();
include( '../php53.class.php' );
$em = new Eazymatch53Client( '--KEY--', '--SECRET--', 'INSTANTIE' );

$url = strtok( $_SERVER["REQUEST_URI"], '?' );

if ( isset( $_GET['uitloggen'] ) ) {
	$_SESSION['loginToken'] = null;
	header( 'location: ' . $url . '' );
	die();
}
/**
 * 'MulderLeon',
 * '3caKzm3w'
 */
/**
 * waarden die door het formulier gepost worden oppakken en hiermee inloggen op eazymatch
 * SESSION bewaart de token wanneer er succesvol is ingelogd, maar bij iedere poging wordt deze sessie ge-reset
 */
if ( ! empty( $_POST ) ) {

	//reset sessie
	$_SESSION['loginToken'] = null;

	$username = $_POST['Username'];
	$password = $_POST['Password'];

	//token proberen op te halen met logingegegevens
	$res = $em->call( 'session/getUserToken', [
		$username,
		$password
	] );

	//als het resultaat leeg is waren de gegevens niet goed
	if ( empty( $res['result'] ) ) {

		//bezoeker doorsturen naar het formulier met een melding dat het niet gelukt is
		header( 'Location: ' . $url . '?loginFailed=true' );
		die();
	} else {
		//inloggen is gelukt, de token opslaan in een sessie, zodat dit bewaard  wordt tijdens de rest van het bezoek
		$_SESSION['loginToken'] = $res['result'];
		//doorsturen naar succes pagina (of account pagina).
		header( 'Location: ' . $url . '?loginSuccess=true' );
		die();
	}
}

//als er een sessie aanwezig is, deze gebruiken voor Eazymatch
if ( isset( $_SESSION['loginToken'] ) && ! empty( $_SESSION['loginToken'] ) ) {

	$em->setToken( $_SESSION['loginToken'] );

	//gegevens van de ingelogde persoon ophalen
	$profileData = $em->call( 'applicant/getProfilePrivate' );
	if ( ! isset( $profileData['result'] ) ) {
		unset( $_SESSION );
		header( 'Location: ' . $url . '?sessieVerlopen=true' );
		die();
	}
	$profileSummaryData = $em->call( 'applicant/getSummaryPrivate' );

	$profileData        = $profileData['result'];
	$profileSummaryData = $profileSummaryData['result'];
}
?>
<html>
<head>
    <meta charset="utf-8"/>
    <title>EazyMatch data ophalen via login</title>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <style>
        .wrapper {
            margin-top: 80px;
            margin-bottom: 20px;
        }

        .form-signin {
            max-width: 420px;
            padding: 30px 38px 66px;
            margin: 0 auto;
            background-color: #eee;
            border: 3px dotted rgba(0, 0, 0, 0.1);
        }

        .form-signin-heading {
            text-align: center;
            margin-bottom: 30px;
        }

        .form-control {
            position: relative;
            font-size: 16px;
            height: auto;
            padding: 10px;
        }

        input[type="text"] {
            margin-bottom: 0px;
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
        }

        input[type="password"] {
            margin-bottom: 20px;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }

    </style>
</head>
<body>

<div class="container">
    <div class="wrapper">
		<?php
		//als iemand is ingelogd alle gegevens in een tabel:
		if ( isset( $profileData ) ) {
			?>

			<?php

			if ( ! empty( $profileSummaryData['Person']['Picture'] ) ) {
				?>
                <img src="data:image/png;base64,<?php echo $profileSummaryData['Person']['Picture']['content'] ?>"/><br/>
                <h2>Welkom <?php echo $profileData['Person']['fullname'] ?></h2>
                <a href="<?php echo $url ?>?uitloggen">Klik hier om uit te loggen</a>
				<?php
			} ?>
            <h3>$profileData</h3>
            <table class="table">
				<?php
				foreach ( $profileData as $key => $value ) {
					?>
                    <tr>
                        <td><strong><?php echo $key ?></strong></td>
                        <td>
                            <pre><?php var_dump( $value ) ?></pre>
                        </td>
                    </tr>
					<?php
				}
				?>
            </table>

            <h3>$profileSummaryData</h3>
            <table class="table">
				<?php
				foreach ( $profileSummaryData as $key => $value ) {
					?>
                    <tr>
                        <td><strong><?php echo $key ?></strong></td>
                        <td>
                            <pre><?php var_dump( $value ) ?></pre>
                        </td>
                    </tr>
					<?php
				}
				?>
            </table>


		<?php } else { ?>
            <form action="" method="post" name="Login_Form" class="form-signin">
                <h3 class="form-signin-heading">Inloggen op Eazymatch</h3>

				<?php if ( isset( $_GET['loginFailed'] ) ) {
					?>
                    <div class="alert alert-danger">Inloggen niet gelukt, gebruikersnaam en / of wachtwoord niet correct</div>
					<?php
				} ?>
                <br>

                <input type="text" class="form-control" name="Username" placeholder="Username" required="" autofocus=""/>
                <input type="password" class="form-control" name="Password" placeholder="Password" required=""/>

                <button class="btn btn-lg btn-primary btn-block" name="Submit" value="Login" type="Submit">Login</button>
            </form>
		<?php } ?>
    </div>
</div>

</body>
</html>