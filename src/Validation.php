<?php

  namespace Comiguel\Ptp;

  class Validation {

    public static function getTransactionMandatoryFields() {
      return [
        "bankCode",
        "bankInterface",
        "returnURL",
        "reference",
        "description",
        "language",
        "currency",
        "totalAmount",
        "taxAmount",
        "devolutionBase",
        "tipAmount",
        "payer",
        "ipAddress",
        "userAgent",
        "additionalData"
      ];
    }

    public static function getTransactionOptionalsFields() {
      return [
        "buyer",
        "shipping",
        "additionalData"
      ];
    }

    public static function getPersonMandatoryFields() {
      return [
        "document",
        "documentType",
        "firstName",
        "lastName",
        "company",
        "emailAddress",
        "address",
        "city",
        "province",
        "country",
        "phone",
        "mobile"
      ];
    }

    /*
    * Verifica si un Array dado tiene todos los campos obligatorios.
    Recibe el array con las claves obligatorias y el array a verificar
    Devuelve status => true si los tiene todos, status => false y missing => con los campos faltantes
    */
    public static function verifyFields($mandatories, $given) {
      $missing = [];
      foreach ($mandatories as $key => $value) {
        if (!array_key_exists($value, $given)){
          $missing[] = $value;
        }
      }
      if (count($missing) > 0) {
        return ["status" => false, "missing" => $missing];
      } else {
        return ["status" => true];
      }
    }
  }
?>