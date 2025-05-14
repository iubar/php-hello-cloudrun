<?php

namespace App;

class IpUtils {

/**
 * Function to get public IP by contacting an external service
 */
public function getPublicIp() : string {
    $services = [
        'https://api.ipify.org',
        'https://ifconfig.me/ip',
        'https://icanhazip.com',
        'https://checkip.amazonaws.com'
    ];

    foreach ($services as $service) {
        $ip = @file_get_contents($service);
        if ($ip !== false && filter_var(trim($ip), FILTER_VALIDATE_IP)) {
            return trim($ip);
        }
    }
    return '';
}

/**
 * Function to get IP range (CIDR) using an external API
 * 
 * Example API response for 8.8.8.8 from https://ipinfo.io/8.8.8.8/json
 * {
 *   "ip": "8.8.8.8",
 *   "hostname": "dns.google",
 *   "city": "Mountain View",
 *   "region": "California",
 *   "country": "US",
 *   "loc": "37.4056,-122.0775",
 *   "org": "AS15169 Google LLC",
 *   "postal": "94043",
 *   "timezone": "America/Los_Angeles",
 *   "readme": "https://ipinfo.io/missingauth"
 * }
 *  
 * @return array<string, string>
 *      
 */
public function getIpRange(string $ip) : array {
    if($ip){
        $apiUrl = "https://ipinfo.io/{$ip}/json"; // Using ipinfo.io (free tier limits)
        $response = @file_get_contents($apiUrl);
    
        if ($response !== false) {
            $data = json_decode($response, true);
            if (is_array($data)) {
                if (isset($data['org']) && isset($data['cidr'])) {
                    assert(is_string($data['org']));
                    assert(is_string($data['cidr']));
                    return [
                        'organization' => (string) $data['org'],
                        'cidr' => (string) $data['cidr']
                    ];
                }
                // If CIDR is not directly available, fallback to prefix from ipinfo.io
                if (isset($data['ip'])) {
                    return [
                        'organization' => $data['org'] ?? 'Unknown',
                        'cidr' => $data['ip'] . '/32' // single IP as CIDR
                    ];
                }
            }
        }
    }
    return [];
}

/**
 * Cidr (Classless Inter-Domain Routing)
 * 
 * /32	1 IP esatto	1 indirizzo
 * /31	Piccolissima subnet	2 indirizzi
 * /30	Subnet da 4 indirizzi	4 indirizzi
 * /24	Una "classe C" (es: 192.168.1.0/24)	256 indirizzi
 * /16	Rete più grande (es: 192.168.0.0/16)	65.536 indirizzi
 * 
 * @param string $netmask
 * @return int
 */
public function netmaskToCidr(string $netmask): int {
    $binaryMask = '';
    $netmaskParts = explode('.', $netmask);    
    foreach ($netmaskParts as $part) {
        $binaryMask .= str_pad(decbin((int)$part), 8, '0', STR_PAD_LEFT);
    }    
    return substr_count($binaryMask, '1');
}

/**
 * 
 * @param string $ip
 * @param string $subnetMask
 * @return array<string, string|bool>
 */
public  function getNetworkRange(string $ip, string $subnetMask = '255.255.255.0') : array {
    // Convert IP and subnet mask to long integers
    $ipLong = ip2long($ip);
    $maskLong = ip2long($subnetMask);
    
    // Calculate the network address (AND operation)
    $networkLong = $ipLong & $maskLong;
        
    if($maskLong){
        // Calculate the broadcast address (OR operation with inverted mask)
        $broadcastLong = $networkLong | (~$maskLong & 0xFFFFFFFF);
        // Convert network and broadcast addresses back to human-readable IP format
        $network = long2ip($networkLong);
        $broadcast = long2ip($broadcastLong);
        return ['network' => $network, 'broadcast' => $broadcast];
    }
    return ['network' => false, 'broadcast' => false];

}


}
