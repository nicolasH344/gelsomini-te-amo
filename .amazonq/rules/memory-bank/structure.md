# Project Structure and Architecture

## Directory Organization

### Root Level Structure
```
gelsomini-te-amo/
├── pt-br/                 # Portuguese version (primary)
├── en/                    # English version  
├── es/                    # Spanish version
├── src/                   # Object-oriented architecture
├── database/              # Database schemas and setup
├── Dump20250908/          # SQL dump files
├── .amazonq/              # AI assistant configuration
├── index.php              # Language auto-detection entry point
├── start.php              # System initialization page
├── style.css              # Global stylesheet
└── script.js              # Global JavaScript functionality
```

### Language-Specific Directories
Each language folder (pt-br/, en/, es/) contains identical structure:
```
{language}/
├── api/                   # RESTful API endpoints
│   ├── chat_messages.php
│   ├── collaborative_save.php
│   ├── exercise_chat.php
│   ├── forgot_password.php
│   ├── online_users.php
│   ├── password_reset.php
│   └── setup_password_reset.php
├── data/                  # Static data files
│   ├── tutorials.json
│   └── tutorials.php
├── sql/                   # Database scripts (pt-br only)
└── [application files]    # Core application pages
```

### Modern Architecture (src/)
```
src/
├── Config/
│   ├── Database.php       # Singleton database connection
│   └── Environment.php    # Environment configuration
├── Controllers/
│   ├── BaseController.php # Abstract controller foundation
│   ├── ExerciseController.php # Exercise management logic
│   └── ForumController.php    # Forum functionality
├── Models/
│   ├── BaseModel.php      # Abstract model with common methods
│   ├── Badge.php          # Badge system model
│   ├── Exercise.php       # Exercise data model
│   ├── Forum.php          # Forum post/comment model
│   ├── User.php           # User authentication model
│   └── UserProgress.php   # Progress tracking model
├── autoload.php           # PSR-4 compliant autoloader
└── SecurityHelper.php     # Security utilities and validation
```

## Core Components and Relationships

### Authentication System
- **Entry Points**: login.php, register.php, logout.php
- **Security**: forgot_password.php, reset_password.php
- **API Support**: api/password_reset.php, api/setup_password_reset.php
- **Models**: User.php handles authentication logic
- **Sessions**: Persistent login with "remember me" functionality

### Exercise Management
- **Listing**: exercises_index.php (procedural), exercises_index_oop.php (OOP)
- **Details**: exercise_detail.php with integrated code editor
- **Completion**: complete_exercise.php for progress tracking
- **Collaboration**: collaborative_exercise.php for multi-user sessions
- **Controller**: ExerciseController.php manages exercise logic
- **Model**: Exercise.php, UserProgress.php for data management

### Forum System
- **Main View**: forum_index.php (procedural), forum_index_oop.php (OOP)
- **Post Details**: forum_post.php with threaded comments
- **Functions**: forum_functions.php for common operations
- **Controller**: ForumController.php handles forum logic
- **Model**: Forum.php manages posts and comments

### Real-time Features
- **Chat System**: chat.php with api/chat_messages.php backend
- **Online Users**: api/online_users.php for presence tracking
- **Collaborative Editing**: Real-time code sharing and editing

### Administrative Interface
- **Admin Panel**: admin.php for system management
- **Tutorial Management**: admin_tutorial_form.php, gerenciartuto.php
- **Database Setup**: setup_database.php, install_cursinho_db.php

## Architectural Patterns

### Dual Architecture Approach
The project implements both procedural and object-oriented patterns:

**Procedural Files** (Legacy/Original):
- Direct PHP processing with embedded HTML
- Immediate database connections
- Function-based code organization
- Files: exercises_index.php, forum_index.php, etc.

**Object-Oriented Files** (Modern):
- MVC pattern implementation
- Dependency injection through autoloader
- Class-based architecture with inheritance
- Files: exercises_index_oop.php, forum_index_oop.php, etc.

### Database Layer
- **Singleton Pattern**: Database.php ensures single connection instance
- **PDO Implementation**: Prepared statements for security
- **Model Abstraction**: BaseModel.php provides common CRUD operations
- **Migration Support**: SQL files in database/ and Dump20250908/

### Security Architecture
- **Input Sanitization**: SecurityHelper.php centralizes validation
- **CSRF Protection**: Token-based form security
- **Password Security**: bcrypt hashing with salt
- **Session Management**: Secure session handling with regeneration

### Internationalization
- **Language Detection**: index.php auto-detects browser language
- **Folder-based Localization**: Complete application copies per language
- **Consistent Structure**: Identical file organization across languages
- **Fallback System**: Defaults to Portuguese if language unsupported

### API Design
- **RESTful Endpoints**: Clean API structure in api/ directories
- **JSON Responses**: Standardized data format
- **Authentication**: Session-based API access
- **Real-time Support**: Polling-based updates for chat and collaboration

## Component Relationships

### Data Flow
1. **User Request** → Language Detection (index.php) → Appropriate Language Folder
2. **Authentication** → User.php Model → Session Management
3. **Exercise Access** → ExerciseController → Exercise.php Model → Database
4. **Progress Tracking** → UserProgress.php → Database Updates
5. **Forum Interaction** → ForumController → Forum.php Model → Real-time Updates

### Integration Points
- **GitHub Integration**: github_integration.php connects with GitHub API
- **Email System**: email_config.php, setup_email.php for notifications
- **Badge System**: badges.php integrates with UserProgress for achievements
- **Mentorship**: mentorship.php connects users for peer learning
- **Chat Integration**: Embedded in exercise and forum pages for contextual communication