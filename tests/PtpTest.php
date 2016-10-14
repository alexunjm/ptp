<?php
  use Comiguel\Ptp\Ptp;

  class PtpTest extends PHPUnit_Framework_TestCase
  {
    public function ptp() {
      return $ptp = new Ptp(
            'https://test.placetopay.com/soap/pse/?wsdl',
            '6dd490faf9cb87a9862245da41170ff2',
            '024h1IlD',
            ['github' => 'comiguel']
          );
    }

    public function testGetBankList() {
      $ptp = $this->ptp();
      $result = $ptp->GetBankList();
      print_r($result);
      $this->assertEquals('array', gettype($result));
    }

    public function testTransaction() {
      $transaction = Fixtures::getTransaction();
      $ptp = $this->ptp();
      $result = $ptp->createTransaction($transaction);
      print_r($result);
      $this->assertTrue(true);
    }
  }

?>
