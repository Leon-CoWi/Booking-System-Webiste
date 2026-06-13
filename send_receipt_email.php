<?php
require_once '../PHPMailer-master/src/PHPMailer.php';
require_once '../PHPMailer-master/src/SMTP.php';
require_once '../PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendRejectionEmail($toEmail, $toName, $bookingData) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'homes.comodo@gmail.com';
        $mail->Password   = 'qryw wnus wsey tzpy';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('homes.comodo@gmail.com', 'Comodo Homes');
        $mail->addAddress($toEmail, $toName);

        $mail->isHTML(true);
        $mail->Subject = 'Booking Update - Comodo Homes';

        $mail->Body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; border: 1px solid #ddd; border-radius: 10px; overflow: hidden;'>

            <div style='background: #00071d; padding: 24px; text-align: center;'>
                <h1 style='color: #c9a84c; margin: 0; font-size: 22px;'>&#9671; Comodo Homes</h1>
                <p style='color: #aaa; margin: 6px 0 0;'>Booking Status Update</p>
            </div>

            <div style='padding: 24px; background: #fff;'>
                <p style='font-size: 16px;'>Dear <strong>{$bookingData['customerName']}</strong>,</p>
                <p>We regret to inform you that your booking request has been <strong style='color: #e53e3e;'>rejected</strong>. Here are the details of your booking:</p>

                <table style='width: 100%; border-collapse: collapse; margin-top: 16px;'>
                    <tr style='background: #f5f5f5;'>
                        <td style='padding: 10px; font-weight: bold; width: 40%;'>Booking ID</td>
                        <td style='padding: 10px;'>#{$bookingData['bookingID']}</td>
                    </tr>
                    <tr>
                        <td style='padding: 10px; font-weight: bold;'>Room</td>
                        <td style='padding: 10px;'>{$bookingData['roomNumber']}</td>
                    </tr>
                    <tr style='background: #f5f5f5;'>
                        <td style='padding: 10px; font-weight: bold;'>Location</td>
                        <td style='padding: 10px;'>{$bookingData['location']}</td>
                    </tr>
                    <tr>
                        <td style='padding: 10px; font-weight: bold;'>Check-in</td>
                        <td style='padding: 10px;'>{$bookingData['checkIn']}</td>
                    </tr>
                    <tr style='background: #f5f5f5;'>
                        <td style='padding: 10px; font-weight: bold;'>Check-out</td>
                        <td style='padding: 10px;'>{$bookingData['checkOut']}</td>
                    </tr>
                    <tr>
                        <td style='padding: 10px; font-weight: bold;'>Total Amount</td>
                        <td style='padding: 10px; color: #c9a84c; font-weight: bold;'>&#8369;" . number_format($bookingData['total'], 2) . "</td>
                    </tr>
                    <tr style='background: #f5f5f5;'>
                        <td style='padding: 10px; font-weight: bold;'>Status</td>
                        <td style='padding: 10px; color: #e53e3e; font-weight: bold;'>&#10007; Rejected</td>
                    </tr>
                </table>

                <p style='margin-top: 24px;'>If you believe this is a mistake or would like to make another reservation, please contact us at <strong>0943-593-4480</strong> or email us at <strong>analiza.riton@yahoo.com</strong>.</p>

                <p>We apologize for the inconvenience and hope to serve you in the future.</p>
            </div>

            <div style='background: #00071d; padding: 16px; text-align: center;'>
                <p style='color: #aaa; font-size: 12px; margin: 0;'>Casa Fam: Brgy. 48-B, Cabungaan South, Sitio 6, Laoag City</p>
                <p style='color: #aaa; font-size: 12px; margin: 4px 0 0;'>V.F. Riton: Brgy. 6, Romero Street, Laoag City</p>
                <p style='color: #555; font-size: 11px; margin: 8px 0 0;'>&copy; Comodo Homes. All Rights Reserved.</p>
            </div>

        </div>
        ";

        $mail->send();
        return true;

    } catch (Exception $e) {
        return false;
    }
}

