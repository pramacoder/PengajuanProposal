<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class RunExhaustiveTest extends Command
{
    protected $signature = 'test:exhaustive';
    protected $description = 'Run exhaustive blackbox testing on all GET routes';

    public function handle()
    {
        $this->info("============================================================");
        $this->info(" MODUL A: Autentikasi & Public Pages");
        $this->info("============================================================");
        
        $this->testRoute('GET', 'login');
        $this->testRoute('GET', 'register');
        $this->testRoute('GET', 'forgot-password');
        
        $this->info("============================================================");
        $this->info(" MODUL B: Pimpinan PT");
        $this->info("============================================================");
        $this->loginAs('pimpinan@test.com');
        $this->testRoute('GET', 'pimpinan-pt/dashboard');
        $this->testRoute('GET', 'pimpinan-pt/akun');

        $this->info("============================================================");
        $this->info(" MODUL C: Operator");
        $this->info("============================================================");
        $this->loginAs('operator-test@test.com');
        $this->testRoute('GET', 'operator/dashboard');
        $this->testRoute('GET', 'operator/akun');
        $this->testRoute('GET', 'operator/profile');
        $this->testRoute('GET', 'operator/ruang-kontrol');
        $this->testRoute('GET', 'operator/pilih-reviewer');
        $this->testRoute('GET', 'operator/pilih-reviewer-seleksi');
        $this->testRoute('GET', 'operator/hasil-semi-final');
        $this->testRoute('GET', 'operator/hasil-final');
        $this->testRoute('GET', 'operator/form-penilaian');
        $this->testRoute('GET', 'operator/laporan-simbelmawa');

        $this->info("============================================================");
        $this->info(" MODUL D: Mahasiswa");
        $this->info("============================================================");
        $this->loginAs('mahasiswa-test@test.com');
        $this->testRoute('GET', 'mahasiswa/dashboard');
        $this->testRoute('GET', 'mahasiswa/proposal/create');
        $this->testRoute('GET', 'mahasiswa/proposal');
        $this->testRoute('GET', 'mahasiswa/profile');
        $this->testRoute('GET', 'mahasiswa/revisi');

        $this->info("============================================================");
        $this->info(" MODUL E: Dosen");
        $this->info("============================================================");
        $this->loginAs('dosen-test@test.com');
        // GET /dosen/dashboard should redirect
        $this->testRoute('GET', 'dosen/dashboard', [200, 302]);
        $this->testRoute('GET', 'dosen/pendamping/proposal-validasi');
        $this->testRoute('GET', 'dosen/pendamping/hasil-review');
        $this->testRoute('GET', 'dosen/pendamping/hasil-final');
        $this->testRoute('GET', 'dosen/pembimbing/dashboard');
        $this->testRoute('GET', 'dosen/universitas/dashboard');
        $this->testRoute('GET', 'dosen/universitas/validasi-akhir');
        $this->testRoute('GET', 'dosen/profile');

        $this->info("============================================================");
        $this->info(" MODUL F: Reviewer");
        $this->info("============================================================");
        $this->loginAs('reviewer-test1@test.com');
        $this->testRoute('GET', 'reviewer/dashboard');
        $this->testRoute('GET', 'reviewer/review-administratif');
        $this->testRoute('GET', 'reviewer/review-substantif');
        $this->testRoute('GET', 'reviewer/review-substantif-seleksi', [200, 302]); // 302 if closed
        $this->testRoute('GET', 'reviewer/profile');

        $this->info("============================================================");
        $this->info(" POST / PUT / DELETE Endpoint Validation Tests");
        $this->info("============================================================");
        // Pimpinan PT Actions
        $this->loginAs('pimpinan@test.com');
        $this->testRoute('POST', 'pimpinan-pt/akun/mahasiswa', [302, 422], ['email' => '']); // Should fail validation
        // Operator Actions
        $this->loginAs('operator-test@test.com');
        $this->testRoute('POST', 'operator/akun/dosen', [302, 422]); 
        $this->testRoute('POST', 'operator/assign-reviewer', [302, 422]);
        $this->testRoute('POST', 'operator/assign-reviewer-seleksi', [302, 422]);
        $this->testRoute('POST', 'operator/form-penilaian', [302, 422]);
        $this->testRoute('POST', 'operator/laporan-simbelmawa', [302, 422]);
        // Mahasiswa Actions
        $this->loginAs('mahasiswa-test@test.com');
        $this->testRoute('POST', 'mahasiswa/proposal/store', [302, 422]); 
        // Dosen Actions
        $this->loginAs('dosen-test@test.com');
        $this->testRoute('POST', 'dosen/pendamping/proposal/999/validasi', [302, 422, 404]);

        $this->info("\nDone.");
    }

    protected function loginAs($email)
    {
        $user = User::where('email', $email)->first();
        if ($user) {
            Auth::login($user);
            $this->info("✅ Logged in as $email");
        } else {
            $this->error("❌ User not found: $email");
        }
    }

    protected function testRoute($method, $uri, $expectedStatuses = [200], $data = [])
    {
        try {
            $request = Request::create($uri, $method, $data);
            
            // Re-bind request to container
            app()->instance('request', $request);
            
            // Handle request through Kernel
            $kernel = app()->make(\Illuminate\Contracts\Http\Kernel::class);
            $response = $kernel->handle($request);
            
            $status = $response->getStatusCode();
            
            if (in_array($status, $expectedStatuses)) {
                $this->info("✅ $method /$uri -> [$status]");
            } else {
                $this->error("❌ $method /$uri -> [$status] (Expected " . implode(',', $expectedStatuses) . ")");
                if ($status == 500) {
                    $this->error(substr($response->getContent(), 0, 500));
                }
            }
            
            $kernel->terminate($request, $response);
        } catch (\Exception $e) {
            $this->error("❌ $method /$uri -> Exception: " . $e->getMessage());
        }
    }
}
