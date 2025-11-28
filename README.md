# Packing List CMS

A simple, user-friendly Content Management System for creating and managing packing lists for trips and events. Built with PHP and MySQL, featuring user authentication, category organization, and responsive design.

## 🚀 Project Overview

Packing List CMS is a web-based application designed to help users organize and manage their travel packing lists efficiently. The system provides an intuitive interface for creating, editing, and tracking packing progress for various trips and events. Users can categorize items, set quantities, and monitor their packing status in real-time.

## ✨ Key Features

### Core Functionality
- **User Authentication**: Secure registration and login system with password hashing
- **Packing List Management**: Create, edit, and delete packing lists with titles, descriptions, and trip dates
- **Item Management**: Add, edit, delete, and track packing status of individual items
- **Category Support**: Organize items by predefined categories (clothes, electronics, toiletries, etc.)
- **Progress Tracking**: Visual progress indicators showing packing completion percentage
- **Responsive Design**: Mobile-friendly interface that works on all devices

### Security Features
- Password hashing using PHP's `password_hash()` function
- CSRF token protection on all forms
- Input validation and sanitization
- SQL injection prevention through prepared statements
- Session-based authentication

### User Experience
- Clean, modern interface with gradient designs
- Intuitive navigation and user-friendly forms
- Real-time progress tracking with visual indicators
- Mobile-responsive design for on-the-go access
- Quick item status toggling with checkboxes

## 🛠️ Technologies Used

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+ or MariaDB 10.2+
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Styling**: Custom CSS with responsive design
- **Security**: PDO prepared statements, CSRF protection, password hashing
- **Server**: Apache/Nginx compatible with .htaccess support

## 👥 User Roles

### Regular Users
- **Registration & Login**: Create accounts and authenticate securely
- **Packing List Management**: Create, edit, and delete personal packing lists
- **Item Management**: Add, edit, and organize items within lists
- **Progress Tracking**: Monitor packing completion status
- **Category Organization**: Use predefined categories for better organization

### System Features
- **User Isolation**: Each user can only access their own data
- **Data Privacy**: Secure storage and transmission of personal information
- **Session Management**: Secure login/logout functionality

## 📁 Project Structure

```
packing-list-cms/
├── assets/
│   └── css/
│       └── style.css          # Main stylesheet with responsive design
├── config/
│   └── database.php           # Database configuration and connection
├── includes/
│   ├── auth.php              # Authentication and user management functions
│   └── packing_lists.php     # Packing list CRUD operations
├── sql/
│   └── setup.sql             # Database schema and initial setup
├── create_list.php           # Create new packing list interface
├── dashboard.php             # Main user dashboard
├── edit_item.php            # Edit individual items
├── edit_list.php            # Edit packing list details
├── index.php                # Landing page and home
├── login.php                # User authentication
├── logout.php               # Session termination
├── register.php             # User registration
├── view_list.php            # View and manage packing list items
└── README.md                # Project documentation
```

## ⚙️ Setup Instructions

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7+ or MariaDB 10.2+
- Web server (Apache, Nginx, or built-in PHP server)
- PDO MySQL extension enabled

### Installation Steps

#### 1. Download and Extract
```bash
# Clone or download the project files
git clone <repository-url> packing-list-cms
cd packing-list-cms
```

#### 2. Database Setup
1. Create a MySQL database:
```sql
CREATE DATABASE packing_list_cms;
```

2. Import the database structure:
```bash
mysql -u your_username -p packing_list_cms < sql/setup.sql
```

#### 3. Configuration
1. Edit `config/database.php` with your database credentials:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'packing_list_cms');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

#### 4. Web Server Setup

**For Apache:**
- Ensure your web server has read/write permissions
- Enable mod_rewrite module
- Place the project in your web root (e.g., `htdocs/packing-list-cms` or `www/packing-list-cms`)
- Access via: `http://localhost/packing-list-cms` (or your configured domain)
- The included `.htaccess` file will handle URL routing

