# Admin System Setup Instructions

## Initial Setup

1. First, run the setup script to create the admin users table:
   ```bash
   C:\xampp\php\php.exe admin/setup_admin.php
   ```
   This will create the admin_users table and add a default admin user with:
   - Username: admin
   - Password: changeThisPassword123!

2. Immediately after setup, log in to the admin panel at:
   ```
   http://localhost/myapp/admin/login.php
   ```
   using the default credentials.

## Changing Default Password

1. After first login, immediately change your password:
   - Go to Profile Settings (top-right menu)
   - Click "Change Password"
   - Enter your current password and new password
   - Click Submit

## Security Notes

- The default admin password should be changed immediately after setup
- All admin pages are protected with session-based authentication
- CSRF protection is enabled for all forms
- File uploads are validated for type and size
- Admin sessions expire after 2 hours of inactivity

## Troubleshooting

If you encounter issues:
1. Verify XAMPP is running (Apache + MySQL)
2. Check database connection in db.php
3. Ensure all admin/ directory files have proper permissions
4. Clear browser cache and cookies if seeing login issues

## Security Best Practices

1. Use strong passwords (min 12 characters, mix of letters, numbers, symbols)
2. Don't share admin credentials
3. Log out when finished
4. Keep XAMPP and PHP updated
5. Regular backup of the admin_users table

---

Additional: Dashboard & Activity Panel
-------------------------------------
I recently added a redesigned admin dashboard at `admin/dashboard.php` with the following features:

- Left sidebar with quick links to common admin forms.
- Top stat cards for Advertisements, Subscribers, and Posts (click any stat to filter recent activities by that type).
- Main analytics area with a doughnut chart and a scrollable Recent Activities list.
- Right-side detail panel: click any activity to view full details (ads, subscribers, posts).

APIs used:
- `api/admin_stats.php` — returns counts for ads, subscribers, and posts.
- `api/admin_activities.php` — returns recent activities (ads, subscribers, posts).
- `api/admin_activity_detail.php` — returns detailed info for a selected activity (expects `id` and `type` query params).

Customizing the dashboard:
- To add/remove sidebar links, edit `admin/dashboard.php` and `admin/includes/header.php`.
- To support additional activity types (e.g., bookings, gallery uploads), add a query in `api/admin_activities.php` and a corresponding detail branch in `api/admin_activity_detail.php`.
- Styling is currently inline for rapid iteration — move styles into `frontend/src/admin.css` and compile with Tailwind if you prefer a consistent, themeable approach.

If you'd like, I can:
- Move inline styles into a dedicated stylesheet and wire it into your Tailwind build.
- Add server-side pagination and search for the activities list.
- Implement more activity detail endpoints (bookings, gallery items, etc.).