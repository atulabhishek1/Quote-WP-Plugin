# Database Schema

## Tables

### wp_ac_clients
id, name, mobile, email, address

### wp_ac_projects
id, client_id, status, budget

### wp_ac_quotes
id, project_id, version, total

### wp_ac_quote_items
id, quote_id, room, item, qty, price

### wp_ac_payments
id, project_id, amount, method

### wp_ac_progress
id, project_id, stage

### wp_ac_gallery
id, project_id, media_url

Relationships
Client -> Projects
Project -> Quotes
Quote -> Quote Items
Project -> Payments
Project -> Progress
Project -> Gallery
