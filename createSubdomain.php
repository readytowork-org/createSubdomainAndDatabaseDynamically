<?php
//AUTHOR : YASHWINDAR SINGH
//THIS CAN BE USED TO CREATE SUBDOMAINS AND DATABASE 
//YOU CAN MAKE IT A HELPER FUNCTION TO CREATE SUBDOMAINS AND DATABASE
function createSubdomain($subDomain)
{
    $database_name = $subDomain; //without prefix
    $database_user = 'landlord'; //database name and database username are both similar, change the value if you want
    $database_pass = env('DB_PASSWORD');
    $cpanel_username = env('CPANEL_USERNAME');
    $cpanel_pass = env('CPANEL_PASSWORD');
    $cpanel_theme = "paper_lantern";

    //Create Db
    createDb($cpanel_theme, $cpanel_username, $cpanel_pass, $database_name);

    //Create User
    createUser($cpanel_theme, $cpanel_username, $cpanel_pass, $database_user, $database_pass);

    //Add user to DB - ALL Privileges
    addUserToDb($cpanel_theme, $cpanel_username, $cpanel_pass, $database_user, $database_name, 'ALL PRIVILEGES');

    $buildRequest = "/frontend/paper_lantern/subdomain/doadddomain.html?rootdomain=" . env('ROOT_DOMAIN') . "&domain=" . $subDomain . "&dir=/subdomain/directory";

    $openSocket = fsockopen('localhost', 2082);
    if (!$openSocket) {
        return "Socket error";
        exit();
    }

    $authString = env('CPANEL_USERNAME') . ":" . env('CPANEL_PASSWORD');
    $authPass = base64_encode($authString);
    $buildHeaders  = "GET " . $buildRequest . "\r\n";
    $buildHeaders .= "HTTP/1.0\r\n";
    $buildHeaders .= "Host:localhost\r\n";
    $buildHeaders .= "Authorization: Basic " . $authPass . "\r\n";
    $buildHeaders .= "\r\n";

    fputs($openSocket, $buildHeaders);
    while (!feof($openSocket)) {
        fgets($openSocket, 128);
    }
    fclose($openSocket);
    //current tenant
    Artisan::call('tenants:artisan "migrate --database=tenant"');
}

function createApidomain($subDomain)
{

    $buildRequest = "/frontend/paper_lantern/subdomain/doadddomain.html?rootdomain=" . env('ROOT_DOMAIN') . "&domain=" . $subDomain . "&dir=/public_html/";

    $openSocket = fsockopen('localhost', 2082);
    if (!$openSocket) {
        return "Socket error";
        exit();
    }

    $authString = env('CPANEL_USERNAME') . ":" . env('CPANEL_PASSWORD');
    $authPass = base64_encode($authString);
    $buildHeaders  = "GET " . $buildRequest . "\r\n";
    $buildHeaders .= "HTTP/1.0\r\n";
    $buildHeaders .= "Host:localhost\r\n";
    $buildHeaders .= "Authorization: Basic " . $authPass . "\r\n";
    $buildHeaders .= "\r\n";

    fputs($openSocket, $buildHeaders);
    while (!feof($openSocket)) {
        fgets($openSocket, 128);
    }
    fclose($openSocket);
}
// change this to "x3" if you don't have paper_lantern yet

function createDb($cpanel_theme, $cPanelUser, $cPanelPass, $dbName)
{
    $buildRequest = "/frontend/" . $cpanel_theme . "/sql/addb.html?db=" . $dbName;

    $openSocket = fsockopen('localhost', 2082);
    if (!$openSocket) {
        return "Socket error";
        exit();
    }

    $authString = $cPanelUser . ":" . $cPanelPass;
    $authPass = base64_encode($authString);
    $buildHeaders = "GET " . $buildRequest . "\r\n";
    $buildHeaders .= "HTTP/1.0\r\n";
    $buildHeaders .= "Host:localhost\r\n";
    $buildHeaders .= "Authorization: Basic " . $authPass . "\r\n";
    $buildHeaders .= "\r\n";

    fputs($openSocket, $buildHeaders);
    while (!feof($openSocket)) {
        fgets($openSocket, 128);
    }
    fclose($openSocket);
}

function createUser($cpanel_theme, $cPanelUser, $cPanelPass, $userName, $userPass)
{
    $buildRequest = "/frontend/" . $cpanel_theme . "/sql/adduser.html?user=" . $userName . "&pass=" . $userPass;

    $openSocket = fsockopen('localhost', 2082);
    if (!$openSocket) {
        return "Socket error";
        exit();
    }

    $authString = $cPanelUser . ":" . $cPanelPass;
    $authPass = base64_encode($authString);
    $buildHeaders = "GET " . $buildRequest . "\r\n";
    $buildHeaders .= "HTTP/1.0\r\n";
    $buildHeaders .= "Host:localhost\r\n";
    $buildHeaders .= "Authorization: Basic " . $authPass . "\r\n";
    $buildHeaders .= "\r\n";

    fputs($openSocket, $buildHeaders);
    while (!feof($openSocket)) {
        fgets($openSocket, 128);
    }
    fclose($openSocket);
}

function addUserToDb($cpanel_theme, $cPanelUser, $cPanelPass, $userName, $dbName, $privileges)
{

    /* Redefine prefix for user and dbname */
    $prefix = substr($cPanelUser, 0, 8);

    $buildRequest = "/frontend/" . $cpanel_theme . "/sql/addusertodb.html?user=" . $prefix . "_" .
        $userName . "&db=" . $prefix . "_" . $dbName . "&privileges=" . $privileges;

    $openSocket = fsockopen('localhost', 2082);
    if (!$openSocket) {
        return "Socket error";
        exit();
    }

    $authString = $cPanelUser . ":" . $cPanelPass;
    $authPass = base64_encode($authString);
    $buildHeaders = "GET " . $buildRequest . "\r\n";
    $buildHeaders .= "HTTP/1.0\r\n";
    $buildHeaders .= "Host:localhost\r\n";
    $buildHeaders .= "Authorization: Basic " . $authPass . "\r\n";
    $buildHeaders .= "\r\n";

    fputs($openSocket, $buildHeaders);
    while (!feof($openSocket)) {
        fgets($openSocket, 128);
    }
    fclose($openSocket);
}
?>