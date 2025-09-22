# Maintenance Mode Feature

This document outlines the new maintenance mode feature for the application.

## Enabling and Disabling Maintenance Mode

To enable maintenance mode, set the `MAINTENANCE_MODE_ENABLED` environment variable to `true`. To disable maintenance mode, set it to `false`.

## Customizing the Maintenance Page

The maintenance page can be customized by editing the `resources/views/maintenance.blade.php` file. You can also change the message and estimated downtime by setting the `MAINTENANCE_MODE_MESSAGE` and `MAINTENANCE_MODE_ESTIMATED_DOWNTIME` environment variables.

## Scheduling Maintenance

To schedule maintenance for a future time, set the `MAINTENANCE_MODE_SCHEDULED_AT` environment variable to a timestamp in the format `YYYY-MM-DD HH:MM:SS`.

## Allowed IPs

To allow certain IP addresses to access the application during maintenance mode, set the `MAINTENANCE_MODE_ALLOWED_IPS` environment variable to a comma-separated list of IP addresses.