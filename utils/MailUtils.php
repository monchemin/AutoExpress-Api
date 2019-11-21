<?php


namespace utils;

use Entities\Customers;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

require_once join(DIRECTORY_SEPARATOR, ['vendor', 'autoload.php']);

//require_once join(DIRECTORY_SEPARATOR, ['vendor', 'phpmailer', 'phpmailer', 'src', 'PHPMailer.php']);

class MailUtils
{
 public static function sendActivationMail($mailAddress, $lastName, $code, $language="fr") {

    $mail = self::initMailer();
     $mail->From = "info@toncopilote.com";
     $mail->FromName = "OuiLift";
     try {
         $mail->addAddress($mailAddress, $lastName);
     } catch (Exception $e) {
     }

     $mail->Subject = $language === "fr" ? "Activer votre compte OuiLift" : "Activate your OuiLift account";
     $mail->Body = $language === "fr" ? self::messageFr($mailAddress, $lastName, $code) : self::messageEn($mailAddress, $lastName, $code);
     try {
         $mail->send();
     } catch (Exception $e) {
     }

 }

    public static function sendReservationMail(Customers $customer, $reservation, $language="fr") {

        $mail = self::initMailer();
        $mail->From = "info@toncopilote.com";
        $mail->FromName = "OuiLift Reservation";
        try {
            $mail->addAddress($customer->eMail, $customer->firstName);
        } catch (Exception $e) {
        }

        $mail->Subject = $language === "fr" ? "Votre reservation OuiLift" : "Your OuiLift reservation";
        $mail->Body = $language === "fr" ? self::reservationFr($reservation, $customer->firstName) : self::reservationEn($reservation, $customer->firstName);

        try {
            $mail->send();
        } catch (Exception $e) {
        }

    }

    public static function sendRouteDeleted($mails, $language='en') {
        $mail = self::initMailer();
        $mail->From = "info@toncopilote.com";
        $mail->FromName = "OuiLift Reservation";
        try {
            foreach ($mails as $address) {
                $mail->addAddress($address);
            }
        } catch (Exception $e) {}

        $mail->Subject = $language === "fr" ? "Annulation Reservation" : "Reservation cancelled";
        $mail->Body = $language === "fr" ? self::routeDeleteFr() : self::routeDeleteEn();
        try {
            $mail->send();
        } catch (Exception $e) {
        }

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
     $mail->isHTML(true);
     $mail->AltBody = "This is the plain text version of the email content";

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
            ."Numero de reservation : <strong>".$reservation['reservationId']."</strong><br>"
            ."Place(s) : <strong>".$reservation['place']."</strong><br></p>"
            ."Prix : ".$reservation['routePrice']."<br>"
            ."Date : ".$reservation['routeDate']." <br>"
            ."Heure : ".$reservation['hour']."<br>"
            ."D&eacute;part : ".$reservation['fStation']."<br>"
            ."Arriv&eacute;e : ".$reservation['tStation']."<br> </p>"

            ." <p><strong>conduteur et v&eacute;hicule</strong><br>"
            ."conduteur : <strong>".$reservation['driverFirstName']."</strong><br>"
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
            ."Reservation Number : <strong>".$reservation['reservationId']."</strong><br>"
            ."Place(s) : <strong>".$reservation['place']."</strong><br></p>"
            ."Price : ".$reservation['routePrice']."<br>"
            ."Date : ".$reservation['routeDate']." <br>"
            ."Hour : ".$reservation['hour']."<br>"
            ."Departure : ".$reservation['fStation']."<br>"
            ."Arrival : ".$reservation['tStation']."<br> </p>"

            ." <p><strong>Driver and car</strong><br>"
            ."Driver : <strong>".$reservation['driverFirstName']."</strong><br>"
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

    private static function routeDeleteFr() {
        $message = "Bonjour <br>"
            ." <p>Une de vos reservations a été annulée en raison de l'annulation de l'itinéraire par le conduction<br>
                Veuillez SVP consulter votre compte pour plus d'information."
            ."Nous sommes d&eacute;sol&eacute;s pour le d&eacute;sagr&eacute;ment caus&eacute;<br></p>"
            ."<p>
                L'&eacute;quipe OuiLift<br>
                <a href='http://www.ouilift.com'>http://www.ouilift.com</a></p>";
        return $message;

    }

    private static function routeDeleteEn() {
        $message = "Hello, "

            ." <p>One of your reservations has been canceled because the driver has canceled the itinerary.<br>
                 please check your account for more information.<p>
                 We are sorry for the inconvenience</p>"
            ."<p>OuiLift Team<br>
                <a href='http://www.ouilift.com'>http://www.ouilift.com</a></p>";
        return $message;

    }
}