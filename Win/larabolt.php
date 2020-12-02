<?php
graphitihandler();

if (count($argv) < 2) {
    help();
    return;
}
$command = $argv[1];
switch ($command) {
    case 'git':
        setup($argv);
        break;

    default:
        echo "\n  No such commands found, please use -h or --help to open help menu";
}

function help()
{
    echo "  Larabolt is an utility software which helps to set up a Laravel Project\n";
    echo "\n  Commands:";
    echo "\n  git <GitHub repo link> - This clones a github repo containing a Laravel\n                           project from remote Github repository and sets\n                           it in the local machine";
    echo "\n\n  Help                   - Shows this menu";
    echo "\n\n  Version 1.1\n  Developed By NeilKerman\n  Github Link:  https://github.com/NeilKerman/LaraBolt.git\n  A Star to this repo would be Amazing! :)\n";
}

function setup($argv)
{
    if (count($argv) < 3) {
        echo "  Please add a git link after 'git'";
        return;
    }

    $url = $argv[2];
    $parsed_url = explode("/", $url);
    $name = end($parsed_url);
    $name = explode(".", $name)[0];

    if (file_exists($name)) {
        echo "\n  +- ERROR: Project already exists in the path";
        return;
    }

    echo "\n  ~:------ Running Git Clone ------:~\n";

    exec('git clone ' . $url);


    echo "\n  ~:------ Git Clone complete ------:~\n";

    echo "\n  ~:------ Installing Packages ------:~\n";

    exec('cd ' . $name . '/ && composer install');

    echo "\n  ~:------ Package Installation Complete ------:~\n";

    echo "\n  ~:------ Setting up project ------:~\n";

    echo "\n   +-- Setting up database...\n";

    $serverurl = "localhost";
    $serverport = "3306";
    $servername = $serverurl.":".$serverport;
    $username = "root";
    $password = "";

    echo "\n     Do you have a database setting other than the defaults?";
    echo "\n      Default Setting:";
    echo "\n      Server URL: localhost";
    echo "\n      Server PORT: 3306";
    echo "\n      Server USERNAME: root";
    echo "\n      Server PASSWORD: ";
    $db_choice = read_data("\n     Enter Choice (Y/N): "); 

    if($db_choice == 'Y' || $db_choice == 'y')
    {
        echo "\n\n     Alright! Please Enter the following details:\n";
        $serverurl = read_data("     Server URL: ");
        $serverport = read_data("     Server PORT: ");
        $username = read_data("     Server USERNAME: ");
        $password = read_data("     Server PASSWORD: ", true);
        $servername = $serverurl.":".$serverport;
        echo "\n";
    }

    $db_name = preg_replace("/[^a-zA-Z]+/", "", $name);

    // Create connection
    $conn = new mysqli($servername, $username, $password);
    // Check connection
    if ($conn->connect_error) {
        die("\n   +- ERROR: Database Connection failed: " . $conn->connect_error);
    }

    // Create database

    $sql = "create database " . $db_name;
    if ($conn->query($sql) === TRUE) {
        echo "\n   +- Database created, Please set the database in .env\nDatabase Name: " . $db_name . "\n";
    } else {
        echo "\n   +- ERROR: " . $conn->error."\n";
    }

    $conn->close();

    echo "\n   +-- Generating .env file...\n";

    writeEnvFile($name, $db_name, $serverurl, $serverport, $username, $password);

    echo "   +- .env file generated.\n";

    echo "\n    !!! WARN: Don't forget to set up correct APP_URL \n";

    echo "\n   +-- Generating Key...\n";

    exec('cd ' . $name . '/ && php artisan key:generate');

    echo "   +- Key Generated! \n";

    echo "\n   +-- Migrating Database...\n";

    exec('cd ' . $name . '/ && php artisan migrate');

    echo "   +- Migration Complete\n";

    echo "\n   +-- Seeding Database...\n";

    exec('cd ' . $name . '/ && php artisan db:seed');

    echo "   +- Seeding Complete\n";

    echo "\n  ~:------ Project Setup Complete ------:~\n";

    echo "\n\n  All the best with the project :)";

    echo "\n  (And some stars on our repo would be amazing!)\n";
}

