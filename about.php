<?php?>

<!doctype html>
<html>
<head>
    <style>
        .about a {
            color: #3E6ADF !important;
        }

        .about a:hover {
            text-decoration: none !important;
            transition: color 0.5s;
            color: #FF9B00 !important;
        }

        .col-6 {
            background: #F8F8F8 !important;
        }
    </style>
</head>
<body>
    <?php include('nav.php') ?>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-12 col-md-6 about">
                <h5>About the developer</h5>
                <p style="text-indent:1rem;">
                    This app was written by <a href="http://www.stephanemireault.ca">Steph Mireault</a>. He received his technical training at Red River College
                    (Business Information Technology program) and graduated in 2019; he was recruited by [REDACTED] later that year.
                    During his time at RRC, he was trained in the ways of computers. Most notably object-oriented programming, full-stack web development,
                    and Android app development.
                    In his spare time, Steph likes to be running on a not-too-hot day with a cool breeze and waving 'Hello' to any cats he sees along the path.
                </p>
                <h5>About the app</h5>
                <ul>
                    <li>Built with PHP, Bootstrap, MySql, and jQuery</li>
                    <li>Designed to work on older iPads (iOS 9.3.5)</li>
                    <li>Allows employees to log their work hours</li>
                    <li>Lets administrators manage attendance records and view reports</li>
                </ul>
                <h5 class="">Other libraries</h5>
                <table class="table" style="table-layout:fixed;">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>License</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><a href="https://longbill.github.io/jquery-date-range-picker/">jQuery Date Range Picker</a></td><td><a href="https://opensource.org/licenses/MIT">MIT</a></td>
                    </tr>
                    <tr>
                        <td><a href="https://github.com/vlucas/phpdotenv">PHP dotenv</a></td><td><a href="https://opensource.org/licenses/BSD-3-Clause">BSD 3-Clause</a></td>                    
                    </tr>
                    <tr>
                        <td><a href="https://github.com/ifsnop/mysqldump-php">MySQLDump - PHP</a></td><td><a href="https://opensource.org/licenses/GPL-3.0">GNU General Public License v3.0</a></td>
                    </tr>
                    <tr>
                        <td><a href="https://github.com/summernote/summernote">Summernote - super simple WYSIWYG editor</a></td><td><a href="https://opensource.org/licenses/MIT">MIT</a></td>
                    </tr>
                    <tr>
                        <td><a href="https://github.com/weareoutman/clockpicker">ClockPicker (jQuery)</a></td>
                        <td><a href="https://opensource.org/licenses/MIT">MIT</a></td>
                    </tr>
                </tbody>
                </table>
            </div>
            <div class="col-md-3"></div>
        </div><!--row-->
    </div><!--container-->
    <?php include('footer.php') ?>
</body>
</html>
