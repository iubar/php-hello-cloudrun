<?php

// Carica il file autoload di Composer
require "vendor/autoload.php";

use Dotenv\Dotenv;

/**
 * @return array<string, string> La configurazione del database.
 */
function loadConfig(): array {
    $config = [];

    // $debug = getenv("DEBUG"); // false
 
    $file = __DIR__ . '/.env';
    
    $host = getenvOrEmpty("DB_HOST");
    $dbname = getenvOrEmpty("DB_NAME");
    $username = getenvOrEmpty("DB_USER");
    $password = getenvOrEmpty("DB_PASS");
    
    if(!$host && !$dbname){
        if(is_file($file) && is_readable($file)){
            // Crea l'istanza di Dotenv e carica il file .env
            $path_parts = pathinfo($file);	
            $dirname =  $path_parts['dirname'];
            $basename =  $path_parts['basename'];
            $dotenv = Dotenv::createImmutable($dirname, $basename);
            $dotenv->load();
            
            $host = (string) readEnv("DB_HOST");
            $dbname = (string) readEnv("DB_NAME");
            $username = (string) readEnv("DB_USER");
            $password = (string) readEnv("DB_PASS");
        }else{
            echo "Error : can't read the config file $file " . PHP_EOL;
        }
    }
        
    if(!$host || !$dbname){
        echo "Please set the right env variables for the db connection." . PHP_EOL;
        exit(1);
    }
    
    $config = [
        'host' => $host,
        'username' => $username,
        'password' => $password,
        'dbname' => $dbname,
    ];
  
    return $config;
}


function readEnv(string $name) : string {
    $value = '';
    // $value = getenv($name); // Using getenv() and putenv() is strongly discouraged due to the fact that these functions are not thread safe.
if (isset($_ENV[$name])) {
        $value = mixedToString($_ENV[$name]);
} else if (isset($_SERVER[$name])) {
         $value = mixedToString($_SERVER[$name]); 
} 
    return $value;
}
 
function mixedToString(mixed $value) : string {
    if (is_string($value)) {
        return $value;
    }
    if (is_null($value)) {
        return '';
    }
    if (is_bool($value)) {
        return $value ? '1' : '';
    }
    if (is_int($value) || is_float($value)) {
        return (string) $value;
    }
    if (is_object($value) && method_exists($value, '__toString')) {
        return (string) $value;
    }
    if (is_array($value)) {
        // Se vuoi puoi serializzare o json_encode
        $encoded = json_encode($value);
        return $encoded !== false ? $encoded : '';
    }

    // Per sicurezza, fallback
    return '';
}

function stringToBooleanOrNull(string $str): ?bool {
    $str = strtolower(trim($str));
    $trueValues = ['1', 'true', 'yes', 'on'];
    $falseValues = ['0', 'false', 'no', 'off'];

    if (in_array($str, $trueValues, true)) {
        return true;
    }
    if (in_array($str, $falseValues, true)) {
        return false;
    }
    // Non rappresenta un booleano valido
    return null;
}

function isBoolean(string $str): bool {
    $str = strtolower(trim($str));
    $trueValues = ['1', 'true', 'yes', 'on'];
    $falseValues = ['0', 'false', 'no', 'off'];

    if (in_array($str, $trueValues, true)) {
        return true;
    }
    if (in_array($str, $falseValues, true)) {
        return true;
    }
    // Non rappresenta un booleano valido
    return false;
}

/**
 * Il metodo permette di superare il type check di phpstan
 */
function getenvOrEmpty(string $varName) : string {
    // Usa getenv() per ottenere il valore della variabile
    $value = getenv($varName);
    // Se la variabile d'ambiente non è settata, restituisci una stringa vuota
    return $value !== false ? $value : '';
}

// Imposta il fuso orario corretto se necessario
date_default_timezone_set('Europe/Rome');

// Ottiene la data e ora attuale formattata
$timestamp = date('Y-m-d H:i:s');

$config = loadConfig();

// Crea un'istanza della classe Database
use App\Database;
use App\IpUtils;
$db = new Database($config);
$result = $db->open();
$connStatus = $result['status'];
$connMessage = $result['message'];

$ip_utils = new IpUtils();
$ip = $ip_utils->getPublicIp();
$rangeInfo = $ip_utils->getIpRange($ip);

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifica Connessione al DB</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .status { font-weight: bold; margin-top: 20px; }
        .error { color: red; }
        .success { color: green; }
        form { margin-top: 20px; }
        label, input { display: block; margin: 5px 0; }
        input[type="text"], input[type="password"] { width: 100%; padding: 8px; }
        button { padding: 10px 15px; background-color: #4CAF50; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>

    <h1>Check MySql database connection</h1>
    <div>
        <p>
            Dbms host : <?php echo $config['host']; ?>
        </p>
       	<p>
           Time : <?php echo $timestamp; ?>
        </p>
    </div>
    <!-- Mostra lo stato della connessione -->
    <div class="status">
        <p class="<?php echo $connStatus ? "success" : "error"; ?>">
            <?php echo $connMessage; ?>
        </p>
    </div>
    
    <hr />
    
    <h1>Server Outbound IP Information</h1>
    <?php if ($ip) { ?>
        <p><strong>Outbound IP:</strong> <?php echo htmlspecialchars($ip); ?></p>
        <?php if ($rangeInfo) { ?>
            <p><strong>Organization:</strong> <?php echo htmlspecialchars($rangeInfo['organization']); ?></p>
            <p><strong>IP Range (CIDR):</strong> <?php echo htmlspecialchars($rangeInfo['cidr']); ?></p>
        <?php }else{ ?>
            <p>Could not retrieve IP range information.</p>
        <?php } ?>
    <?php }else{ ?>
        <p>Could not determine outbound IP address.</p>
    <?php } ?>    

</body>
</html>
