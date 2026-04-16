# Transition to Real Users Plan

To transition the NORSU BSIT Repository from testing to real users, follow these steps:

## 1. Clear Test Data (Optional)
If you want to remove the sample projects and test users (`admin@example.com`, `adviser@example.com`, `student@example.com`), run the following command in your terminal:
```bash
php artisan migrate:fresh
```
*Note: This will delete everything. You will need to create your first admin account afterward.*

## 2. Create Your First Real Admin Account
You can create your real administrator account by running this command:
```bash
php artisan tinker
```
Then paste this code (replace with your details):
```php
\App\Models\User::create([
    'name' => 'Your Actual Name',
    'email' => 'your@real-email.com',
    'password' => \Illuminate\Support\Facades\Hash::make('your-secure-password'),
    'role' => 'admin',
    'is_active' => true,
]);
```

## 3. Verify Registration Flow
The registration page at `/register` is now fully functional and will automatically assign the **Student** role to anyone who signs up. They will be immediately redirected to their **Student Library**.

## 4. Invite Faculty
As an Admin, you can add Faculty Advisers through the **Admin Dashboard > Manage Users** section. Once added, they can log in and start reviewing project submissions.

## 5. Security Checklist
- [ ] Change the default `admin@example.com` password if you kept it.
- [ ] Ensure `APP_DEBUG` is set to `false` in your `.env` if you are hosting this on a real server.
- [ ] Update the `APP_URL` in `.env` to match your real domain or IP.
