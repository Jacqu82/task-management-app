#!/bin/sh
set -e

cd /web/frontend

echo "🚀 Starting frontend container..."

# Install dependencies if node_modules does not exist
if [ ! -d "node_modules" ]; then
  echo "📦 Installing npm dependencies..."
  npm install
else
  echo "📦 npm dependencies already installed"
fi

echo "🖥 Running frontend dev server..."
exec npm run dev -- -H 0.0.0.0
