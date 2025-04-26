<?php

namespace App;

class Database
{
    private string $host = "";
    private string $username = "";
    private string $password = "";
    private string $dbname = "";
    private \PDO $pdo;

    /**
     * 
     * @param array<string, string> $config
     */
    public function __construct(array $config)
    {
        // Carica la configurazione dal file ini
        $this->host = $config["host"];
        $this->username = $config["username"];
        $this->password = $config["password"];
        $this->dbname = $config["dbname"];

        $result = $this->checkPdoDrivers();
        if(!$result['status']){
            echo $result['message'] . PHP_EOL;
            exit(1);
        }
    }

    /**
     * @return array<string, bool|string>
     */
    private function checkPdoDrivers(): array {
        $isPdo = false;
        $message = '';
        
        if (!extension_loaded("pdo")) {
            $message = "❌ Estensione PDO non è caricata";
        }

        $drivers = \PDO::getAvailableDrivers();
        // echo "✅ Estensione PDO caricata" . PHP_EOL;

        if (in_array("mysql", $drivers)) {
            $message = "✅ Driver PDO MySQL disponibile";
            $isPdo = true;
        } else {
            $message = "❌ Driver PDO MySQL non trovato";
        }
        return ['status' => $isPdo, 'message' => $message];
    }

    // Crea una connessione al database utilizzando PDO
    public function connect(bool $useSsl = false): \PDO {
        
        $charset = 'utf8mb4';
        
        /**
         * 1) Some servers (like AWS RDS, Google Cloud SQL) only require the CA file — no client cert/key needed.
         * 2) Your MySQL server must be configured to accept SSL connections (require_secure_transport = ON in my.cnf).
         */
        $options = [
            \PDO::MYSQL_ATTR_SSL_CA     => '/path/to/ca-cert.pem',    // Certificate Authority
            //\PDO::MYSQL_ATTR_SSL_CERT   => '/path/to/client-cert.pem', // Client certificate
            //\PDO::MYSQL_ATTR_SSL_KEY    => '/path/to/client-key.pem',  // Client private key
            \PDO::ATTR_ERRMODE          => \PDO::ERRMODE_EXCEPTION,
        ];
        
        
        $dsn = "mysql:host={$this->host};dbname={$this->dbname}";
        // $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=$charset";
        
        if(!$useSsl){
            $this->pdo = new \PDO($dsn, $this->username, $this->password);
        }else{
            $this->pdo = new \PDO($dsn, $this->username, $this->password, $options);
        }
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        return $this->pdo;
    }
    
    /**
     * @return array<string, int|string>
     */
    public function open() : array {
        $connStatus = -1;
        $message = "⚠️ Connection status unknown";
        // Connessione al database
        try {
            $this->connect();
            $connStatus = 1;
            $message = "✔️ 🟢 Connection successful";
        } catch (\PDOException $e) {
            $connStatus = 0;
            $message = "❌ 🔴 Connection failed: " . $e->getMessage();
        }
        return ['status' => $connStatus, 'message' => $message];
    }
    
    /**
     * Common problems:
     * 
     * - Wrong file paths ➔ Check permissions and existence of .pem files.
     * - Certificate mismatches ➔ CA must match the server’s SSL cert.
     * - Server not configured for SSL ➔ You must configure MySQL server (ssl-ca, ssl-cert, ssl-key).
     * - If using AWS RDS / Azure Database / GCP SQL ➔ Use their provided CA bundle!
     * 
     * @return array<string, bool|string>
     */
    public function checkSslConn() : array {
        $isSsl = false;
        $message = '';
        $stm = $this->pdo->query("SHOW STATUS LIKE 'Ssl_cipher'");
        if(!$stm){
            $message = "❌ 🔴 Query error !";
        }else{
            $result = $stm->fetch(\PDO::FETCH_ASSOC);
            if (is_array($result) && !empty($result['Value'])) {
                assert(is_string($result['Value'])); // Gli assert() vengono eseguiti solo se zend.assertions = 1 (oppure se vale 0 sono compilati ma non eseguiti)
                $message = "✔️ 🟢 SSL connection is active. Cipher: " . $result['Value'];
                $isSsl = true;
            } else {
                $message = "❌ 🔴 SSL is NOT active. Connection is NOT secured with SSL.";
            }
        }
        return ['ssl' => $isSsl, 'message' => $message];
    }
    
}