function sendPaymentConfirmEmail($toEmail, $toName, $bookingData) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'homes.comodo@gmail.com';
        $mail->Password   = 'qryw wnus wsey tzpy';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('homes.comodo@gmail.com', 'Comodo Homes');
        $mail->addAddress($toEmail, $toName);

        $mail->isHTML(true);
        $mail->Subject = 'Payment Confirmed - Comodo Homes';

        $mail->Body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; border: 1px solid #ddd; border-radius: 10px; overflow: hidden;'>

            <div style='background: #00071d; padding: 24px; text-align: center;'>
                <h1 style='color: #c9a84c; margin: 0; font-size: 22px;'>&#9671; Comodo Homes</h1>
                <p style='color: #aaa; margin: 6px 0 0;'>Payment Confirmation</p>
            </div>

            <div style='padding: 24px; background: #fff;'>
                <p style='font-size: 16px;'>Dear <strong>{$bookingData['customerName']}</strong>,</p>
                <p>Great news! Your payment has been confirmed. Your booking is now active. Here are your booking details:</p>

                <table style='width: 100%; border-collapse: collapse; margin-top: 16px;'>
                    <tr style='background: #f5f5f5;'>
                        <td style='padding: 10px; font-weight: bold; width: 40%;'>Booking ID</td>
                        <td style='padding: 10px;'>#{$bookingData['bookingID']}</td>
                    </tr>
                    <tr>
                        <td style='padding: 10px; font-weight: bold;'>Room</td>
                        <td style='padding: 10px;'>{$bookingData['roomNumber']}</td>
                    </tr>
                    <tr style='background: #f5f5f5;'>
                        <td style='padding: 10px; font-weight: bold;'>Location</td>
                        <td style='padding: 10px;'>{$bookingData['location']}</td>
                    </tr>
                    <tr>
                        <td style='padding: 10px; font-weight: bold;'>Check-in</td>
                        <td style='padding: 10px;'>{$bookingData['checkIn']}</td>
                    </tr>
                    <tr style='background: #f5f5f5;'>
                        <td style='padding: 10px; font-weight: bold;'>Check-out</td>
                        <td style='padding: 10px;'>{$bookingData['checkOut']}</td>
                    </tr>
                    <tr>
                        <td style='padding: 10px; font-weight: bold;'>Nights</td>
                        <td style='padding: 10px;'>{$bookingData['nights']}</td>
                    </tr>
                    <tr style='background: #f5f5f5;'>
                        <td style='padding: 10px; font-weight: bold;'>Guests</td>
                        <td style='padding: 10px;'>{$bookingData['guests']}</td>
                    </tr>
                    <tr>
                        <td style='padding: 10px; font-weight: bold;'>Payment Method</td>
                        <td style='padding: 10px;'>{$bookingData['paymentMethod']}</td>
                    </tr>
                    <tr style='background: #f5f5f5;'>
                        <td style='padding: 10px; font-weight: bold;'>Total Amount</td>
                        <td style='padding: 10px; color: #c9a84c; font-weight: bold;'>&#8369;" . number_format($bookingData['total'], 2) . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 10px; font-weight: bold;'>Status</td>
                        <td style='padding: 10px; color: green; font-weight: bold;'>&#10003; Paid</td>
                    </tr>
                </table>

                <p style='margin-top: 24px;'>We look forward to welcoming you! For any questions, please contact us at <strong>0943-593-4480</strong> or email us at <strong>analiza.riton@yahoo.com</strong>.</p>
            </div>

            <div style='background: #00071d; padding: 16px; text-align: center;'>
                <p style='color: #aaa; font-size: 12px; margin: 0;'>Casa Fam: Brgy. 48-B, Cabungaan South, Sitio 6, Laoag City</p>
                <p style='color: #aaa; font-size: 12px; margin: 4px 0 0;'>V.F. Riton: Brgy. 6, Romero Street, Laoag City</p>
                <p style='color: #555; font-size: 11px; margin: 8px 0 0;'>&copy; Comodo Homes. All Rights Reserved.</p>
            </div>

        </div>
        ";

        $mail->send();
        return true;

    } catch (Exception $e) {
        return false;
    }
}

