# CSSPrites
Simple CSS Sprites generator.

## Installation

### Phar file

Download the [cssprites.phar](http://benoth.github.io/phar/cssprites/cssprites.phar).

```bash
wget http://benoth.github.io/phar/cssprites/cssprites.phar
```

To install globally put `cssprites.phar` in `/usr/bin`.

```bash
sudo chmod +x cssprites.phar && mv cssprites.phar /usr/bin/cssprites
```

### Composer global

```bash
composer global require benoth/cssprites
```

## Usage

### Short version

Go in your sprite's images directory and run `cssprites generate` and just answer the few asked questions on how you want your sprite generated.

You can also run with `-i path/to/images` and `-n` for non interactive mode.

### Long version

@todo

## Testing

``` bash
$ vendor/bin/phpunit
```

## License

The MIT License (MIT). Please see [License File](https://github.com/Benoth/cssprites/blob/master/LICENSE) for more information.
