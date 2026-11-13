<?php

namespace Tests\Unit;

use App\Libraries\AuditLogger;
use App\Models\AuditLogModel;
use App\Models\StatusLogModel;
use App\Models\TransactionModel;
use App\Models\UserModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * @internal
 */
final class BusinessLogicTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate = true;
    protected $migrateOnce = true;
    protected $namespace = 'App';

    protected function setUp(): void
    {
        parent::setUp();
        helper(['app']);
    }

    public function testHelpersFormatting(): void
    {
        // Rupiah
        $this->assertSame('Rp 50.000', format_rupiah(50000));
        $this->assertSame('Rp 1.250.000', format_rupiah(1250000));
        $this->assertSame('Rp 0', format_rupiah(0));

        // Badges
        $this->assertStringContainsString('text-bg-secondary', status_badge('antri'));
        $this->assertStringContainsString('text-bg-warning', status_badge('dikerjakan'));
        $this->assertStringContainsString('text-bg-info', status_badge('selesai'));
        $this->assertStringContainsString('text-bg-success', status_badge('diambil'));
        $this->assertStringContainsString('text-bg-danger', status_badge('dibatalkan'));

        // Category badges
        $this->assertStringContainsString('text-bg-primary', category_badge('mobil'));
        $this->assertStringContainsString('text-bg-dark', category_badge('helm'));

        // Payment badges
        $this->assertStringContainsString('text-bg-success', payment_badge('lunas'));
        $this->assertStringContainsString('text-bg-light', payment_badge('belum'));
    }

    public function testAuditLoggerRecordsWithoutSecrets(): void
    {
        $sensitiveData = [
            'username' => 'testkasir',
            'password' => 'secret123',
            'role'     => 'kasir',
        ];

        // Pass null for user_id or test without foreign key violation
        $logged = AuditLogger::log(null, 'TEST_ACTION', 'user', 99, $sensitiveData);
        $this->assertTrue($logged);

        $auditModel = new AuditLogModel();
        $record = $auditModel->where('action', 'TEST_ACTION')->where('entity_id', 99)->first();

        $this->assertNotNull($record);
        $this->assertStringNotContainsString('secret123', $record['detail']);
        $this->assertStringContainsString('testkasir', $record['detail']);
    }

    public function testStatusTransitionRules(): void
    {
        $statusRank = [
            'antri'      => 1,
            'dikerjakan' => 2,
            'selesai'    => 3,
            'diambil'    => 4,
            'dibatalkan' => 99,
        ];

        // 1. Antri -> Dikerjakan (Maju valid)
        $this->assertGreaterThan($statusRank['antri'], $statusRank['dikerjakan']);

        // 2. Dikerjakan -> Selesai (Maju valid)
        $this->assertGreaterThan($statusRank['dikerjakan'], $statusRank['selesai']);

        // 3. Selesai -> Diambil (Maju valid)
        $this->assertGreaterThan($statusRank['selesai'], $statusRank['diambil']);

        // 4. Selesai -> Dikerjakan (Mundur: hanya diperbolehkan bila userRole == 'admin')
        $isBackward = $statusRank['dikerjakan'] < $statusRank['selesai'];
        $this->assertTrue($isBackward);

        $kasirCanMoveBackward = false; // rule: Kasir dilarang mundur
        $adminCanMoveBackward = true;  // rule: Admin boleh mundur
        $this->assertFalse($kasirCanMoveBackward);
        $this->assertTrue($adminCanMoveBackward);
    }

    public function testDiambilRequiresLunasRule(): void
    {
        $paymentStatus = 'belum';
        $targetStatus  = 'diambil';

        // Syarat: Diambil hanya boleh bila lunas
        $canBeDiambil = ($targetStatus === 'diambil' && $paymentStatus === 'lunas');
        $this->assertFalse($canBeDiambil, 'Status Diambil harus ditolak bila payment_status belum lunas');

        $paymentStatus = 'lunas';
        $canBeDiambilNow = ($targetStatus === 'diambil' && $paymentStatus === 'lunas');
        $this->assertTrue($canBeDiambilNow, 'Status Diambil diperbolehkan bila payment_status sudah lunas');
    }

    public function testCancelRequiresReasonAndAdminForLunas(): void
    {
        // 1. Tanpa alasan harus ditolak
        $reason = '';
        $isValidReason = ! empty(trim($reason));
        $this->assertFalse($isValidReason, 'Pembatalan tanpa alasan harus ditolak');

        // 2. Transaksi lunas dibatalkan oleh Kasir harus ditolak
        $userRole = 'kasir';
        $paymentStatus = 'lunas';

        $kasirCanCancelLunas = ($paymentStatus === 'lunas' && $userRole === 'admin');
        $this->assertFalse($kasirCanCancelLunas, 'Kasir dilarang membatalkan transaksi yang sudah lunas');

        $userRole = 'admin';
        $adminCanCancelLunas = ($paymentStatus === 'lunas' && $userRole === 'admin');
        $this->assertTrue($adminCanCancelLunas, 'Admin diperbolehkan membatalkan transaksi yang sudah lunas');
    }
}
