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

  //   public function testAuthentication() {
  //     $ptp = $this->ptp();
  //     print_r($ptp->getAuthentication()->toArray());
  //     $this->assertEquals(
  //       '6dd490faf9cb87a9862245da41170ff2',
  //       $ptp->getAuthentication()->toArray()['login']
  //     );
  //   }

  //   public function testGetBankList() {
  //     $ptp = $this->ptp();
  //     $result = $ptp->GetBankList();
  //     print_r($result);
  //     $this->assertEquals('array', gettype($result));
  //   }

  //   public function testTransaction() {
  //     $transaction = Fixtures::getTransaction();
  //     $ptp = $this->ptp();
  //     $result = $ptp->createTransaction($transaction);
  //     print_r($result);
  //     $this->assertTrue(true);
  //   }

    public function testTransactionMulticredit() {
      $total = 0;
      $transaction = Fixtures::getTransaction();
      $transaction["credits"] = Fixtures::getCredits(2);
      unset($transaction["totalAmount"]);
      unset($transaction["taxAmount"]);
      print_r($transaction);
      $ptp = $this->ptp();
      $result = $ptp->createTransactionMulticredit($transaction);
      print_r($result);
      $this->assertTrue(true);
    }

  //   public function testTransactionInformation() {
  //     $ptp = $this->ptp();
  //     $result = $ptp->getTransactionInformation(1442699586);
  //     print_r($result);
  //     $this->assertTrue(true);
  //   }

    // public function testNothing(){
    //   var_dump(Validation::getTransactionMultiCreditMandatoryFields());
    //   $this->assertTrue(true);
    // }

  }

?>