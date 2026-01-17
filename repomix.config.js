module.exports = {
  // Repomix configuration for CannaRewards - Clean version
  include: [
    'app/**/*',
    'routes/**/*',
    'config/**/*',
    'resources/js/**/*',
    'resources/css/**/*',
    'resources/views/**/*',
    'database/migrations/**/*',
    'database/factories/**/*',
    'database/seeders/**/*',
    'tests/**/*',
    '*.php',
    '*.js',
    '*.ts',
    '*.jsx',
    '*.tsx',
    '*.json',
    '*.lock',
    '*.env',
    '*.md',
    '.env*',
    '.github/**/*',
    'docs/**/*'
  ],
  exclude: [
    // Dependency directories
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
    
    // Large documentation files that are not code
    'node_modules/**/*',
    
    // Docker related (keeping only compose files at root)
    '.docker/**/*',
    
    // Cache files
    'cache/**/*',
    '.phpunit.result.cache'
  ],
  output: {
    filePath: './repomix-output-clean.xml',
    format: 'xml',
    maxFileSize: 20000000, // 20MB max per file
  },
  workspace: {
    path: '.',
    name: 'CannaRewards-v4.0-Clean',
    description: 'Clean version of CannaRewards v4.0 focusing on core application code'
  }
};