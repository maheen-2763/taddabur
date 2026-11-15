# Changelog

All notable changes to the app and admin panel are recorded here.

## [Unreleased]

### Added

- Admin Activity Log: failed login, lockout, password reset, account deletion, admin user deletion, admin sign-in and backup events (no email or IP is stored)
- Admin Changelog page
- Admin can delete a user after confirming their own password (uses `DeleteUserAccount`)
- Backups page: total size, Auto/Manual badge and delete selected
- User feedback page with star rating and complaints; admin Reviews and Complaints pages
- Admin content changes (stories, chapters, prophets, daily content) are recorded in the Activity Log
- Changelog page shows recent git commits with the files each one added, modified or deleted
- Changelog page paginates recent commits
- Changelog page shows commit type badges, commit times (IST) and when the list was last built

### Changed

- Auto-backup rotation now removes both the file and the database row safely
- Success messages fade out automatically

### Format to follow while commiting in Git

- feat: log admin content changes in the activity log
- fix: ignore deleted_at and computed fields in content log updates
- style: make the admin sidebar scrollable
- refactor: behaviour same, clean code
- chore : Config Cleanup
- test: cover git history parsing and file counts
- docs: update changelog for admin module
