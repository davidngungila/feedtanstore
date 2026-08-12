<?php

namespace App\Services\Fcm\Exceptions;

/**
 * Thrown when FCM rejects the OAuth access token or service-account credentials.
 */
class FcmUnauthorizedException extends FcmException {}
