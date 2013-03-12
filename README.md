# mdsite

mdsite is my attempt at making a ridiculously easy-to-use framework for simple sites in less than 200 lines of PHP.
Basically, give it a folder full of Markdown pages and it will make them into a website.
It features clean URLs, automatically generated navigation, a nice default template, and the ability to easily make templates using Tumblr-like syntax.

You don't need to know PHP or HTML to install and use mdsite. You do need to know Markdown.
(Markdown Extra features are also available if you want to use them.)

## Documentation

### Getting started

Requirements:

* Apache2
* mod_rewrite
* PHP 4.0.5 or greater
* PHP APC extension (optional)

Extract mdsite into your website's document root (usually called `public_html`). Make sure the `.htaccess` file gets copied too. You should have a folder called `res/`, an `.htaccess` file, and a page called `index.md`. You can start editing `index.md` right away, but when you open your site you'll notice that the site title is still the default. To change the site title, edit `res/layout.htm`. You don't have to know HTML to make this change, if you're careful. The site title is in two places, once between `<title>` tags and once between `<h1>` tags. The `<title>` is what displays in the title bar or tab in a browser, and the `<h1>` is what displays on the page.

That's all you need to do for setup, unless you want to mess with the site template (which is pretty self-explanatory if you know HTML). To add more pages, just make more files with `.md` extensions.

### File structure

Files with a `.md` extension are treated as pages. Their URL is the filename with the extension removed. If a directory is requested, the script will look for a page called `index.md` and display that. If it doesn't find an index page, it displays a list of the directory's contents.

Any other files (images etc.) can just be stored alongside pages and will behave exactly like a normal static HTML site. Additionally, if a page is requested _with_ its `.md` extension, its Markdown source will be displayed (because the script doesn't run on existing files).

Pages and other files can be in folders, which can be as many levels deep as you want. The `res/` folder contains mdsite's components, so you can't make pages in there. Other than `res/`, you can name folders anything you want. (including folders named res on other levels)

Any file called `[pagename].redirect` will act as a redirect. For example, if you create a file at `/path/to/test.redirect` containing only the text `/path/to/something/else`, then users visiting `/path/to/test` will be sent to `/path/to/something/else` instead. (You can also redirect to external links like `http://example.com`.) Redirect files will not show up in your site's navigation.

### Template syntax

To theme your site, take a look at `res/layout.htm`. (You'll need to know HTML/CSS.) The special tags for mdsite are listed below; there's nothing special about them, they're just replaced with the values they stand for. These tags are what allow you to make a single "layout" file that mdsite uses for all your pages.

You don't have to use `res/style.css`, so rename it if you want. Similarly, you can use images etc. in your design, but you should keep them in `res/` so they're out of the way.

Tag|Replaced with
---|-------------
`{title}`|The title of the page, grabbed from the first `h1`.
`{content}`|The entire contents of the page.
`{contentbody}`|Like `{content}` except with the first `h1` removed, so you can use `<h1>{title}</h1>` to put it somewhere else.
`{nav}`|A `ul` containing the site navigation. It has no class or id, so you might need to put it inside an identifiable element for styling.
`{pageurl}`|The current page's URL path, starting with a `/`. To make an absolute path to the current page, write something like `http://example.com{pageurl}`.

Currently, you can only use one of the two content tags (`{content}`, `{contentbody}`) in your template. You probably shouldn't need to use either more than once anyway.

Example (unstyled) template:


    <!DOCTYPE html>
    <html>
    <head>
    <meta charset="UTF-8" />
    <title>{title} - Site Title</title>
    </head>
    <body>
    <header>
    <h1>Site Title</h1>
    {nav}
    </header>
    <article>
    {content}
    </article>
    </body>
    </html>

Example of share links with `{pageurl}`:

    <p>Share this page on
    <a href="http://facebook.com/sharer.php?u=http://example.com{pageurl}">Facebook</a>
    or <a href="http://twitter.com/share?url=http://example.com{pageurl}">Twitter</a>.</p>
