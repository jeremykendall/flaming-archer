# Flaming Archer

master: [![Build Status](https://secure.travis-ci.org/jeremykendall/flaming-archer.png?branch=master)](https://travis-ci.org/jeremykendall/flaming-archer) develop: [![Build Status](https://secure.travis-ci.org/jeremykendall/flaming-archer.png?branch=develop)](https://travis-ci.org/jeremykendall/flaming-archer)

## What is Flaming Archer?

Flaming Archer is a "photo-a-day" application for hackers. It makes it simple to
create a [365 day](http://fatmumslim.com.au/january-2013-photo-a-day-lets-do-this-thing/)
[photo project](http://mylifescoop.com/2012/10/08/7-most-inspiring-365-day-photo-projects/) of your own.
Simply [fork](https://help.github.com/articles/fork-a-repo) the project, deploy to
your own server, run `composer install`, and voila! You're ready to rock.

## Inspiration

Flaming Archer is a photo-a-day application I put together to learn the 
[Slim PHP micro framework](http://www.slimframework.com/).  The application has
expanded to learning Puppet, rspec, Twig, and Composer, and more. It finally expanded
to an application I thought was production ready, and now I'm using it to share
[my own 365 day photo project](http://365.jeremykendall.net/).

## Documentation

Real documentation is on its way.  Here's the quick and dirty.

* Fork
* Deploy to your server
* Make sure your docroot is `/public`
* `composer install`
* Make sure `/db`, `/logs`, `templates/cache` and `/tmp` are writeable by the web user
    * Tip: I used the Symfony2 ["Setting up Permissions"](http://symfony.com/doc/current/book/installation.html#configuration-and-setup) directions (#1 on my Mac and #2 on Ubuntu)
* Edit the `$userConfig` portion of `/config.php` (only available after running `composer install`)
* Visit http://your-project-site.com
* Fill out the setup form
* SUCCESS!

## Contributing

Pull requests and issues are welcome.  Please review the CONTRIBUTING.md document
before sending a PR.

## 'Keeping it Small' Presentations

### TechCamp Memphis

This project is the basis of my *Keeping it Small: Getting to know the Slim
micro framework* presentation, presented at [TechCamp Memphis](http://techcampmemphis.com/)
on November 3, 2012.  [Slides](http://www.slideshare.net/jeremykendall/keeping-it-small-slim-php)
and [video](http://www.youtube.com/watch?v=yEA0VWHCFac) are available. 

*The presentation was given on 
[revision 8f3d27b](https://github.com/jeremykendall/flaming-archer/tree/8f3d27b73159924102b607cbc0f4a005c971058e)*

### php[tek] 2013

This project is the basis of my *Keeping it Small: Getting to know the Slim
micro framework* presentation, presented at [phptek](http://tek.phparch.com/)
on May 15, 2013. [Slides](http://slidesha.re/13xHfWR) are available. 

*The presentation was given on 
[revision fb71177](https://github.com/jeremykendall/flaming-archer/commit/fb711771ed9b7a8b1c745685a9b1534bee55dafe)*
