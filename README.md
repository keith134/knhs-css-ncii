# KNHS CSS NCII - TESDA Certificate Submission Portal

A modern, 3D-styled Google Forms-style website for collecting TESDA NC II certificate submissions from Kauswagan National High School students.

## 📚 School Information

- **School**: Kauswagan National High School
- **Location**: Kauswagan, Cagayan de Oro
- **Program**: KNHS CSS NCII - Computer Systems Servicing NC II
- **Developed by**: Keith Dandan - ICT 12 Magsaysay

## 🌟 Features

- 🎨 **Beautiful 3D Design** - Modern, colorful interface with animations
- 📝 **Google Forms-style** - Clean, intuitive form layout
- 👤 **Name & Email** - Required applicant information
- 📸 **Certificate Upload** - Drag & drop with image preview
- 📚 **Course Selection** - Multiple TESDA courses available
- 📅 **Date of Completion** - Optional completion date
- 💬 **Additional Message** - Optional message field
- 📧 **Gmail Integration** - Automatic email sending to your Gmail

## 📂 Project Structure

```
knhs-css-ncii-submission/
├── index.html                 # Main submission form
├── submit.php                 # PHP backend for email sending
├── assets/
│   ├── css/
│   │   └── style.css          # 3D styling and animations
│   ├── js/
│   │   └── script.js          # Form handling & Gmail integration
│   └── uploads/               # Temporary file storage (auto-created)
├── config/
│   └── mail-config.php        # SMTP / mail settings
└── README.md                  # This file
```

## 🚀 Quick Start

### Option 1: Gmail Mailto (No Server Required)
Simply open `index.html` in your browser! This method:
- Opens Gmail with pre-filled recipient
- Auto-fills subject and body
- User manually attaches the certificate image
- Works without any server setup

### Option 2: PHP Server (Direct Email with Attachment)
1. **Install a local server** (required for PHP):
   - Windows: [XAMPP](https://www.apachefriends.org/) or [WAMP](https://www.wampserver.com/)
   - Mac: [MAMP](https://www.mamp.info/)

2. **Place files** in your server's web directory:
   - For XAMPP: `C:\xampp\htdocs\knhs\`
   - For WAMP: `C:\wamp64\www\knhs\`

3. **For direct Gmail sending with attachments** (recommended):
   ```bash
   # Install PHPMailer via Composer
   composer require phpmailer/phpmailer
   ```

4. **Configure Gmail**:
   - Go to your Google Account
   - Enable 2-Step Verification
   - Generate an App Password: https://myaccount.google.com/apppasswords
   - Edit `config/mail-config.php` and replace:
     ```php
     define('GMAIL_APP_PASSWORD', 'your_app_password_here');
     ```
     with your actual App Password

5. **Access** the website at: `http://localhost/knhs/`

## 🎯 How It Works

1. User fills out the form (name, email, course)
2. User uploads certificate image
3. User clicks "Submit via Gmail"
4. **If PHP is available**: Certificate is emailed directly with attachment
5. **If PHP is not available**: Gmail opens with pre-filled info, user attaches file manually

### Email Recipient:
```
keithcharlespacatangdandan@gmail.com
```

## 🔧 Customization

### Change Gmail Address
Edit `config/mail-config.php`:
```php
define('GMAIL_ADDRESS', 'your-email@gmail.com');
```

### Add More Courses
Edit the `<select>` element in `index.html`:
```html
<option value="New Course">New Course Name</option>
```

## 📋 Form Fields

| Field | Type | Required |
|-------|------|----------|
| Full Name | Text | Yes |
| Email Address | Email | Yes |
| Course/Program | Dropdown | Yes |
| Date of Completion | Date | No |
| Certificate Picture | File (Image) | Yes |
| Additional Message | Textarea | No |

## 🛠️ Troubleshooting

### PHP not working?
- Make sure Apache/PHP server is running
- Check PHP is installed: `php -v` in terminal
- Check error logs in server directory

### Gmail not opening?
- Check popup blocker settings
- Allow popups for the website

### Email not sending?
- For PHPMailer: Verify App Password is correct
- For basic mail(): Many hosting providers block PHP mail()
- Check spam folder

## 📄 License

This project is for educational purposes.
Created for KNHS CSS NCII - Kauswagan National High School Computer Systems Servicing NC II Program.

---

## ⚡ Important: Automatic Attachment

**For automatic file attachment**, you MUST use PHP with PHPMailer:

1. Install XAMPP/WAMP
2. Run: `composer require phpmailer/phpmailer`
3. Generate App Password at https://myaccount.google.com/apppasswords
4. Update `config/mail-config.php` with your App Password

When properly configured, the certificate image will be automatically attached to the email sent to your Gmail!

The email is sent TO your Gmail (keithcharlespacatangdandan@gmail.com), so you can simply open your Gmail and download the certificate attachment directly!
