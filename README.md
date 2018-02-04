# BronyCenter - Social Network Web Application

![ScreenShot](https://raw.github.com/Assertrex/BronyCenter/master/screenshots/social-index.jpg)

## About it
BronyCenter is a social network created mainly for My Little Pony fans. It's
currently in an early development stage, so a lot of things will change with time.

It's not recommended to use this code as it can have a lot of security issues. It
also doesn't have an installer or updater, so it may be hard to add new
changes to the database to update the code.

I know that I should use a MVC framework, but it takes me too much time to
learn it and I don't really know what's happening in my code then. Anyway, I try
to make my code as much readable as I can.

## Security
If you really want to use this code, please, don't forget to secure
**/system/config/settings.ini** from remote access if you're using Nginx.

#### Nginx
Add code below to this file (inside server block):
/etc/nginx/sites-available/[name-of-your-website]

```
location /system/config/ {
    deny all;
    return 403;
}
```

#### Apache
You don't have to add anything, as *.htaccess* file will do that for you (added in commit 48).
