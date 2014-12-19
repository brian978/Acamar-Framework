# About

The Acamar framework is not intended as a full stack framework and it will never be one. The main purpose of the
framework is to provide the minimum code that is required to build a web application that uses the MVC pattern (this
ranges from APIs to admin back-ends).

The framework is PSR-0 / PSR-4 compliant, so you can use it in conjunction with any other library or full stack
framework.

# Development state

Since the framework is only implemented in
the [skeleton application](https://github.com/brian978/Acamar-SkeletonApplication) I will keep it in Alpha stage for
now. After the propel integration is done I will move it to Beta stage. I'll will set it to Stable only after I
implement a bookstore application (as a usage example) on the skeleton application.

# Main selling points
* Event based architecture
* Light and fast
* Flexible
* Decoupled a much as possible (the Mvc package si an exception)
* Some features load ONLY on demand

# Documentation

The documentation can be found [HERE](http://acamar.no-ip.biz)

# Composer

The framework can also be installed via composer:

    {
        "require": {
                "brian978/acamar": "dev-master"
            }
    }

# Starter application

The framework uses a specific file structure (which can be found in the documentation).
If you don't want to create the structure manually you can use the skeleton application
from [HERE](https://github.com/brian978/Acamar-SkeletonApplication).
