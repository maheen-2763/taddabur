<?php

namespace App\Enums;

enum ActivityEvent: string
{
    case LoginFailed = 'auth.login_failed';
    case Lockout = 'auth.lockout';
    case PasswordReset = 'auth.password_reset';
    case AccountDeleted = 'account.deleted';
    case AdminUserDeleted = 'admin.user_deleted';
    case BackupDeleted = 'backup.deleted';
    case BackupPruned = 'backup.pruned';
    case BackupCreated = 'backup.created';
    case BackupDownloaded = 'backup.downloaded';
    case ReviewHidden = 'review.hidden';
    case ReviewRestored = 'review.restored';
    case ComplaintStatusChanged = 'complaint.status_changed';
    case AdminLogin = 'auth.admin_login';

    case ContentCreated = 'content.created';
    case ContentUpdated = 'content.updated';
    case ContentDeleted = 'content.deleted';
    case ContentRestored = 'content.restored';

    public function label(): string
    {
        return match ($this) {
            self::LoginFailed => 'Login failed',
            self::Lockout => 'Account lockout',
            self::PasswordReset => 'Password reset',
            self::AccountDeleted => 'Account deleted',
            self::AdminUserDeleted => 'User deleted by admin',
            self::BackupCreated => 'Backup created',
            self::BackupDownloaded => 'Backup downloaded',
            self::BackupDeleted => 'Backups deleted',
            self::BackupPruned => 'Old backups pruned',
            self::ReviewHidden => 'Review hidden',
            self::ReviewRestored => 'Review restored',
            self::ComplaintStatusChanged => 'Complaint status changed',
            self::AdminLogin => 'Admin login',
            self::ContentCreated => 'Content created',
            self::ContentUpdated => 'Content updated',
            self::ContentDeleted => 'Content deleted',
            self::ContentRestored => 'Content restored',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::LoginFailed, self::Lockout => 'bg-warning text-dark',
            self::PasswordReset, self::BackupDownloaded => 'bg-info text-dark',
            self::BackupCreated => 'bg-success',
            self::AccountDeleted, self::AdminUserDeleted, self::BackupDeleted, self::BackupPruned => 'bg-danger',
            self::ReviewHidden => 'bg-warning text-dark',
            self::ReviewRestored => 'bg-success',
            self::ComplaintStatusChanged => 'bg-info text-dark',
            self::AdminLogin => 'bg-primary',
            self::ContentCreated, self::ContentRestored => 'bg-success',
            self::ContentUpdated => 'bg-primary',
            self::ContentDeleted => 'bg-danger',
        };
    }
}
