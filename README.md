# mdsite

This is a really simple website framework built in PHP.
You provide pages written in Markdown, and it gives you a nicely-formatted website.

## Installing mdsite

1. You need Apache, `mod_rewrite`, and PHP to run mdsite. Just about any shared host should work.
1. Copy the contents of the `public_html` folder into your server's document root (which is probably also named `public_html`).
1. That's all. Open up your new website and check it out!

## Getting started

Once it's set up, here's some things you can do:

* Edit `index.md` to change what's on your homepage.
* Make some new pages, which are just files ending in `.md`. You can organize them in folders if you want.
* Change the site title by editing `res/layout.htm` and changing `Site Title` to something better.
  Make sure you change it in both the `<title>` and the `<h1>`.
* If you know some HTML and CSS, change more of `res/layout.htm` to make the site unique.
  Feel free to replace or mess with `style.css`, or put some of your own stuff in `res/`.

## More info

Go to <http://nkorth.com/projects/mdsite> for more details.
