Docker - jobs processing using php pcntl
============================================

### How to use

* docker build -t php-workers .
* docker run php-workers php ./run.php
* docker run php-workers ./vendor/bin/phpunit ./tests/
```
Log file path: ./log/result.log