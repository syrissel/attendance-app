# Attendance App

Attendance App is an application built using PHP, and is intended to log employee's work times and provided useful reports for managers. Some functionality was designed to work with older JavaScript versions particularly with older iPad devices running iOS 9.3.1 in an effort to reuse older technology and divert electronic waste.

## How it works
- Users clock-in at the beginning of the work day and clock-out when they are done. Their total hours are determined by the time between their clock-in and clock-out times.
- Users can also punch in and out for breaks, but these are considered paid breaks and will not be deducted off their hours.
- A 30 minute lunch break will be deducted if a user has worked 5 hours or more.

## Reports
Managers can view a variety of useful reports including:
- Late report
- Employee clock-in, clock-out, and break times
- Who is currently in the building
- Payroll
- Guest activity
- Employee biweekly hours breakdown

## User management
Managers can choose to edit an employee's profile:
- Set expected clock-in and clock-out times
- Set expected biweekly work hours
- Edit or create attendance records

## Technologies used
- Objected-oriented PHP
- jQuery
- Bootstrap
- MySQL Database
- Composer
- NPM
- Webpack

## Third-party libraries
- [jQuery Date Range Picker](https://longbill.github.io/jquery-date-range-picker/)
- [PHP dotenv](https://github.com/vlucas/phpdotenv)
- [MySQLDump](https://github.com/ifsnop/mysqldump-php)
- [Summernote](https://github.com/summernote/summernote)
- [ClockPicker (jQuery)](https://github.com/weareoutman/clockpicker)
- [Full Calendar](https://fullcalendar.io/)
