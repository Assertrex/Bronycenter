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

### Requirements
I don't really know yet, but I'm testing it on a lastest versions of:

* **Apache/Nginx** webserver
* **MariaDB** database
* **PHP** 7.2.x
  * Changing avatars requires **Imagick** PHP extension. If you've never set it up
before then good luck. I always forget how I've installed it and I have to waste
about 30 minutes to set it up correctly again (at least on Windows).

## Configuration

### Document root & Security
If you have an Apache, then .htaccess files will use **public/** directory as
a document root (by doing a rewrite). This will prevent access to the files
that shouldn't be accessable from the outside.

If you use Nginx or you don't want to use .htaccess files in Apache, then remove
them and point your document root to the **public/** directory.

### Database
Database schema is exported to a **dbschema.sql** file. Create a database and
import this file with PHPMyAdmin or copy & paste it directly into MySQL console.
It should work, I guess...

You have to fill **settings.ini** file with your database credentials.

### Updates
Don't update it. Each update can have different database schema, and it just
won't work without changes to the database. Yes, I have no idea what I am doing.
