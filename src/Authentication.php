<?php

  namespace Comiguel\Ptp;

  class Authentication {
    private $login;
    private $tranKey;
    private $seed;/* Calculado desde función*/
    private $additional;

    public function __construct($login, $tranKey, $additional = []) {
      $this->login = $login;
      date_default_timezone_set('America/Bogota');
      $this->seed = date('c');
      $this->tranKey = sha1($this->seed.$tranKey, false);
      $this->additional = $additional;
    }

    public function toArray() {
      return [
        "login" => $this->login,
        "tranKey" => $this->tranKey,
        "seed" => $this->seed,
        "additional" => $this->additional
      ];
    }
  }

?>