# Secure Root Project Directory
This is very important to secure your project directory to prevent unwanted access
### Code Sample

Create a htaccess to your root project, e.g: `/var/www/html/project/.htaccess`
```bash
# Disable server signature
ServerSignature Off

# Disable directory listing
IndexIgnore *

# Disable access to /vendor/*
RewriteRule ^(.*)/vendor/.*\.(php|rb|py)$ - [F,L,NC]
RewriteRule ^vendor/.*\.(php|rb|py)$ - [F,L,NC]

# Disable anything access to .htaccess, .env, etc
<FilesMatch "^\.">
  Order allow,deny
  Deny from all
</FilesMatch>
```

## Table Of Contents
- [Back To Index](./index.md)