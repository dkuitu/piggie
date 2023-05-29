# piggie
PHP lab for special topic
Getting Started
Below are the steps to get started with the Piggie project.

Prerequisites
Before you begin, ensure you have met the following requirements:

PHP Environment: You need a web server environment that supports PHP. You can use environments such as XAMPP, WAMP, or MAMP for Windows, Mac, and Linux respectively. Alternatively, you can use a standalone PHP interpreter if you are comfortable configuring it.

Python: The project also includes Python scripts. Make sure you have Python3 installed on your machine.

SQL Database: The PHP scripts interact with a SQL database. You should have a SQL database ready for use. MySQL or MariaDB are good options for this.

Redis: Some of the PHP scripts interact with Redis, so you should have a Redis server installed and configured.

Installation
Clone the repository: First, clone this repository to your local machine using git clone https://github.com/dkuitu/piggie.git.

Configure the Database: Import the SQL structure to your database. Note that this repository does not include a SQL file, so you might need to create your own structure based on the PHP scripts. Update the database.php file with your database connection details.

Configure Redis: Update the redis.php script with your Redis connection details.

Run the Server: If you are using a web server environment like XAMPP, place the repository in the htdocs (or equivalent) directory and start the server. If you are using a standalone PHP interpreter, navigate to the directory containing the repository and start the PHP server using php -S localhost:8000 (or any port of your choice).

Access the Scripts: You can now access the PHP scripts in your browser by visiting localhost:8000/<script_name>.php (replace <script_name> with the name of the script you want to access).

Please note that these scripts are designed to be run on the server side. They will not function properly if opened as local files in a web browser.

Usage
You can use these scripts as a starting point for a web-based game. Each script performs a different function in the game, such as starting a game, making a purchase, or playing a round of the game. You might need to integrate these scripts with a front-end interface to make them usable for end users.

Contributions
Contributions are welcome. Please fork this repository and create a pull request if you have something you'd like to add or change.

Disclaimer
This project is a lab for a special topic and may not function as a complete, production-ready game. Use it at your own risk​1​.
