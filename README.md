<img align="right" width="175px" height="100px" src="https://d33wubrfki0l68.cloudfront.net/5a27d37defa5f82b8542756e2ecb0108db2f5a45/eb216/assets/images/footer_logo.svg" />

# Phalcon Email Sending with Queue

## Coding Test Task
Write a Phalcon 3.4.5 application that contains 2 buttons.
- first button on click queues an email. Does not wait for email to be sent. (Make sure to create the task that actually sends the email)
- second button sends an email directly without using the queue, page waits for the email to be sent.
- Show a success page for both cases.
- Configurable mail settings (use https://mailtrap.io for testing)
- Use PHPMailer
- Use Beanstalk

Bonus points:
- Create an alternative version using RabbitMQ.

## Solution
- Docker based on [this phalcon image][:phalconImage:]
- docker-compose
- PHP 7.2
- [Phalcon][:phalcon:] 4 (couldn't use v3)
- RabbitMQ 3

## Start the server with Docker on macOS/Linux
1. Clone this repository
2. Make sure you have [Docker][:docker:] installed
3. Edit config/config.json with the credentials to the Mail server (username, password, emailServer, etc.)
4. Execute make to build and start the server on docker:
```bash
make install
make run
```
5. Access the server on http://localhost

## Start the server without Docker on local
1. Clone this repository
2. Make sure you have PHP 7.2 and Phalcon 4 installed
3. Edit config/config.json with the credentials to the Mail server (username, password, emailServer, etc.)
4. Execute make to build and start the server:
```bash
make php-build
make php-run
```
5. Access the server on http://localhost


[:phalconImage:]:     https://github.com/iamcommee/phalcon-docker-compose-example   
[:phalcon:]:          https://github.com/phalcon/cphalcon
[:docker:]:           https://www.docker.com
