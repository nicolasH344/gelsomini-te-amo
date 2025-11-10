# Development Guidelines and Standards

## Code Quality Standards

### PHP Coding Standards
- **Session Management**: Always check session status before starting: `if (session_status() == PHP_SESSION_NONE) { session_start(); }`
- **File Inclusion**: Use `require_once` for critical dependencies, `include` for optional components
- **Variable Sanitization**: Sanitize all user input using custom `sanitize()` function or `htmlspecialchars()`
- **Array Functions**: Prefer modern PHP syntax with arrow functions: `array_filter($array, fn($item) => $condition)`
- **Null Coalescing**: Use `??` operator for default values: `$_GET['param'] ?? 'default'`
- **Type Casting**: Explicit type casting for safety: `max(1, (int)$_GET['page'])`

### HTML/CSS Standards
- **Semantic Structure**: Use proper heading hierarchy (h1, h2, h3) and semantic HTML5 elements
- **Bootstrap Integration**: Consistent use of Bootstrap 5 classes with custom CSS enhancements
- **Accessibility**: Include ARIA labels and proper form labeling: `aria-hidden="true"`, `aria-label`, `aria-current`
- **Icon Usage**: Font Awesome icons with semantic meaning and accessibility attributes
- **Responsive Design**: Mobile-first approach with Bootstrap grid system

### JavaScript Standards
- **Input Sanitization**: Always escape HTML in dynamic content using `escapeHtml()` function
- **Event Handling**: Use `addEventListener` for event binding with proper error handling
- **DOM Manipulation**: Check element existence before manipulation: `if (container) return;`
- **Function Naming**: Descriptive function names with clear purpose: `renderBasicExercises()`, `filterAdvancedExercises()`
- **Data Validation**: Validate and sanitize all user inputs before processing

## Architectural Patterns

### MVC Implementation
- **Controllers**: Extend `BaseController` class with common functionality
- **Models**: Inherit from `BaseModel` for shared database operations
- **Separation**: Clear separation between business logic (controllers) and data access (models)
- **Dependency Injection**: Constructor injection for model dependencies

### Database Patterns
- **Singleton Pattern**: Database connection using singleton pattern in `Database.php`
- **PDO Usage**: Prepared statements for all database queries
- **Error Handling**: Proper exception handling with logging: `error_log("Database connection failed: " . $e->getMessage())`
- **Environment Configuration**: Use `Environment::get()` for configuration values with defaults

### Security Patterns
- **Input Validation**: Sanitize all user inputs using dedicated functions
- **SQL Injection Prevention**: Use prepared statements exclusively
- **XSS Prevention**: Escape output with `htmlspecialchars()` or custom `escapeHtml()`
- **Session Security**: Proper session management with status checks

## File Organization Standards

### Directory Structure
- **Language Folders**: Identical structure across `pt-br/`, `en/`, `es/` directories
- **API Endpoints**: Separate `api/` directory for RESTful endpoints
- **Data Files**: Static data in `data/` directory with both JSON and PHP formats
- **Modern Architecture**: Object-oriented code in `src/` directory with PSR-4 autoloading

### File Naming Conventions
- **Procedural Files**: Descriptive names like `tutorials_index.php`, `forum_index.php`
- **OOP Files**: Suffix with `_oop.php` for object-oriented versions
- **API Files**: Clear endpoint names in `api/` directory
- **Configuration**: Centralized config files like `config.php`, `database.php`

### Include Patterns
- **Header/Footer**: Consistent inclusion of `header.php` and `footer.php`
- **Configuration**: Always include `config.php` early in execution
- **Data Sources**: Include data files when needed: `require_once 'data/tutorials.php'`

## User Interface Patterns

### Form Design
- **Bootstrap Forms**: Consistent use of Bootstrap form classes
- **Validation**: Client-side and server-side validation
- **Accessibility**: Proper labeling and ARIA attributes
- **Error Handling**: User-friendly error messages with visual feedback

### Navigation Patterns
- **Breadcrumbs**: Clear navigation hierarchy
- **Pagination**: Consistent pagination with accessibility support
- **Filtering**: Real-time filtering with debounced search
- **Theme Support**: Multiple theme options with localStorage persistence

### Visual Design
- **Card Layout**: Consistent card-based design for content display
- **Badge System**: Color-coded badges for categories and difficulty levels
- **Icons**: Meaningful Font Awesome icons with accessibility attributes
- **Responsive Grid**: Bootstrap grid system for all layouts

## Data Management Patterns

### JSON Data Handling
- **File-based Storage**: JSON files for configuration and static data
- **Default Data**: Automatic creation of default data when files don't exist
- **Pretty Printing**: Use `JSON_PRETTY_PRINT` for readable JSON files
- **Error Handling**: Fallback to empty arrays on JSON decode errors

### Array Processing
- **Filtering**: Use `array_filter()` with arrow functions for data filtering
- **Mapping**: Transform data using `array_map()` and modern PHP syntax
- **Slicing**: Pagination using `array_slice()` for performance
- **Counting**: Use `count()` and `array_column()` for statistics

### State Management
- **Session Variables**: Proper session variable management
- **Local Storage**: Client-side preferences using localStorage
- **URL Parameters**: Clean URL parameter handling with defaults
- **Form State**: Maintain form state across submissions

## Performance Optimization

### Frontend Optimization
- **CDN Resources**: Use CDN for external libraries (Bootstrap, Font Awesome)
- **Lazy Loading**: Implement lazy loading for dynamic content
- **Debouncing**: Debounce search inputs to reduce server requests
- **Caching**: Browser caching for static resources

### Backend Optimization
- **Database Queries**: Efficient queries with proper indexing
- **File Operations**: Minimize file I/O operations
- **Memory Usage**: Efficient array operations and memory management
- **Error Logging**: Proper error logging without exposing sensitive data

### Code Organization
- **Autoloading**: PSR-4 compliant autoloading for classes
- **Dependency Management**: Minimal dependencies with clear separation
- **Code Reuse**: Shared functionality in base classes and helper functions
- **Modular Design**: Modular architecture for maintainability

## Testing and Debugging

### Error Handling
- **Exception Handling**: Proper try-catch blocks with meaningful messages
- **Logging**: Use `error_log()` for debugging without exposing errors to users
- **Fallbacks**: Graceful degradation when features fail
- **Validation**: Input validation at multiple levels

### Development Practices
- **Code Comments**: Meaningful comments for complex logic
- **Function Documentation**: Clear function purposes and parameters
- **Variable Naming**: Descriptive variable names following conventions
- **Code Formatting**: Consistent indentation and formatting

### Security Testing
- **Input Sanitization**: Test all input vectors for XSS and injection
- **Authentication**: Verify authentication and authorization logic
- **Session Management**: Test session handling and security
- **Data Validation**: Validate all data transformations and storage