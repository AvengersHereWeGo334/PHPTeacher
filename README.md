SecureVault 2.0 - Advanced Access Control System
================================================

DESCRIPTION:
SecureVault is a robust PHP-based authentication system designed for:
- Secure user registration and login
- Protection against brute-force attacks
- Comprehensive activity monitoring
- Session-based data persistence

KEY FEATURES:
✔ User account registration
✔ Secure login authentication
✔ Attempt limiting (5 max attempts)
✔ Real-time activity logging
✔ Responsive Bootstrap 5 interface
✔ Password visibility toggle
✔ Automatic session security
✔ System lockout protection

TECHNICAL DETAILS:
- Backend: PHP 7.4+ (session-based)
- Frontend: Bootstrap 5 + Vanilla JS
- No database required
- Session-based data storage
- Secure cookie settings
- XSS protection headers

INSTALLATION:
1. Upload all files to your web server
2. Ensure PHP 7.4+ is installed
3. Configure PHP session settings if needed
4. Access via web browser

FILE STRUCTURE:
/index.php          - Main application entry point
/auth_manager.php   - Core authentication logic
/functions.php      - Helper functions
/styles.css         - Custom styling
/script.js          - Client-side interactions

SECURITY NOTES:
⚠ This is a demonstration system. For production use:
1. Implement proper password hashing (password_hash())
2. Add CSRF protection tokens
3. Enable HTTPS encryption
4. Consider database storage for user data
5. Implement account verification

CUSTOMIZATION:
To modify the system:

1. Visual Changes:
   - Edit styles.css for colors/layout
   - Modify card designs in security-card class

2. Security Settings:
   - Adjust $max_attempts in auth_manager.php
   - Change session settings in functions.php

3. Functionality:
   - Add new validation rules in functions.php
   - Extend AccessManager class for new features

DEVELOPMENT NOTES:
- All code is commented for easy modification
- Follows PSR-12 coding standards
- No external dependencies except Bootstrap
- Vanilla JavaScript (no jQuery)

LICENSE:
MIT License - Free for educational and personal use.

SUPPORT:
For assistance, please open an issue on GitHub.