function sendReceiptEmail($toEmail, $toName, $bookingData) {
    $mail = new PHPMailer(true);

    try {
        // SMTP Settings — use your email provider
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'homes.comodo@gmail.com'; // your Gmail
        $mail->Password   = 'qryw wnus wsey tzpy';    // Gmail App Password
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Sender & Recipient
        $mail->setFrom('homes.comodo@gmail.com', 'Comodo Homes');
        $mail->addAddress($toEmail, $toName);

        // Email Content
        $mail->isHTML(true);
        $mail->Subject = 'Booking Confirmation - Comodo Homes';

        $mail->Body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; border: 1px solid #ddd; border-radius: 10px; overflow: hidden;'>
            
            <div style='background: #00071d; padding: 24px; text-align: center;'>
                <h1 style='color: #c9a84c; margin: 0; font-size: 22px;'>&#9671; Comodo Homes</h1>
                <p style='color: #aaa; margin: 6px 0 0;'>Booking Confirmation</p>
            </div>

            <div style='padding: 24px; background: #fff;'>
                <p style='font-size: 16px;'>Dear <strong>{$bookingData['customerName']}</strong>,</p>
                <p>Thank you for choosing Comodo Homes! Your booking has been received and is currently being reviewed. Below are your booking details:</p>

                <table style='width: 100%; border-collapse: collapse; margin-top: 16px;'>
                    <tr style='background: #f5f5f5;'>
                        <td style='padding: 10px; font-weight: bold; width: 40%;'>Booking ID</td>
                        <td style='padding: 10px;'>#{$bookingData['bookingID']}</td>
                    </tr>
                    <tr>
                        <td style='padding: 10px; font-weight: bold;'>Room</td>
                        <td style='padding: 10px;'>{$bookingData['roomType']} - {$bookingData['roomNumber']}</td>
                    </tr>
                    <tr style='background: #f5f5f5;'>
                        <td style='padding: 10px; font-weight: bold;'>Location</td>
                        <td style='padding: 10px;'>{$bookingData['location']}</td>
                    </tr>
                    <tr>
                        <td style='padding: 10px; font-weight: bold;'>Check-in</td>
                        <td style='padding: 10px;'>{$bookingData['checkIn']}</td>
                    </tr>
                    <tr style='background: #f5f5f5;'>
                        <td style='padding: 10px; font-weight: bold;'>Check-out</td>
                        <td style='padding: 10px;'>{$bookingData['checkOut']}</td>
                    </tr>
                    <tr>
                        <td style='padding: 10px; font-weight: bold;'>Nights</td>
                        <td style='padding: 10px;'>{$bookingData['nights']}</td>
                    </tr>
                    <tr style='background: #f5f5f5;'>
                        <td style='padding: 10px; font-weight: bold;'>Guests</td>
                        <td style='padding: 10px;'>{$bookingData['guests']}</td>
                    </tr>
                    <tr>
                        <td style='padding: 10px; font-weight: bold;'>Payment Method</td>
                        <td style='padding: 10px;'>{$bookingData['paymentMethod']}</td>
                    </tr>
                    <tr style='background: #f5f5f5;'>
                        <td style='padding: 10px; font-weight: bold;'>Total Amount</td>
                        <td style='padding: 10px; color: #c9a84c; font-weight: bold;'>₱" . number_format($bookingData['total'], 2) . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 10px; font-weight: bold;'>Status</td>
                        <td style='padding: 10px; color: orange;'>Pending</td>
                    </tr>
                </table>

                " . (!empty($bookingData['notes']) ? "<p style='margin-top: 16px;'><strong>Special Requests:</strong> {$bookingData['notes']}</p>" : "") . "

                <p style='margin-top: 24px;'>Our team will review your booking and confirm it shortly. For any questions, please contact us at <strong>0943-593-4480</strong> or email us at <strong>analiza.riton@yahoo.com</strong>.</p>

                <p>Thank you for booking with us. We look forward to welcoming you!</p>
            </div>

            <div style='background: #00071d; padding: 16px; text-align: center;'>
                <p style='color: #aaa; font-size: 12px; margin: 0;'>Casa Fam: Brgy. 48-B, Cabungaan South, Sitio 6, Laoag City</p>
                <p style='color: #aaa; font-size: 12px; margin: 4px 0 0;'>V.F. Riton: Brgy. 6, Romero Street, Laoag City</p>
                <p style='color: #555; font-size: 11px; margin: 8px 0 0;'>&copy; Comodo Homes. All Rights Reserved.</p>
            </div>

        </div>
        ";

        $mail->send();
        return true;

    } catch (Exception $e) {
        return false;
    }
}