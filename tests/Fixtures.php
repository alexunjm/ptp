<?php

class Fixtures {

  public static function getPerson() {
    return [
      "document" => "123456789",
      "documentType" => "CC",
      "firstName" => "Pedro",
      "lastName" => "Pérez",
      "company" => "Independiente",
      "emailAddress" => "pedroperez@mail.com",
      "address" => "Calle 1 # 2 - 3",
      "city" => "Barranquilla",
      "province" => "Atlántico",
      "country" => "Colombia",
      "phone" => "3184623",
      "mobile" => "3004579663"
    ];
  }

  public static function getTransaction() {
    return [
      "bankCode" => "1022",
      "bankInterface" => "0",
      "returnURL" => "https://www.directv.com.co/midirectv/inicio-pospago",
      "reference" => "1sd23f1sd856f1",
      "description" => "Pay Per View DirectTV",
      "language" => "ES",
      "currency" => "COP",
      "totalAmount" => rand(1, 100000),
      "taxAmount" => 0.32,
      "devolutionBase" => 0,
      "tipAmount" => 0,
      "payer" => Fixtures::getPerson(),
      "ipAddress" => "192.168.1.15",
      "userAgent" => "Google Chrome 53 x64",
      "additionalData" => [
        "github" => "comiguel"
      ]
    ];
  }

  public static function getCredit() {
    return [
        "entityCode" => "1r2k3s45we78",
        "serviceCode" => "q73d84weg9a",
        "amountValue" => rand(1, 50000),
        "taxValue" => 0.16,
        "description" => "Gasto regular"
      ];
  }

  public static function getCredits($n) {
    $i = 0;
    $credits = [];
    while ($i < $n) {
      $credits[] = Fixtures::getCredit();
      $i++;
    }
    return $credits;
  }
}

?>