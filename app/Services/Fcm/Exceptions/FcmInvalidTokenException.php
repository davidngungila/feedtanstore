<?php

namespace App\Services\Fcm\Exceptions;

/**
 * Thrown when FCM reports the device token as unregistered or invalid.
 * The caller should deactivate/remove the device token.
 */
class FcmInvalidTokenException extends FcmException {}
