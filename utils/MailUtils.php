<?php


namespace utils;

use Entities\Customers;
use PHPMailer\PHPMailer\PHPMailer;

require_once join(DIRECTORY_SEPARATOR, ['vendor', 'autoload.php']);

//require_once join(DIRECTORY_SEPARATOR, ['vendor', 'phpmailer', 'phpmailer', 'src', 'PHPMailer.php']);

class MailUtils
{
 public static function sendActivationMail($mailAddress, $lastName, $code, $language="fr") {

    $mail = self::initMailer();
     $mail->From = "info@toncopilote.com";
     $mail->FromName = "OuiLift";
     $mail->addAddress($mailAddress, $lastName);

     $mail->isHTML(true);

     $mail->Subject = $language === "fr" ? "Activer votre compte OuiLift" : "Activate your OuiLift account";
     $mail->Body = $language === "fr" ? self::messageFr($mailAddress, $lastName, $code) : self::messageEn($mailAddress, $lastName, $code);
     $mail->AltBody = "This is the plain text version of the email content";
     $mail->send();

 }

    public static function sendReservationMail(Customers $customer, $reservation, $language="fr") {

        $mail = self::initMailer();
        $mail->From = "info@toncopilote.com";
        $mail->FromName = "OuiLift Reservation";
        $mail->addAddress($customer->eMail, $customer->firstName);

        $mail->isHTML(true);

        $mail->Subject = $language === "fr" ? "Votre reservation OuiLift" : "Your OuiLift reservation";
        $mail->Body = $language === "fr" ? self::reservationFr($reservation, $customer->firstName) : self::reservationEn($reservation, $customer->firstName);
        $mail->AltBody = "This is the plain text version of the email content";
        $mail->send();

    }

 private static function initMailer() {
     $mail = new PHPMailer;
     // $mail->SMTPDebug = 3;
//Set PHPMailer to use SMTP.
     $mail->isSMTP();
//Set SMTP host name
     $mail->Host = "mail32.lwspanel.com";
//Set this to true if SMTP host requires authentication to send email
     $mail->SMTPAuth = true;
//Provide username and password
     $mail->Username = "info@toncopilote.com";
     $mail->Password = "Geoleduc@123";
//If SMTP requires TLS encryption then set it
     $mail->SMTPSecure = "tls";
//Set TCP port to connect to
     $mail->Port = 587;

     return $mail;
 }

 private static function messageFr($mailAddress, $lastName, $code) {
     $message = "Bonjour <i>". $lastName .", </i><br>"
                ." <p>Ce courriel vous permet d'activer votre inscription au service OuiLift<br>"
                ."nom d'utilisateur: <strong>".$mailAddress."</strong><br>"
                ."code d'activation: <strong>".$code."</strong><br></p>"
                ."<p>Pour activer votre compte, connectez vous a l'application mobile et rendez vous &agrave; la section mon compte <br>"
                ."Ceci est un message automatique. Merci de ne pas r&eacute;pondre.</p>"
                ."<p><i>Ce courriel et toutes les informations qu'il contient sont &eacute;tablis &agrave; 
                l'intention exclusive de ses destinataires. Si vous recevez ce courriel par erreur, merci de le supprimer de votre syst&egrave;me.</i></p>
                <p>L'&eacute;quipe OuiLift<br>
                <a href='http://www.ouilift.com'>http://www.ouilift.com</a></p>";
     return $message;

 }

    private static function messageEn($mailAddress, $lastName, $code) {
        $message = "Hello <i>". $lastName .", </i><br>"
            ." <p>This email allows you to activate your OuiLift subscription<br>"
            ."username: <strong>".$mailAddress."</strong><br>"
            ."activation code: <strong>".$code."</strong><br></p>"
            ."<p>To activate your account, connect to the mobile application and go to the section my account <br>"
            ."This is an automatic message. Please do not answer.</p>"
            ."<p><i>This email and all the information it contains are for the exclusive use of its intended recipients. If you receive this email in error, please remove it from your system.</i></p>
                <p>OuiLift Team<br>
                <a href='http://www.ouilift.com'>http://www.ouilift.com</a></p>";
        return $message;

    }

    private static function reservationFr($reservation, $lastName) {
        $message = "Bonjour <i>". $lastName .", </i><br>"
            ." <p>Voici les details de votre r&eacute;servation<br>"
            ."Numero de reservation : <strong>".$reservation['reservation']."</strong><br>"
            ."Place(s) : <strong>".$reservation['remainingPlace']."</strong><br></p>"
            ."Prix : ".$reservation['routePrice']."<br>"
            ."Date : ".$reservation['routeDate']." <br>"
            ."Heure : ".$reservation['hour']."<br>"
            ."D&eacute;part : ".$reservation['fStation']."<br>"
            ."Arriv&eacute;e : ".$reservation['tStation']."<br> </p>"

            ." <p><strong>conduteur et v&eacute;hicule</strong><br>"
            ."conduteur : <strong>".$reservation['firstName']."</strong><br>"
            ."Immatriculation : <strong>".$reservation['registrationNumber']."</strong><br></p>"
            ."Couleur : ".$reservation['colorName']." <br>"
            ."Marque : ".$reservation['brandName']."<br>"
            ."Mod&egrave;le : ".$reservation['modelName']."<br>"
            ."Annee : ".$reservation['year']."<br> </p>"

            ."<p><i>Merci de vous presenter au lieu de rendez-vous dix minutes en avance</i></p>"


                ."<p>L'&eacute;quipe OuiLift<br>
                <a href='http://www.ouilift.com'>http://www.ouilift.com</a></p>";
        return $message;

    }

    private static function reservationEn($reservation, $lastName) {
        $message = "Hello <i>". $lastName .", </i><br>"
            ." <p>Your reservation detail<br>"
            ."Reservation Number : <strong>".$reservation['reservation']."</strong><br>"
            ."Place(s) : <strong>".$reservation['remainingPlace']."</strong><br></p>"
            ."Price : ".$reservation['routePrice']."<br>"
            ."Date : ".$reservation['routeDate']." <br>"
            ."Hour : ".$reservation['hour']."<br>"
            ."Departure : ".$reservation['fStation']."<br>"
            ."Arrival : ".$reservation['tStation']."<br> </p>"

            ." <p><strong>Driver and car</strong><br>"
            ."Driver : <strong>".$reservation['firstName']."</strong><br>"
            ."Car Registration : <strong>".$reservation['registrationNumber']."</strong><br></p>"
            ."Color : ".$reservation['colorName']." <br>"
            ."Brand : ".$reservation['brandName']."<br>"
            ."Model : ".$reservation['modelName']."<br>"
            ."Year : ".$reservation['year']."<br> </p>"

            ."<p><i>Thank you for coming to the meeting place ten minutes in advance</i></p>"


            ."<p>OuiLift Team<br>
                <a href='http://www.ouilift.com'>http://www.ouilift.com</a></p>";
        return $message;

    }
}