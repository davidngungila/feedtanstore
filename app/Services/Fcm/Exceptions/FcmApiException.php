<?php

namespace App\Services\Fcm\Exceptions;

/**
 * Thrown when FCM responds with a non-retryable API error (e.g. malformed payload).
 */
class FcmApiException extends FcmException {}
