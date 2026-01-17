#!/bin/bash

# Comprehensive Repomix Script for CannaRewards
# This script offers multiple options for generating focused repomix outputs

echo "==========================================="
echo "CannaRewards Repomix Generator"
echo "==========================================="
echo ""
echo "Original repomix file size:"
du -h /home/anwar/cannarewards/repomix-output.xml 2>/dev/null || echo "File not found: /home/anwar/cannarewards/repomix-output.xml"
echo ""

while true; do
  echo "Select an option:"
  echo "1) Generate clean repomix (excludes node_modules, pnpm-store, Filament)"
  echo "2) Generate ultra-clean repomix (minimal essential files only)"
  echo "3) Compare all repomix file sizes"
  echo "4) Exit"
  echo -n "Enter your choice [1-4]: "
  read choice

  case $choice in
    1)
      echo ""
      echo "Generating clean repomix..."
      cd /home/anwar/cannarewards/cannarewards
      npx repomix@latest --config ./repomix.config.js
      echo "Clean repomix generation completed!"
      echo "Size of clean output:"
      du -h ./repomix-output-clean.xml
      echo ""
      ;;
    2)
      echo ""
      echo "Generating ultra-clean repomix..."
      cd /home/anwar/cannarewards/cannarewards
      npx repomix@latest --config ./repomix.config.ultra.js
      echo "Ultra-clean repomix generation completed!"
      echo "Size of ultra-clean output:"
      du -h ./repomix-output-ultra-clean.xml
      echo ""
      ;;
    3)
      echo ""
      echo "Comparing repomix file sizes:"
      echo "Original (bloated):"
      du -h /home/anwar/cannarewards/repomix-output.xml 2>/dev/null || echo "Not found"
      echo "Clean version:"
      du -h /home/anwar/cannarewards/cannarewards/repomix-output-clean.xml 2>/dev/null || echo "Not generated"
      echo "Ultra-clean version:"
      du -h /home/anwar/cannarewards/cannarewards/repomix-output-ultra-clean.xml 2>/dev/null || echo "Not generated"
      echo ""
      ;;
    4)
      echo "Exiting..."
      exit 0
      ;;
    *)
      echo "Invalid option. Please select 1-4."
      ;;
  esac
done