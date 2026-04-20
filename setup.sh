#!/bin/bash

# Setup script for apigeneradordata project
# This script generates required encryption keys and configures the .env file

set -e

echo "🚀 Setting up apigeneradordata..."

cd "$(dirname "$0")/src"

# Check if .env exists, if not copy from .env.example
if [ ! -f .env ]; then
    echo "📋 Creating .env from .env.example..."
    cp .env.example .env
else
    echo "✅ .env already exists"
fi

# Generate APP_KEY if not set
if grep -q "APP_KEY=base64:GENERATE_ME_WITH_SETUP_SCRIPT" .env; then
    APP_KEY=$(openssl rand -base64 32)
    echo "🔑 Generating APP_KEY..."
    sed -i "s|APP_KEY=base64:GENERATE_ME_WITH_SETUP_SCRIPT|APP_KEY=base64:$APP_KEY|g" .env
else
    echo "✅ APP_KEY already configured"
fi

# Generate JWT_SECRET if not set
if grep -q "JWT_SECRET=GENERATE_ME_WITH_SETUP_SCRIPT" .env; then
    JWT_SECRET=$(openssl rand -base64 32)
    echo "🔐 Generating JWT_SECRET..."
    sed -i "s|JWT_SECRET=GENERATE_ME_WITH_SETUP_SCRIPT|JWT_SECRET=$JWT_SECRET|g" .env
else
    echo "✅ JWT_SECRET already configured"
fi

echo ""
echo "✨ Setup completed successfully!"
echo ""
echo "Next steps:"
echo "  1. docker compose build"
echo "  2. docker compose up -d"
echo "  3. docker compose exec app php artisan migrate"
echo "  4. Visit http://localhost:8001"