function writeEnvFile($name, $db_name, $serverurl, $serverport, $username, $password)
{
    $envTxt = "APP_NAME=\"" . $name . "\"\nAPP_ENV=local\nAPP_KEY=\nAPP_DEBUG=true\nAPP_URL=http://localhost/" . $name . "\nASSET_URL=\"\${APP_URL}/public\"\nLOG_CHANNEL=stack\n\nDB_CONNECTION=mysql\nDB_HOST=".$serverurl."\nDB_PORT=".$serverport."\nDB_DATABASE=" . $db_name . "\nDB_USERNAME=".$username."\nDB_PASSWORD=".$password."\n\nBROADCAST_DRIVER=log\nCACHE_DRIVER=file\nQUEUE_CONNECTION=sync\nSESSION_DRIVER=file\nSESSION_LIFETIME=120\n\nREDIS_HOST=127.0.0.1\nREDIS_PASSWORD=null\nREDIS_PORT=6379\n\nMAIL_MAILER=smtp\nMAIL_HOST=smtp.mailtrap.io\nMAIL_PORT=2525\nMAIL_USERNAME=null\nMAIL_PASSWORD=null\nMAIL_ENCRYPTION=null\nMAIL_FROM_ADDRESS=null\nMAIL_FROM_NAME=\"\${APP_NAME}\"\n\nAWS_ACCESS_KEY_ID=\nAWS_SECRET_ACCESS_KEY=\nAWS_DEFAULT_REGION=us-east-1\nAWS_BUCKET=\n\nPUSHER_APP_ID=\nPUSHER_APP_KEY=\nPUSHER_APP_SECRET=\nPUSHER_APP_CLUSTER=mt1\n\nMIX_PUSHER_APP_KEY=\"\${PUSHER_APP_KEY}\"\nMIX_PUSHER_APP_CLUSTER=\"\${PUSHER_APP_CLUSTER}\"\n\nADMIN_EMAIL=\n";
    $env = fopen($name . "\.env", "w") or die("Unable to open file!");
    fwrite($env, $envTxt);
    fclose($env);
}

function graphitihandler()
{
    $counter = rand(1, 4);

    switch ($counter) {
        case 1:
            firegraphiti();
            break;

        case 2:
            sweetgraphiti();
            break;

        case 3:
            lilghostgraphiti();
            break;

        case 4:
            wetgraphiti();
            break;
    }
}

function read_data($message = 'prompt: ', $hidden = false) {
    if (PHP_SAPI !== 'cli') {
        return false;
    }
    echo $message;
    $ret = 
        $hidden
        ? exec(
            PHP_OS === 'WINNT' || PHP_OS === 'WIN32'
            ? __DIR__ . '\prompt_win.bat'
            : 'read -s PW; echo $PW'
        )
        : rtrim(fgets(STDIN), PHP_EOL)
    ;
    if ($hidden) {
        echo PHP_EOL;
    }
    return $ret;
}

function firegraphiti()
{
    echo "\n";
    echo "   (                                          \n";
    echo "   )\ )                     (        (     )  \n";
    echo "  (()/(     )  (       )  ( )\       )\ ( /(  \n";
    echo "   /(_)) ( /(  )(   ( /(  )((_)  (  ((_))\()) \n";
    echo "  (_))   )(_))(()\  )(_))((_)_   )\  _ (_))/  \n";
    echo "  | |   ((_)_  ((_)((_)_  | _ ) ((_)| || |_   \n";
    echo "  | |__ / _` || '_|/ _` | | _ \/ _ \| ||  _|  \n";
    echo "  |____|\__,_||_|  \__,_| |___/\___/|_| \__|  \n";
    echo "                                              \n";
    echo " ~|*.*.*.* Developed by NeilKerman *.*.*.*|~ \n";
    echo "\n";
}

