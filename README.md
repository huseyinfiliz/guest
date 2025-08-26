# Guest Login Mode

![License](https://img.shields.io/badge/license-MIT-blue.svg) [![Latest Stable Version](https://img.shields.io/packagist/v/huseyinfiliz/guest.svg)](https://packagist.org/packages/huseyinfiliz/guest) [![Total Downloads](https://img.shields.io/packagist/dt/huseyinfiliz/guest.svg)](https://packagist.org/packages/huseyinfiliz/guest)

A [Flarum](http://flarum.org) extension. Guest Login Mode allows quick access for users who want to make a few posts without registration.

## Features

- 🚀 Quick guest login without registration
- 🔒 IP-based session management (GDPR compliant with hashed IPs)
- 📝 Configurable post limits for guest users
- 🏷️ Customizable guest username prefix
- 🛡️ Protection against spam with IP tracking

## Configuration

- **Guest Username Prefix**: Set the prefix for guest usernames (default: Guest)
- **Maximum Posts**: Limit the number of posts guest users can make (default: 3)

## Installation

Install with composer:

```sh
composer require huseyinfiliz/guest:"*"
```

## Updating

```sh
composer update huseyinfiliz/guest:"*"
php flarum migrate
php flarum cache:clear
```

## Links

- [Packagist](https://packagist.org/packages/huseyinfiliz/guest)
- [GitHub](https://github.com/huseyinfiliz/guest)
- [Discuss](https://discuss.flarum.org/d/37996-guest-mode-extension)