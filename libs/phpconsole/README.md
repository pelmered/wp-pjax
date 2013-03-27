### What is phpconsole?

In one sentence, phpconsole is a detached logging facility for PHP, JS and other environments, with analytical twist, to aid your daily development routine. What does it mean, exactly?

The main aim of phpconsole is to replace burdensome print_r() and var_dump() functions with something vastly superior.

Phpconsole lets you send data to external server, where the data is processed and displayed in user-friendly form. The code is indented and highlighted and you can see additional information, like the address used to trigger the code, the date and time or the location of the code within your project.

At this moment it only works with PHP, unless you feel like porting the client code to other languages. All client libraries are going to be stored on public github account.

### How easy is it to use phpconsole?

```php
include_once('path/to/phpconsole/install.php');
phpconsole('Hello World!');

// that easy!
```

### Useful links

[phpconsole.com](http://phpconsole.com) - main page

[Getting Started Guide](https://docs.google.com/document/d/1gdmk6USG5q92tDJjqrC35oYnBhnk6xZ4_Z77vDbdmns/edit) - how to set up basic version of phpconsole (always up to date)

[Getting Started (with CodeIgniter)](https://docs.google.com/document/d/14LGF1D4WKgw7GlERjDNyktPWfb3MVx_52ZlydqUzZkA/edit) - how to set up CodeIgniter framework to work with phpconsole

Check out our [Product Tour](http://phpconsole.com/tour).

### License

See LICENSE file.
