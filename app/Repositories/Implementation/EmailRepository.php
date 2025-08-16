<?php

namespace App\Repositories\Implementation;

use App\Repositories\Contracts\EmailRepositoryInterface;
use App\Models\EmailSMTPSettings;
use PHPMailer\PHPMailer\PHPMailer;
use Illuminate\Support\Facades\Storage;

//use Your Model

/**
 * Class ActionsRepository.
 */
class EmailRepository  implements EmailRepositoryInterface
{

    public function sendEmail($attribute)
    {
        $smtpSettings = EmailSMTPSettings::where('isDefault', 1)->first();

        if ($smtpSettings) {
            $mail = new  PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = $smtpSettings['host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $smtpSettings['userName'];
            $mail->Password   = $smtpSettings['password'];
            $mail->SMTPSecure = $smtpSettings['encryption'];
            $mail->Port       = $smtpSettings['port'];
            $mail->addAddress($attribute['to_address']);
            $mail->setFrom($smtpSettings['fromEmail'], $smtpSettings['fromName'] ?? $smtpSettings['fromEmail']);
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = $attribute['subject'];
            $mail->Body    = $attribute['message'];
            $mail->AltBody = $attribute['message'];
            $mail->Sendmail   = '/usr/sbin/sendmail -bs';

            if ($attribute['path'] != null) {
                $file_contents = Storage::disk($attribute['location'])->get($attribute['doc_url']);
                $mail->addStringAttachment($file_contents, $attribute['file_name']);
            }
            $mail->send();
        }
    }
}
