#!/bin/bash

# Ultra Clean Repomix Script for CannaRewards
# This script generates a highly focused repomix output with only essential files

echo "Starting ultra clean repomix generation for CannaRewards..."

# Navigate to the project directory
cd /home/anwar/cannarewards/cannarewards

# Run repomix with the ultra clean configuration
npx repomix@latest --config ./repomix.config.ultra.js

echo "Ultra clean repomix generation completed!"
echo "Output saved to: /home/anwar/cannarewards/cannarewards/repomix-output-ultra-clean.xml"

# Show the size of the output file
echo "File size:"
du -h ./repomix-output-ultra-clean.xml

echo "Ultra clean repomix generation complete!"

# Compare with the original massive file
echo "Comparing with original file size:"
du -h /home/anwar/cannarewards/repomix-output.xml