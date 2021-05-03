<?php

namespace App\SignatureVerify;
use \Spatie\WebhookClient\SignatureValidator\SignatureValidator;
use Illuminate\Http\Request;
use \Spatie\WebhookClient\WebhookConfig;

class SignatureVerification implements SignatureValidator {
  public function isValid(Request $request, WebhookConfig $config): bool {
    return true;
  }
}


?>