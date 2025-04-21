# Environments
.env
.env.*
!.env.example

# Dependencies
/node_modules
/vendor

# Laravel Storage
/storage/*.key
/storage/framework/sessions/*
/storage/framework/cache/*
/storage/framework/testing/*
/storage/logs/*.log
!/storage/logs/.gitignore

# Compiled Assets (if building locally ONLY for deployment - safer to ignore)
# /public/build

# IDE/OS Files
.idea/
.vscode/
*.DS_Store
Thumbs.db

# PHPUnit
phpunit.xml
phpunit.result.cache

# Sail
/.sail/
docker-compose.yml