#!/bin/bash

# Clean Repomix Script for CannaRewards
# This script generates a focused repomix output excluding irrelevant files

echo "Starting clean repomix generation for CannaRewards..."

# Navigate to the project directory
cd /home/anwar/cannarewards/cannarewards

# Run repomix with the clean configuration
npx repomix@latest --config ./repomix.config.js

echo "Repomix generation completed!"
echo "Output saved to: /home/anwar/cannarewards/cannarewards/repomix-output-clean.xml"

# Show the size of the output file
echo "File size:"
du -h ./repomix-output-clean.xml

echo "Clean repomix generation complete!"