# Project Estimate Analyser Application
Helps you to estimate project's total time duration as a whole by dividing it into smaller parts and estimating each one separately.

You can create the estimate both in table and text mode of which the latter is usually more flexible and faster.

The estimate can be saved in JSON format and loaded later.

The application produces structured PDF with the final estimate.

Frontend of the application is written in Knockout and backend in PHP.

# Installation

To run the project you will need Apache, PHP, Composer and NodeJs installed.

- clone project by running ```git clone https://github.com/martyhora/project-estimate-analyser``` into your DocumentRoot path
- change folder ```cd api/v1``` and run ```composer install```
- run ```npm i``` in the project root
- run ```webpack --watch``` to compile changes in JS a LESS files
- open the project in the browser
