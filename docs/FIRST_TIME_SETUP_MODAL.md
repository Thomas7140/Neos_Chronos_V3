# First-Time Setup Modal

When you visit `admin.php` for the first time with no admin users in the database, you'll see this beautiful setup modal:

```
┌─────────────────────────────────────────────────────────────┐
│                                                             │
│         🎉 Welcome to Chronos Stats Admin                   │
│        Create Your First Administrator Account              │
│                                                             │
│  ┌───────────────────────────────────────────────────────┐ │
│  │ Username *                                            │ │
│  │ [________________________________]                    │ │
│  │ Choose a unique username for the administrator account│ │
│  └───────────────────────────────────────────────────────┘ │
│                                                             │
│  ┌───────────────────────────────────────────────────────┐ │
│  │ Password *                                            │ │
│  │ [________________________________]                    │ │
│  │ Use a strong password with mixed case, numbers, and  │ │
│  │ symbols                                               │ │
│  └───────────────────────────────────────────────────────┘ │
│                                                             │
│  ┌───────────────────────────────────────────────────────┐ │
│  │ Confirm Password *                                    │ │
│  │ [________________________________]                    │ │
│  │ Re-enter password                                     │ │
│  └───────────────────────────────────────────────────────┘ │
│                                                             │
│  ┌───────────────────────────────────────────────────────┐ │
│  │ Email Address                                         │ │
│  │ [________________________________]                    │ │
│  │ Optional - for password recovery notifications        │ │
│  └───────────────────────────────────────────────────────┘ │
│                                                             │
│  ┌───────────────────────────────────────────────────────┐ │
│  │ Full Name                                             │ │
│  │ [________________________________]                    │ │
│  │ Optional - display name for the admin panel           │ │
│  └───────────────────────────────────────────────────────┘ │
│                                                             │
│  ┌───────────────────────────────────────────────────────┐ │
│  │ 🔒 Security Information:                              │ │
│  │                                                        │ │
│  │ • Passwords are encrypted using Argon2id (OWASP       │ │
│  │   recommended)                                         │ │
│  │ • Account locks after 5 failed login attempts         │ │
│  │ • All admin actions are logged for security audits    │ │
│  └───────────────────────────────────────────────────────┘ │
│                                                             │
│           [ Create Administrator Account ]                  │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

## Features

- **Smart Detection**: Only appears when the admin database exists but has zero users
- **Auto-Login**: After creating the account, you're automatically logged in
- **One-Time Only**: The modal never appears again after the first admin is created
- **Beautiful UI**: Modern, clean interface with helpful hints
- **Secure**: Passwords are hashed with Argon2id before storage
- **Validation**: Real-time checks for password length and matching

## When Does It Appear?

The modal appears when ALL of these conditions are true:
1. ✅ Admin users table exists in database (`chronos_admin_users`)
2. ✅ Table is empty (no admin users created yet)
3. ✅ User is not already authenticated
4. ✅ Visiting the admin panel (not in iframe mode)

## When Does It NOT Appear?

The modal will NOT show if:
- ❌ Admin users already exist in database (shows normal login)
- ❌ User is already logged in (shows admin panel)
- ❌ Database tables haven't been created yet (shows error)

## Error Handling

The modal includes validation and helpful error messages:

- **"Username and password are required"** - You must fill in both fields
- **"Password must be at least 8 characters long"** - Use a longer password
- **"Passwords do not match"** - The confirmation doesn't match
- **"Failed to create admin user"** - Database error (check logs)

## After Setup

Once you click "Create Administrator Account":
1. Password is hashed with Argon2id
2. Admin record is inserted into database
3. You're automatically logged in
4. Activity is logged as `first_admin_created`
5. Page redirects to admin panel
6. Modal will never appear again (unless you delete all admins)

## Customization

The modal is styled with inline CSS in `admin.php` and includes:
- Responsive design
- Focus states on inputs
- Security information panel
- Professional color scheme
- Clean typography

## Security Notes

- Passwords are NEVER stored in plain text
- Passwords are NEVER sent via .env files (when using modal)
- Argon2id hashing provides maximum security
- Failed creation attempts don't leak information
- Input is sanitized and validated server-side
