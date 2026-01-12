# Contributing to Chronos V3

We welcome contributions from the community! This document provides guidelines for contributing to the project.

## Code of Conduct

- Be respectful and constructive
- Focus on what is best for the community
- Show empathy towards other community members

## How to Contribute

### Reporting Bugs

1. Check if the bug has already been reported
2. Create a new issue with:
   - Clear title and description
   - Steps to reproduce
   - Expected vs actual behavior
   - Screenshots if applicable
   - PHP version, database version, and OS

### Suggesting Enhancements

1. Check if the enhancement has been suggested
2. Create a new issue describing:
   - The problem you're trying to solve
   - Your proposed solution
   - Alternative solutions considered
   - Potential impact

### Pull Requests

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/your-feature-name`
3. Make your changes following our coding standards
4. Test your changes thoroughly
5. Commit with clear messages: `git commit -m "Add feature: description"`
6. Push to your fork: `git push origin feature/your-feature-name`
7. Create a Pull Request

## Coding Standards

### PHP

- Follow PSR-12 coding standard
- Use type declarations where possible
- Document functions with PHPDoc comments
- Use meaningful variable and function names

Example:

```php
/**
 * Calculate player rating based on statistics
 *
 * @param array $stats Player statistics array
 * @return int Calculated rating
 */
function calculateRating(array $stats): int {
    $kills = $stats['kills'] ?? 0;
    $deaths = $stats['deaths'] ?? 0;
    
    return ($kills * RATING_KILL_POINTS) + 
           ($deaths * RATING_DEATH_POINTS);
}
```

### SQL

- Use prepared statements for all queries
- Use descriptive table and column names
- Add indexes for frequently queried columns
- Include comments for complex queries

### HTML/CSS

- Use semantic HTML5 elements
- Follow mobile-first responsive design
- Use CSS variables for theming
- Keep CSS organized and commented

### JavaScript

- Use modern ES6+ syntax
- Comment complex logic
- Handle errors appropriately
- Avoid global variables

## Testing

Before submitting a PR:

1. Test all modified functionality
2. Test in different PHP versions (8.0, 8.1, 8.2)
3. Test in different browsers
4. Check for SQL injection vulnerabilities
5. Verify XSS protection
6. Test with different database sizes

## Git Commit Messages

- Use present tense ("Add feature" not "Added feature")
- Use imperative mood ("Move cursor to..." not "Moves cursor to...")
- Limit first line to 72 characters
- Reference issues and pull requests

Examples:
```
Add player search functionality
Fix rating calculation bug (#123)
Update documentation for installation
Refactor database connection class
```

## Project Structure

```
Neos_Chronos_V3/
├── admin/              # Admin panel files
├── includes/           # Core PHP includes
├── modules/            # Feature modules
├── templates/          # HTML/CSS templates
├── database/           # Database schemas
├── docs/              # Documentation
├── cache/             # Cache storage
├── logs/              # Log files
└── uploads/           # User uploads
```

## Development Setup

1. Clone the repository:
   ```bash
   git clone https://github.com/Thomas7140/Neos_Chronos_V3.git
   cd Neos_Chronos_V3
   ```

2. Set up local environment:
   ```bash
   cp .env.example .env
   # Edit .env with your local settings
   ```

3. Install dependencies (if any):
   ```bash
   composer install  # If using Composer
   ```

4. Set up database:
   ```bash
   mysql -u root -p < database/schema.sql
   ```

## Documentation

When adding features:

1. Update relevant documentation in `docs/`
2. Add inline code comments
3. Update CHANGELOG.md
4. Include usage examples

## Security

### Reporting Security Issues

**DO NOT** create public issues for security vulnerabilities.

Email security concerns to: [security contact from project]

### Security Checklist

- [ ] All user input is sanitized
- [ ] SQL queries use prepared statements
- [ ] Passwords are properly hashed
- [ ] CSRF tokens are validated
- [ ] XSS protection is in place
- [ ] File uploads are validated
- [ ] Error messages don't leak sensitive info

## Code Review Process

1. PRs require at least one review
2. All tests must pass
3. Code must follow style guidelines
4. Documentation must be updated
5. No merge conflicts

## Release Process

1. Update version in config.php
2. Update CHANGELOG.md
3. Create git tag: `git tag -a v3.x.x -m "Version 3.x.x"`
4. Push tag: `git push origin v3.x.x`
5. Create GitHub release with notes

## Getting Help

- GitHub Issues: Bug reports and feature requests
- Discussions: General questions and ideas
- Discord/Forums: Community chat (if available)

## License

By contributing, you agree that your contributions will be licensed under the MIT License.

## Recognition

Contributors will be recognized in:
- README.md contributors section
- Release notes
- About page in the application

## Development Priorities

Current focus areas:

1. PHP 8.2+ compatibility
2. Performance optimization
3. Enhanced security features
4. Mobile responsiveness
5. API development
6. Plugin system

## Questions?

Feel free to ask questions by:
- Creating a discussion topic
- Commenting on relevant issues
- Reaching out to maintainers

Thank you for contributing to Chronos V3! 🎮
