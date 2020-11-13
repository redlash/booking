
## About this project

This project is a web application to book meeting rooms. The following functionality is provided:
- Register, login, logout
- View current booking records
- Book a meeting room
- Update an existing booking record
- Cancel a booking record
- Filter the records by users, meeting rooms, and date range
- Sort the records by date/time, users, and meeting rooms

## How to setup local environment
- Get the source code and set the env: (Valet users need to connect to MySQL and create the database)
```$xslt
git clone https://github.com/redlash/booking.git
cd booking
npm install && npm run dev
vagrant up -d (Vagrant users only, you may need to run vagrant init to create Vagrantfile first)
vagrant ssh (Vagrant users only)
composer install
cp .env.example .env
php artisan key:generate
```
- Go to the project directory and run migration and seeder:
```$xslt

php artisan migrate --seed
```
Two users and three meeting rooms will be generate, the password of the users are <i>password</i>
You may create you own user by registering as well.

- Provision the service:
  + Valet users run below commands, in browser access to <u>http://booking.test</u>:
  ```$xslt
  valet park
  ```
  + Homestead users access to <u>http://homestead.test</u> in browser

## License

The project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
