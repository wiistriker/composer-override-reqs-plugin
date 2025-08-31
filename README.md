# Composer Override Reqs Plugin

[![Latest Stable Version](https://poser.pugx.org/wiistriker/composer-override-reqs-plugin/v)](//packagist.org/packages/wiistriker/composer-override-reqs-plugin)
[![Latest Unstable Version](http://poser.pugx.org/wiistriker/composer-override-reqs-plugin/v/unstable)](https://packagist.org/packages/wiistriker/composer-override-reqs-plugin)
[![Total Downloads](https://poser.pugx.org/wiistriker/composer-override-reqs-plugin/downloads)](//packagist.org/packages/wiistriker/composer-override-reqs-plugin)
[![License](http://poser.pugx.org/wiistriker/composer-override-reqs-plugin/license)](https://packagist.org/packages/wiistriker/composer-override-reqs-plugin)


[Русская версия](#русская-версия) | [English version](#english-version)

---

## Русская версия

### Для чего этот плагин?

Иногда при работе с Composer возникает проблема: некоторые пакеты имеют слишком жёсткие ограничения по версиям зависимостей.  
Например, пакет [`dama/doctrine-test-bundle:6.7.5`](https://packagist.org/packages/dama/doctrine-test-bundle#v6.7.5) требует:

```json
"symfony/cache": "^4.4 || ^5.3 || ^6.0",
"symfony/framework-bundle": "^4.4 || ^5.3 || ^6.0"
```

Это означает, что пакет можно установить только вместе с Symfony `4.4`, `5.3` или `6.0`.  
Однако на практике часто бывает так, что пакет вполне работает и с промежуточными версиями, например Symfony `5.0–5.2`.

### Как можно обойти ограничение?

Существуют стандартные варианты, но у них есть недостатки:

- **Использовать `as` в require**  
  Например:
  ```json
  "symfony/cache": "5.0.11 as 5.3.14"
  ```  
  Минус: нужно указывать точные версии вручную.

- **Форкнуть репозиторий**  
  Внести правки и подключить его через [repositories](https://getcomposer.org/doc/05-repositories.md).  
  Минус: придётся поддерживать собственный форк.

Если по каким-то причинам эти методы вам (как и мне), не подошли, то можно попробовать данный плагин. Минус у данного плагина в том, что
он использует Reflection, поэтому не гарантируется работа при выходе новых версий composer.

---

### Установка и настройка

Установите плагин:

```bash
composer require --dev wiistriker/composer-override-reqs-plugin
```

Добавьте в `composer.json` секцию `extra.requirements-override.override`:

```json
{
    "require": {
        "symfony/cache": "5.0.*",
        "dama/doctrine-test-bundle": "*"
    },
    "extra": {
        "requirements-override": {
            "override": {
                "dama/doctrine-test-bundle": {
                    "6.7.5": {
                        "symfony/cache": "^4.4 || ^5.0 || ^6.0",
                        "symfony/framework-bundle": "^4.4 || ^5.0 || ^6.0"
                    }
                }
            }
        }
    }
}
```

После этого запустите:

```bash
composer update
```

Теперь `dama/doctrine-test-bundle` установится и на Symfony `5.0`, так как зависимости были переопределены.

---

## English version

### What is this plugin for?

Sometimes Composer packages come with overly strict dependency constraints.  
For example, [`dama/doctrine-test-bundle:6.7.5`](https://packagist.org/packages/dama/doctrine-test-bundle#v6.7.5) requires:

```json
"symfony/cache": "^4.4 || ^5.3 || ^6.0"
```

This means the bundle can only be installed with Symfony `4.4`, `5.3`, or `6.0`.  
But in reality, it would also work just fine with Symfony `5.0–5.2`.

### How can we bypass this?

Typical workarounds exist, but each has drawbacks:

- **Use `as` in require**  
  Example:
  ```json
  "symfony/cache": "5.0.11 as 5.3.14"
  ```  
  Drawback: you must specify exact versions manually.

- **Fork the repository**  
  Modify the dependency constraints and include it via [repositories](https://getcomposer.org/doc/05-repositories.md).  
  Drawback: you need to maintain your own fork.

If, for some reason, these methods (just like for me) didn’t work for you, you can try this plugin instead.  
The downside of this plugin is that it relies on Reflection, so compatibility with future Composer releases is not guaranteed.

---

### Installation & Usage

Install the plugin:

```bash
composer require --dev wiistriker/composer-override-reqs-plugin
```

Then add the `extra.requirements-override.override` section in your `composer.json`:

```json
{
    "require": {
        "symfony/cache": "5.0.*",
        "dama/doctrine-test-bundle": "*"
    },
    "extra": {
        "requirements-override": {
            "override": {
                "dama/doctrine-test-bundle": {
                    "6.7.5": {
                        "symfony/cache": "^4.4 || ^5.0 || ^6.0",
                        "symfony/framework-bundle": "^4.4 || ^5.0 || ^6.0"
                    }
                }
            }
        }
    }
}
```

Finally, run:

```bash
composer update
```

Now `dama/doctrine-test-bundle` will install even with Symfony `5.0`, since the dependency constraints were overridden.
