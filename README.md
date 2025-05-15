SecureAccess Portal - User Management System
🔒 A PHP-based user authentication and management system with session storage.

📌 Overview
This project is a secure user management portal that allows:
✅ User registration
✅ Login authentication
✅ User data listing
✅ Session persistence

Built with PHP, Bootstrap 5, and vanilla JavaScript for a clean, functional interface.

🚀 Features
✔ User Registration – Store usernames & passwords securely
✔ Login System – Authenticate users with session tracking
✔ Dynamic User Table – View all registered users
✔ Password Visibility Toggle – 👁️ Show/hide passwords
✔ Last Login Tracking – Records when users log in
✔ Responsive Design – Works on mobile & desktop

⚙️ Installation
Requirements:

PHP 7.4+

A web server (Apache/Nginx)

Browser with JavaScript enabled

Setup:

Upload files to your server (index.php + any dependencies).

No database needed (uses PHP sessions).

Open in a browser and start registering users!

📂 Code Structure
/  
├── index.php           # Main application logic & UI  
├── README.md           # This documentation  
└── (No external dependencies required)
🔧 How It Works
1. User Registration
Stores users in $_SESSION['user_data']

Validates input fields

2. User Login
Checks credentials against stored users

Updates last_login timestamp

3. User Management
Displays all registered users in a table

Hidden form fields maintain state

🛡️ Security Notes
⚠ This is a demo system. For production use:

Use password hashing (e.g., password_hash()).

Sanitize all inputs to prevent XSS.

Implement CSRF protection for forms.

📜 License
MIT License - Free for personal and educational use.

📬 Contact
Author: PHPTeacher- AvengersHereWeGo334
GitHub: https://github.com/AvengersHereWeGo334

🎯 Why This Project?
Great for learning PHP sessions

Simple authentication flow example

Easy to extend (add hashing, DB support, etc.)

🚀 Feel free to fork, modify, and improve!