**For PHP Built-in Server (Development):**
```bash
# Navigate to project directory
cd packing-list-cms

# Start server (default port 8000)
php -S localhost:8000

# Or specify a different port
php -S localhost:8080
```
Access via: `http://localhost:8000` (or your specified port)

**For Nginx:**
- Configure your server block to point to the project directory
- Ensure PHP-FPM is configured correctly
- Access via your configured domain or `http://localhost/packing-list-cms`

#### 5. Verify Installation
- Navigate to your installation URL (e.g., `http://localhost/packing-list-cms`)
- You should see the landing page
- Test registration and login functionality
- All redirects should work without port numbers in the URL

### Demo Account
For testing purposes, you can use the following demo credentials:
- **Username**: `demo_user`
- **Password**: `password123`

**Note**: The demo account is pre-configured with sample data for testing the application features.

## 📖 Usage

### Getting Started
1. **Registration**: Create a new account
2. **Login**: Access your dashboard
3. **Create Lists**: Start with your first packing list
4. **Add Items**: Populate with items and categories
5. **Track Progress**: Monitor packing completion

### Creating Packing Lists
1. Click "Create New List" from dashboard
2. Enter title, description, and trip date
3. Save and start adding items

### Managing Items
1. Open a packing list
2. Add items with names, quantities, and categories
3. Check off items as you pack them
4. Edit or delete items as needed

### Categories Available
- Clothes, Electronics, Toiletries
- Documents, Medications, Entertainment
- Food & Snacks, Travel Gear
- Sports & Outdoor, Miscellaneous

## 🎯 Intended Use

### Personal Use
- **Travel Planning**: Organize packing for vacations, business trips, and events
- **Event Preparation**: Manage items for camping, hiking, or special occasions
- **Home Organization**: Track items for moving or storage projects

### Educational Use
- **Learning PHP/MySQL**: Study modern web development practices
- **Security Implementation**: Understand authentication and data protection
- **Responsive Design**: Learn mobile-first web development

### Development Use
- **Template Project**: Use as a starting point for similar applications
- **Code Reference**: Study clean, well-structured PHP code
- **Best Practices**: Learn secure coding and database design

## 🔒 Security Features

- **Password Security**: Bcrypt hashing with salt
- **Session Protection**: Secure session handling
- **CSRF Protection**: Token-based form security
- **Input Validation**: Comprehensive data sanitization
- **SQL Injection Prevention**: Prepared statements
- **XSS Protection**: Output escaping and validation

## 🎨 Customization

### Styling
- Modify `assets/css/style.css` for visual changes
- Responsive design with mobile breakpoints
- CSS custom properties for easy theming

### Functionality
- Extend with additional PHP files
- Follow existing code patterns
- Use established authentication functions

## 🐛 Troubleshooting

### Common Issues
- **Database Connection**: Verify credentials and MySQL service
- **Permissions**: Check file and database user permissions
- **Sessions**: Ensure PHP session configuration is correct
- **Styling**: Clear browser cache and verify CSS paths
- **Redirect Issues**: If redirects include port numbers (e.g., `localhost:8080`), ensure you're accessing the site via the correct URL. Use relative paths in your browser (e.g., `http://localhost/packing-list-cms` instead of `http://localhost:8080/packing-list-cms`)
- **URL Rewriting**: For Apache, ensure `mod_rewrite` is enabled. The `.htaccess` file handles proper URL routing

### Debug Mode
Enable error reporting for development:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## 📄 License

**License for RiverTheme**

[RiverTheme.com](https://RiverTheme.com) makes this project available for demo, instructional, and personal use. You can ask for or buy a license from [RiverTheme.com](https://RiverTheme.com) if you want a pro website, sophisticated features, or expert setup and assistance. A Pro license is needed for production deployments, customizations, and commercial use.

**Disclaimer**

The free version is offered "as is" with no warranty and might not function on all devices or browsers. It might also have some coding or security flaws. For additional information or to get a Pro license, please get in touch with [RiverTheme.com](https://RiverTheme.com).

---

**Packing List CMS** - Simple trip organization made easy.

*Developed by [RiverTheme](https://rivertheme.com)*

