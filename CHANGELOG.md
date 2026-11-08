# Changelog

All notable changes to the app and admin panel are recorded here.

## [Unreleased]

### Added

- Admin Activity Log: failed login, lockout, password reset, account deletion, admin user deletion, admin sign-in and backup events (no email or IP is stored)
- Admin Changelog page
- Admin can delete a user after confirming their own password (uses `DeleteUserAccount`)
- Backups page: total size, Auto/Manual badge and delete selected
- User feedback page with star rating and complaints; admin Reviews and Complaints pages

### Changed

- Auto-backup rotation now removes both the file and the database row safely
- Success messages fade out automatically
