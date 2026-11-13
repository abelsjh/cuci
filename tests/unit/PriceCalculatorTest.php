<?php

namespace Tests\Unit;

use App\Libraries\PriceCalculator;
use CodeIgniter\Test\CIUnitTestCase;
use InvalidArgumentException;

/**
 * @internal
 */
final class PriceCalculatorTest extends CIUnitTestCase
{
    private PriceCalculator $calc;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calc = new PriceCalculator();
    }

    public function testCalculateItemSubtotal(): void
    {
        $this->assertSame(40000.0, $this->calc->calculateItemSubtotal(40000, 1));
        $this->assertSame(100000.0, $this->calc->calculateItemSubtotal(50000, 2));
    }

    public function testCalculateItemSubtotalThrowsExceptionForNegativePrice(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->calc->calculateItemSubtotal(-5000, 1);
    }

    public function testCalculateItemSubtotalThrowsExceptionForZeroOrNegativeQty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->calc->calculateItemSubtotal(40000, 0);
    }

    public function testCalculateGrossTotal(): void
    {
        $items = [
            ['price' => 50000, 'qty' => 1],
            ['price' => 20000, 'qty' => 2], // 40000
        ];
        $this->assertSame(90000.0, $this->calc->calculateGrossTotal($items));
    }

    public function testCalculateNominalDiscount(): void
    {
        $gross = 100000.0;
        $discount = $this->calc->calculateDiscount($gross, 15000, 'nominal');
        $this->assertSame(15000.0, $discount);
    }

    public function testCalculatePercentDiscount(): void
    {
        $gross = 100000.0;
        $discount = $this->calc->calculateDiscount($gross, 10, 'percent'); // 10%
        $this->assertSame(10000.0, $discount);
    }

    public function testDiscountCannotExceedGrossTotal(): void
    {
        $gross = 50000.0;
        // Nominal melebihi gross
        $discountNominal = $this->calc->calculateDiscount($gross, 75000, 'nominal');
        $this->assertSame(50000.0, $discountNominal);

        // Persen melebihi 100%
        $discountPercent = $this->calc->calculateDiscount($gross, 150, 'percent');
        $this->assertSame(50000.0, $discountPercent);
    }

    public function testCalculateTotalCannotBeNegative(): void
    {
        $gross = 50000.0;
        $discount = 50000.0;
        $total = $this->calc->calculateTotal($gross, $discount);
        $this->assertSame(0.0, $total);

        // Jika diskon lebih besar dari gross
        $total2 = $this->calc->calculateTotal($gross, 60000.0);
        $this->assertSame(0.0, $total2);
    }

    public function testValidateCashPaymentWithExactAmount(): void
    {
        $res = $this->calc->validatePayment(50000, 'tunai', 50000);
        $this->assertTrue($res['valid']);
        $this->assertSame(50000.0, $res['paid_amount']);
        $this->assertSame(0.0, $res['change_amount']);
        $this->assertNull($res['error']);
    }

    public function testValidateCashPaymentWithChange(): void
    {
        $res = $this->calc->validatePayment(40000, 'tunai', 50000);
        $this->assertTrue($res['valid']);
        $this->assertSame(50000.0, $res['paid_amount']);
        $this->assertSame(10000.0, $res['change_amount']);
        $this->assertNull($res['error']);
    }

    public function testValidateCashPaymentInsufficientAmountFails(): void
    {
        $res = $this->calc->validatePayment(50000, 'tunai', 30000);
        $this->assertFalse($res['valid']);
        $this->assertNotNull($res['error']);
        $this->assertSame(0.0, $res['change_amount']);
    }

    public function testValidateTransferPaymentExactMatch(): void
    {
        $res = $this->calc->validatePayment(65000, 'transfer', 65000);
        $this->assertTrue($res['valid']);
        $this->assertSame(65000.0, $res['paid_amount']);
        $this->assertSame(0.0, $res['change_amount']);
    }

    public function testValidateTransferPaymentMismatchFails(): void
    {
        $res = $this->calc->validatePayment(65000, 'transfer', 70000);
        $this->assertFalse($res['valid']);
        $this->assertNotNull($res['error']);
    }

    public function testValidateQrisPaymentExactMatch(): void
    {
        $res = $this->calc->validatePayment(30000, 'qris', 30000);
        $this->assertTrue($res['valid']);
        $this->assertSame(30000.0, $res['paid_amount']);
        $this->assertSame(0.0, $res['change_amount']);
    }

    public function testInvalidPaymentMethodFails(): void
    {
        $res = $this->calc->validatePayment(50000, 'crypto', 50000);
        $this->assertFalse($res['valid']);
        $this->assertNotNull($res['error']);
    }
}
