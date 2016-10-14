<?php
  use Comiguel\Ptp\Ptp;
  use Comiguel\Ptp\Validation;

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

    // public function testNothing(){
    //   print_r(new Datetime("now", new Datetimezone("America/Bogota")));
    //   $this->assertTrue(true);
    // }

    // public function testAuthentication() {
    //   $ptp = $this->ptp();
    //   print_r($ptp->getAuthentication()->toArray());
    //   $this->assertEquals(
    //     '6dd490faf9cb87a9862245da41170ff2',
    //     $ptp->getAuthentication()->toArray()['login']
    //   );
    // }

    // public function testGetBankList() {
    //   $ptp = $this->ptp();
    //   $result = $ptp->GetBankList();
    //   print_r($result);
    //   $this->assertEquals('array', gettype($result));
    // }

    // public function testTransaction() {
    //   $transaction = Fixtures::getTransaction();
    //   $ptp = $this->ptp();
    //   $result = $ptp->createTransaction($transaction);
    //   print_r($result);
    //   $this->assertTrue(true);
    // }

    public function testTransactionInformation() {
      $ptp = $this->ptp();
      $result = $ptp->getTransactionInformation(1442699586);
      print_r($result);
      $this->assertTrue(true);
    }
  }

?>