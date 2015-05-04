# Contribution guidelines

- Contributor may contribute code, tests, ideas and feedback via e-mail: packages@subscribo.io or via GitHub
- Maintainers may, but not have to use the contribution
- Maintainers may use the contribution in its entirety, or just part(s) of it, modified or unmodified
- Contributor by contributing a contribution is giving consent for using and publishing the contribution under [MIT license](http://opensource.org/licenses/MIT) with current or future [license headers](LICENSE.txt)
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
