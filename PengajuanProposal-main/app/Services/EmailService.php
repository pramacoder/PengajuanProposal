<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailService
{
    /**
     * Kirim email registrasi
     */
    public function sendRegistrationEmail($email, $nama, $identifier, $password, $role, $emailLogin = null)
    {
        try {
            $subject = 'Kredensial Login - Sistem Pengajuan Proposal PKM';
            $data = [
                'nama' => $nama,
                'identifier' => $identifier,
                'password' => $password,
                'role' => $role,
                'email_login' => $emailLogin ?: 'Email ' . $role . ' di database',
                'login_url' => route('login')
            ];

            Mail::send('emails.registration', $data, function ($message) use ($email, $subject) {
                $message->to($email)
                    ->subject($subject);
            });

            Log::info("Email registrasi berhasil dikirim ke: {$email}");
            return true;

        } catch (\Exception $e) {
            Log::error("Gagal mengirim email registrasi ke {$email}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Kirim email reset password
     */
    public function sendPasswordResetEmail($email, $nama, $identifier, $password, $role, $emailLogin)
    {
        try {
            $subject = 'Reset Password - Sistem Pengajuan Proposal PKM';
            $data = [
                'nama' => $nama,
                'identifier' => $identifier,
                'password' => $password,
                'role' => $role,
                'email_login' => $emailLogin,
                'login_url' => route('login')
            ];

            Mail::send('emails.password_reset', $data, function ($message) use ($email, $subject) {
                $message->to($email)
                    ->subject($subject);
            });

            Log::info("Email reset password berhasil dikirim ke: {$email}");
            return true;

        } catch (\Exception $e) {
            Log::error("Gagal mengirim email reset password ke {$email}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Kirim notifikasi proposal baru ke dosen pembimbing
     */
    public function sendProposalNotificationToDosenPembimbing($dosenEmail, $mahasiswaNama, $proposalJudul)
    {
        try {
            $subject = 'Notifikasi: Mahasiswa Bimbingan Mengajukan Proposal Baru';
            $data = [
                'dosen_email' => $dosenEmail,
                'mahasiswa_nama' => $mahasiswaNama,
                'proposal_judul' => $proposalJudul,
                'dashboard_url' => route('dosen.pembimbing.dashboard')
            ];

            Mail::send('emails.proposal_notification_dosen_pembimbing', $data, function ($message) use ($dosenEmail, $subject) {
                $message->to($dosenEmail)
                    ->subject($subject);
            });

            Log::info("Notifikasi proposal berhasil dikirim ke dosen pembimbing: {$dosenEmail}");
            return true;

        } catch (\Exception $e) {
            Log::error("Gagal mengirim notifikasi ke dosen pembimbing {$dosenEmail}: " . $e->getMessage());
            return false;
        }
    }
}
