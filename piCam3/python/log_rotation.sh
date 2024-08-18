#!/bin/bash

log_file="capture.log"
max_backup_files=5

# Check if the log file exists
if [ -f "$log_file" ]; then
    # Determine the number of existing backup files
    current_backup_files=$(ls -1 "${log_file}".* 2>/dev/null | wc -l)
    
    # Create a backup of the current log file
    mv "$log_file" "${log_file}.$(date +%Y%m%d%H%M%S)"
    
    # Remove old backup files exceeding the maximum limit
    while [ "$current_backup_files" -ge "$max_backup_files" ]; do
        oldest_backup_file=$(ls -1t "${log_file}".* | tail -n 1)
        rm "$oldest_backup_file"
        current_backup_files=$((current_backup_files - 1))
    done
    
    # Create a new empty log file
    touch "$log_file"
    
    echo "Log rotated successfully."
else
    echo "Log file not found."
fi
