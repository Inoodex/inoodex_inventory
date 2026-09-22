# Project Agent Rules & Safety Guidelines

## Database Safety
- **STRICT PROHIBITION**: NEVER execute `php artisan migrate:fresh`, `php artisan migrate:reset`, `php artisan db:wipe`, or any table-dropping/destructive database commands without the user's explicit written permission.
- Always preserve live/development data. Use non-destructive migrations or query inspections instead.
