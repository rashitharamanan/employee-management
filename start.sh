#!/bin/bash
set -e

# Railway provides a dynamic PORT environment variable (e.g. 8080 or random port)
PORT="${PORT:-80}"

echo "Starting Apache on port $PORT..."

# Replace default port 80 with the Railway PORT in Apache config
sed -i "s/80/$PORT/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Start Apache in the foreground
exec apache2-foreground
