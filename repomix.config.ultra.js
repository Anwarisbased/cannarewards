module.exports = {
  // Repomix configuration for CannaRewards - Ultra Clean version
  include: [
    // Core application logic
    'app/Actions/**/*',
    'app/Data/**/*',
    'app/Http/Controllers/**/*',
    'app/Http/Middleware/**/*',
    'app/Jobs/**/*',
    'app/Models/**/*',
    'app/Providers/**/*',
    
    // Routes
    'routes/**/*',
    
    // Configuration
    'config/app.php',
    'config/auth.php',
    'config/database.php',
    'config/tenancy.php',
    'config/typescript-transformer.php',
    
    // Frontend
    'resources/js/**/*',
    'resources/css/**/*',
    
    // Database
    'database/migrations/**/*',
    'database/factories/**/*',
    'database/seeders/**/*',
    
    // Tests
    'tests/**/*',
    
    // Root files
    'artisan',
    'composer.json',
    'composer.lock',
    'package.json',
    'pnpm-lock.yaml',
    'vite.config.js',
    'tsconfig.json',
    'eslint.config.js',
    '.env',
    '.env.example',
    
    // Documentation
    'docs/**/*.md',
    
    // Main README
    'README.md'
  ],
  exclude: [
    // Dependency directories - MAJOR TOKEN REDUCERS
    'node_modules/**/*',
    '.pnpm-store/**/*',
    
    // Filament (as mentioned as irrelevant)
    'app/Filament/**/*',
    
    // Build/dist directories
    'public/build/**/*',
    'storage/**/*',
    'vendor/**/*',
    
    // IDE and system files
    '.idea/**/*',
    '.vscode/**/*',
    '.git/**/*',
    '.gitignore',
    
    // Logs and temporary files
    'logs/**/*',
    'bootstrap/cache/**/*',
    
    // Everything in node_modules (massive reduction)
    'node_modules/**/*',
    
    // Docker related (we can include compose.yaml separately if needed)
    '.docker/**/*',
    
    // Cache files
    'cache/**/*',
    '.phpunit.result.cache',
    
    // Large third-party files
    '**/coverage/**/*',
    '**/dist/**/*',
    '**/build/**/*',
    '**/tmp/**/*',
    '**/temp/**/*'
  ],
  output: {
    filePath: './repomix-output-ultra-clean.xml',
    format: 'xml',
    maxFileSize: 20000000, // 20MB max per file
  },
  workspace: {
    path: '.',
    name: 'CannaRewards-v4.0-Ultra-Clean',
    description: 'Ultra clean version of CannaRewards v4.0 focusing only on essential application code'
  }
};