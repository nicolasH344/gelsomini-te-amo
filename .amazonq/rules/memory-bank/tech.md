# Technology Stack and Dependencies

## Programming Languages and Versions

### Backend Technologies
- **PHP 7.4+** - Server-side scripting language
- **MySQL 5.7+** - Relational database management
- **SQL** - Database queries and schema management

### Frontend Technologies
- **HTML5** - Semantic markup and structure
- **CSS3** - Styling and responsive design
- **JavaScript (ES6+)** - Client-side interactivity
- **Bootstrap 5.3.0** - CSS framework for responsive design
- **Font Awesome 6.4.0** - Icon library

### Development Environment
- **XAMPP/WAMP** - Local development server stack
- **Apache** - Web server
- **phpMyAdmin** - Database administration interface

## Build Systems and Dependencies

### External Libraries (CDN)
```html
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Font Awesome Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
```

### PHP Extensions Required
- **PDO** - Database abstraction layer
- **PDO_MySQL** - MySQL driver for PDO
- **Session** - Session management
- **JSON** - JSON data handling
- **mbstring** - Multibyte string functions
- **OpenSSL** - Encryption and security functions

### Database Schema
- **cursinho_users** - User authentication and profiles
- **cursinho_exercises** - Exercise catalog and content
- **cursinho_exercise_categories** - Exercise categorization
- **cursinho_user_progress** - Progress tracking
- **cursinho_forum_posts** - Forum discussions
- **cursinho_forum_comments** - Forum comment threads
- **cursinho_forum_categories** - Forum organization
- **cursinho_tutorials** - Tutorial content management
- **password_reset_codes** - Password recovery system
- **chat_messages** - Real-time chat functionality
- **collaborative_sessions** - Multi-user exercise sessions
- **user_badges** - Gamification system
- **mentorship_requests** - Peer learning connections
- **github_integrations** - Version control integration

## Development Commands and Setup

### Local Development Setup
```bash
# 1. Start XAMPP services
# Start Apache and MySQL from XAMPP Control Panel

# 2. Database initialization
# Access: http://localhost/gelsomini-te-amo/pt-br/setup_database.php

# 3. Alternative database setup
# Access: http://localhost/gelsomini-te-amo/pt-br/install_cursinho_db.php
```

### Project URLs
```
# Auto-detection entry point
http://localhost/gelsomini-te-amo/

# Language-specific access
http://localhost/gelsomini-te-amo/pt-br/    # Portuguese
http://localhost/gelsomini-te-amo/en/       # English  
http://localhost/gelsomini-te-amo/es/       # Spanish

# System initialization
http://localhost/gelsomini-te-amo/start.php

# Configuration test
http://localhost/gelsomini-te-amo/test.php
```

### Database Configuration
```php
// config.php structure
$host = 'localhost';
$dbname = 'cursinho_db';
$username = 'root';
$password = '';
$charset = 'utf8mb4';
```

### File Structure Requirements
```
# Required directory permissions
/gelsomini-te-amo/          # Read/Write access
├── pt-br/api/              # API endpoints - Write access
├── en/api/                 # API endpoints - Write access  
├── es/api/                 # API endpoints - Write access
├── database/               # SQL files - Read access
├── Dump20250908/           # Backup files - Read access
└── src/                    # OOP classes - Read access
```

## Architecture Patterns and Standards

### PHP Standards
- **PSR-4** - Autoloading standard implementation
- **MVC Pattern** - Model-View-Controller architecture
- **Singleton Pattern** - Database connection management
- **Factory Pattern** - Object creation in controllers

### Security Standards
- **PDO Prepared Statements** - SQL injection prevention
- **bcrypt Password Hashing** - Secure password storage
- **CSRF Token Protection** - Cross-site request forgery prevention
- **Input Sanitization** - XSS attack prevention
- **Session Security** - Secure session management

### Code Organization
- **Autoloader** - `src/autoload.php` for class loading
- **Base Classes** - Abstract controllers and models
- **Namespace Structure** - Organized class hierarchy
- **Configuration Management** - Centralized settings

### API Design
- **RESTful Endpoints** - Clean API structure
- **JSON Responses** - Standardized data format
- **Error Handling** - Consistent error responses
- **Authentication** - Session-based API access

## Performance and Optimization

### Frontend Optimization
- **CDN Resources** - External library delivery
- **Minified Assets** - Compressed CSS/JS files
- **Responsive Images** - Optimized media delivery
- **Lazy Loading** - Deferred content loading

### Backend Optimization
- **Database Indexing** - Optimized query performance
- **Connection Pooling** - Efficient database connections
- **Caching Strategy** - Session-based data caching
- **Query Optimization** - Efficient SQL operations

### Development Tools
- **Browser DevTools** - Frontend debugging
- **phpMyAdmin** - Database management
- **XAMPP Logs** - Server error tracking
- **Network Inspector** - API request monitoring