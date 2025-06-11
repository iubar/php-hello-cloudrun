<?php

// Carica il file autoload di Composer
require 'vendor/autoload.php';

use Dotenv\Dotenv;

/**
 * @return array<string, string> La configurazione del database.
 */
function loadConfig(): array {
	$config = [];

	// $debug = getenv("DEBUG"); // false

	$envFile = __DIR__ . '/.env';
 
	if (is_file($envFile) && is_readable($envFile)) {
	    echo 'INFO : reading env file ' . $envFile .  ' ...' . PHP_EOL;
		// Crea l'istanza di Dotenv e carica il file .env
	    $path_parts = pathinfo($envFile);
		$dirname = $path_parts['dirname'];
		$basename = $path_parts['basename'];
		$dotenv = Dotenv::createImmutable($dirname, $basename);
		$dotenv->load();
	} else {
        echo 'WARNING : can\'t read the config file ' . $envFile . PHP_EOL;
	}
 
	$host = readEnv('DB_HOST');
	$dbname = readEnv('DB_NAME');
	$username = readEnv('DB_USER');
	$password = readEnv('DB_PASS');
	
	if (!$host || !$dbname) {
		echo 'Please set the right env variables for the db connection.' . PHP_EOL;
		exit(1);
	}

	$config = [
		'DB_HOST' => $host,
	    'DB_NAME' => $dbname,
		'DB_USER' => $username,
		'DB_PASS' => $password,	    
	];

	return $config;
}

function readEnv(string $varName): string {
    $value = '';
    if (isset($_ENV[$varName])) {
        $value = mixedToString($_ENV[$varName]);
    } else if (isset($_SERVER[$varName])) {
        $value = mixedToString($_SERVER[$varName]);
    }else{
        $value = getenv($varName);
        $value = $value !== false ? $value : '';
    }
    return $value;
}

function mixedToString(mixed $value): string {
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
        <p class="<?php echo $connStatus ? 'success' : 'error'; ?>">
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
        <?php } else { ?>
            <p>Could not retrieve IP range information.</p>
        <?php } ?>
    <?php } else { ?>
        <p>Could not determine outbound IP address.</p>
    <?php } ?>    

</body>
</html>