function sweetgraphiti()
{
    echo "\n";
    echo "  ___                                 ___                ___   ___      \n";
    echo " (   )                               (   )              (   ) (   )     \n";
    echo "  | |    .---.   ___ .-.      .---.   | |.-.     .--.    | |   | |_     \n";
    echo "  | |   / .-, \ (   )   \    / .-, \  | /   \   /    \   | |  (   __)   \n";
    echo "  | |  (__) ; |  | ' .-. ;  (__) ; |  |  .-. | |  .-. ;  | |   | |      \n";
    echo "  | |    .'`  |  |  / (___)   .'`  |  | |  | | | |  | |  | |   | | ___  \n";
    echo "  | |   / .'| |  | |         / .'| |  | |  | | | |  | |  | |   | |(   ) \n";
    echo "  | |  | /  | |  | |        | /  | |  | |  | | | |  | |  | |   | | | |  \n";
    echo "  | |  ; |  ; |  | |        ; |  ; |  | '  | | | '  | |  | |   | ' | |  \n";
    echo "  | |  ' `-'  |  | |        ' `-'  |  ' `-' ;  '  `-' /  | |   ' `-' ;  \n";
    echo " (___) `.__.'_. (___)       `.__.'_.   `.__.    `.__.'  (___)   `.__.   \n";
    echo "                                                                        \n";
    echo " ~|*.*.*.*.*.*.*.*.*.*  Developed by NeilKerman  *.*.*.*.*.*.*.*.*.*|~ \n";
    echo "\n";
}

function lilghostgraphiti()
{
    echo "\n";
    echo "           (`-')  _    (`-')  (`-')  _ <-.(`-')                     (`-')      \n";
    echo "    <-.    (OO ).-/ <-.(OO )  (OO ).-/  __( OO)      .->      <-.   ( OO).->   \n";
    echo "  ,--. )   / ,---.  ,------,) / ,---.  '-'---.\ (`-')----.  ,--. )  /    '._   \n";
    echo "  |  (`-') | \ /`.\ |   /`. ' | \ /`.\ | .-. (/ ( OO).-.  ' |  (`-')|'--...__) \n";
    echo "  |  |OO ) '-'|_.' ||  |_.' | '-'|_.' || '-' `.)( _) | |  | |  |OO )`--.  .--' \n";
    echo " (|  '__ |(|  .-.  ||  .   .'(|  .-.  || /`'.  | \|  |)|  |(|  '__ |   |  |    \n";
    echo "  |     |' |  | |  ||  |\  \  |  | |  || '--'  /  '  '-'  ' |     |'   |  |    \n";
    echo "  `-----'  `--' `--'`--' '--' `--' `--'`------'    `-----'  `-----'    `--'    \n";
    echo "\n";
    echo " ~|*.*.*.*.*.*.*.*.*.*.*.* Developed by NeilKerman *.*.*.*.*.*.*.*.*.*.*.*|~  \n";
    echo "\n";
}

function wetgraphiti()
{
    echo "\n";
    echo "  ,-.      .--.  ,---.    .--.  ,---.    .---.  ,-.  _______  \n";
    echo "  | |     / /\ \ | .-.\  / /\ \ | .-.\  / .-. ) | | |__   __| \n";
    echo "  | |    / /__\ \| `-'/ / /__\ \| |-' \ | | |(_)| |   )| |    \n";
    echo "  | |    |  __  ||   (  |  __  || |--. \| | | | | |  (_) |    \n";
    echo "  | `--. | |  |)|| |\ \ | |  |)|| |`-' /\ `-' / | `--. | |    \n";
    echo "  |( __.'|_|  (_)|_| \)\|_|  (_)/( `--'  )---'  |( __.'`-'    \n";
    echo "  (_)                (__)      (__)     (_)     (_)           \n";
    echo "\n";
    echo " ~|*.*.*.*.*.*.*  Developed by NeilKerman  *.*.*.*.*.*.*|~   \n";
    echo "\n";
}
