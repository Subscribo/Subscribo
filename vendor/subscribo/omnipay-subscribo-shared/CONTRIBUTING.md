# Contribution guidelines

- Contributor may contribute code, tests, ideas and feedback via e-mail: packages@subscribo.io or via GitHub
- Maintainers may, but not have to use the contribution
- Maintainers may use the contribution in its entirety, or just part(s) of it, modified or unmodified
- Contributor by contributing a contribution is giving consent for using and publishing the contribution
  - under [MIT](http://opensource.org/licenses/MIT) or different license
    (provided that that possibly different license does not impose any liability on contributor)
    with current or different [license headers](LICENSE.txt)
  - within package with current or different name
  - within this or possibly re-branded or re-labelled package
  - with or without reference to contributor
  - as part of current package or as part of a different product
  - within package (or product) published for general public or made accessible for limited audience
  - within package (or product) using current or different version control system (VCS) or not using VCS
  - within package (or product) using current or different dependency manager or not using a dependency manager
  - with or without reference to contributor
  - with or without attributing the contribution to contributor
- Please contribute only those contributions for which you have right to contribute and to give before mentioned consent

# Coding Style

Contributors are encouraged to follow [PSR-2 coding style](http://www.php-fig.org/psr/psr-2) with following allowed exceptions:

- Php unary Not logical operator (exclamation mark) may by preceded and followed by space even if it follows opening parenthesis of control structures or method or function calls.
  So the following construction is also allowed:

```php
    if ( ! $var) {
        $this->someMethod( ! $var);
    }
```

- Class with empty body may have opening and closing braces of the body in the same line as class declaration.
  So the following construction is also allowed:

```php
    class SomeClass extends BaseClass {}
```
