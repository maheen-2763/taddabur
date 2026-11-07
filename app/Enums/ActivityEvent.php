<?php

namespace App\Enums;

enum ActivityEvent: string
{
    case LoginFailed = 'auth.login_failed';
    case Lockout = 'auth.lockout';
    case PasswordReset = 'auth.password_reset';
    case AccountDeleted = 'account.deleted';
    case AdminUserDeleted = 'admin.user_deleted';
}
