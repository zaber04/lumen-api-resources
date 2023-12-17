<?php

namespace Zaber04\LumenApiResources\Enums;

/**
 * Allowed actions
 *
 * // For advanced enum implimentation, check --> bensampo/laravel-enum package
 */
enum ActionEnum: string
{
    case ARCHIVE  = 'archive';
    case DELETE   = 'delete';
    case INSERT   = 'insert';
    case UPDATE   = 'update';
    case REGISTER = 'register';
    case LOGIN    = 'login';
    case LOGOUT   = 'logout';
}
