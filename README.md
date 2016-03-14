![](https://travis-ci.org/brian978/Acamar-Framework.svg?branch=master "Travis status")
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/f7a0d15f-6fa2-4c24-844e-2dad70c2a950/small.png)](https://insight.sensiolabs.com/projects/f7a0d15f-6fa2-4c24-844e-2dad70c2a950)


# About

The Acamar framework is not intended as a full stack framework and it will never be one. The main purpose of the
framework is to provide the minimum code that is required to build a web application that uses the MVC pattern (this
ranges from APIs to admin back-ends).

The framework is PSR-0 / PSR-4 compliant, so you can use it in conjunction with any other library or full stack
framework.

# Main selling points
* Event based architecture
* Light and fast
* Flexible
* Decoupled a much as possible (the Mvc package is an exception)
* Some features load ONLY on demand

# Minimum requirements
* PHP 5.3.29
* Nginx / Apache web server (it's not yet tested on others)

# Documentation

The documentation can be found [HERE](http://acamar.no-ip.biz)

# Composer

The framework can also be installed via composer:

    {
        "require": {
                "brian978/acamar": "1.*"
            }
    }

# Starter application

The framework uses a specific file structure (which can be found in the documentation).
If you don't want to create the structure manually you can use the skeleton application
from [HERE](https://github.com/brian978/Acamar-SkeletonApplication).
