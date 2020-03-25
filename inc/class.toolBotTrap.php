<?php

/**
 *   @author          Black Cat Development
 *   @copyright       2020 Black Cat Development
 *   @link            https://blackcat-cms.org
 *   @license         http://www.gnu.org/licenses/gpl.html
 *   @category        CAT_Modules
 *   @package         toolBotTrap
 **/

namespace CAT\Addon;

use \CAT\Base as Base;

if(!class_exists('\CAT\Addon\Tool\toolBotTrap',false))
{
    final class toolBotTrap extends \CAT\Addon\Tool
    {
        protected static $type        = 'tool';
        protected static $directory   = 'toolBotTrap';
        protected static $name        = 'Bot Trap';
        protected static $version     = '0.1';
        protected static $description = "";
        protected static $author      = "BlackCat Development";
        protected static $guid        = "";
        protected static $license     = "GNU General Public License";

        public static function install() : array
        {
            $errors = parent::install();
            self::db()->query(
                'CREATE TABLE IF NOT EXISTS `:prefix:mod_bottrap_log` ( '.
                	"`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, ".
                	"`ip` TINYTEXT NOT NULL COLLATE 'utf8mb4_bin', ".
                	"`method` TINYTEXT NOT NULL COLLATE 'utf8mb4_bin', ".
                	"`protocol` TINYTEXT NOT NULL COLLATE 'utf8mb4_bin', ".
                	"`date` TINYTEXT NOT NULL COLLATE 'utf8mb4_bin', ".
                	"`ua` TINYTEXT NOT NULL COLLATE 'utf8mb4_bin', ".
                	"`visits` INT(11) UNSIGNED NOT NULL, ".
                	"`whitelisted` ENUM('Y','N') NOT NULL DEFAULT 'N' COLLATE 'utf8mb4_bin', ".
                	'PRIMARY KEY (`id`) '.
                ') '.
                "COLLATE='utf8mb4_bin' ".
                'ENGINE=InnoDB'
            );
            self::db()->query(
                'CREATE TABLE IF NOT EXISTS `:prefix:mod_bottrap_knownbots` ( '.
                	"`id` int(11) unsigned NOT NULL AUTO_INCREMENT, ".
                	"`name` varchar(50) COLLATE utf8mb4_bin NOT NULL, ".
                	"`whitelisted` enum('Y','N') COLLATE utf8mb4_bin NOT NULL DEFAULT 'N', ".
                	"PRIMARY KEY (`id`) ".
                ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin; "
            );
            self::db()->query(
                'CREATE TABLE IF NOT EXISTS `:prefix:mod_bottrap_settings` ( '.
                	"`site_id` int(11) unsigned NOT NULL, ".
                	"`enabled` enum('Y','N') COLLATE utf8mb4_bin NOT NULL DEFAULT 'N', ".
                	"`route` tinytext COLLATE utf8mb4_bin NOT NULL DEFAULT 'blackhole', ".
                    "UNIQUE INDEX `site_id` (`site_id`), ".
                	"KEY `FK_:prefix:mod_bottrap_settings_:prefix:sites` (`site_id`), ".
                	"CONSTRAINT `FK_mod_bottrap_settings_sites` FOREIGN KEY (`site_id`) REFERENCES `:prefix:sites` (`site_id`) ".
                    "ON DELETE CASCADE ON UPDATE CASCADE ".
                	") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;"
            );
            self::db()->query(
                "INSERT IGNORE INTO `:prefix:mod_bottrap_knownbots` (`id`, `name`, `whitelisted`) VALUES
	(1, 'a6-indexer', 'Y'),
	(2, 'adsbot-google', 'Y'),
	(3, 'ahrefsbot', 'Y'),
	(4, 'aolbuild', 'Y'),
	(5, 'apis-google', 'Y'),
	(6, 'baidu', 'Y'),
	(7, 'bingbot', 'Y'),
	(8, 'bingpreview', 'Y'),
	(9, 'butterfly', 'Y'),
	(10, 'cloudflare', 'Y'),
	(11, 'duckduckgo', 'Y'),
	(12, 'embedly', 'Y'),
	(13, 'facebookexternalhit', 'Y'),
	(14, 'facebot', 'Y'),
	(15, 'googlebot', 'Y'),
	(16, 'ia_archiver', 'Y'),
	(17, 'linkedinbot', 'Y'),
	(18, 'mediapartners-google', 'Y'),
	(19, 'msnbot', 'Y'),
	(20, 'netcraftsurvey', 'Y'),
	(21, 'outbrain', 'Y'),
	(22, 'pinterest', 'Y'),
	(23, 'quora', 'Y'),
	(24, 'rogerbot', 'Y'),
	(25, 'showyoubot', 'Y'),
	(26, 'slackbot', 'Y'),
	(27, 'slurp', 'Y'),
	(28, 'sogou', 'Y'),
	(29, 'teoma', 'Y'),
	(30, 'tweetmemebot', 'Y'),
	(31, 'twitterbot', 'Y'),
	(32, 'urlresolver', 'Y'),
	(33, 'vkshare', 'Y'),
	(34, 'w3c_validator', 'Y'),
	(35, 'wordpress', 'Y'),
	(36, 'wprocketbot', 'Y'),
	(37, 'yandex', 'Y');"
            );
            self::db()->query(
                "INSERT IGNORE INTO `:prefix:mod_bottrap_settings` ".
                "(`site_id`, `enabled`, `route`) VALUES ".
	            "(1, 'N', 'blackhole');"
            );
            return $errors;
        }

        /**
         *
         * @access public
         * @return
         **/
        public static function getKnownBots()
        {
            $result = self::db()->query(
                'SELECT `name`,`whitelisted` FROM `:prefix:mod_bottrap_knownbots`'
            );
            $data = $result->fetchAll();
            $whitelist_ua = $blacklist_ua = array();
            foreach($data as $row) {
                if($row['whitelisted']=='Y') {
                    $whitelist_ua[] = $row['name'];
                } else {
                    $black[] = $row['name'];
                }
            }
            return array($whitelist_ua,$blacklist_ua);
        }   // end function getKnownBots()

        /**
         *
         * @access public
         * @return
         **/
        public static function getWhitelistedIPs()
        {
            $result = self::db()->query(
                'SELECT `ip` FROM `:prefix:mod_bottrap_log` WHERE `whitelisted`=?',
                array('Y')
            );
            $whitelisted_ips = array();
            $data = $result->fetchAll();
            foreach($data as $row) {
                $whitelisted_ips[] = $row['ip'];
            }
            return $whitelisted_ips;
        }   // end function getWhitelistedIPs()

        /**
         *
         * @access public
         * @return
         **/
        public static function tool()
        {
            $stmt = self::db()->query(
                'SELECT * FROM `:prefix:mod_bottrap_settings` WHERE `site_id`=?',
                array(CAT_SITE_ID)
            );
            $settings = $stmt->fetch();
            $result = self::db()->query(
                'SELECT * FROM `:prefix:mod_bottrap_log`'
            );
            $data = $result->fetchAll();
            return self::tpl()->get('tool',array('data'=>$data,'settings'=>$settings));
        }   // end function tool()
        

        /**
         * Handle the bot trap
         *
         * This code is based on the blackhole() function of the
         * https://perishablepress.com/blackhole-bad-bots/
         * project, but as we need a custom handling, we do not use the
         * original method here
         **/
        public static function trap()
        {
            $stmt = self::db()->query(
                'SELECT `enabled` FROM `:prefix:mod_bottrap_settings` WHERE `site_id`=?',
                array(CAT_SITE_ID)
            );
            $res = $stmt->fetch();
            if(!isset($res['enabled']) || $res['enabled']=='N') {
                return;
            }

	        list ($ip, $ua, $request, $protocol, $method, $date, $time) = self::blackhole_get_vars();

            // checks if the visitor is already listed in the blackhole.dat
        	$badbot = self::blackhole_checkbot($ip, $ua, $request, $date);

            // already banned
            if ($badbot > 0) {
                if(self::asJSON()) {
                    \CAT\Helper\Json::printError('You have been banned!');
                } else {
                    self::tpl()->setPath(__DIR__.'/../templates/default');
            		self::tpl()->output(
                        'trap2',
                        array()
                    );
                }
                exit;
            // first visit, save and send mail to admin
        	} elseif ($badbot === 0 && self::router()->getRoute() == self::getSetting('enabled_bottrap_route')) {
                self::db()->query(
                    'INSERT INTO `:prefix:mod_bottrap_log` '
                    . '(`ip`, `method`, `protocol`, `date`, `ua`, `visits`) '
                    . 'VALUES (:ip,:method,:protocol,:date,:ua,"1")',
                    array(
                        ':ip'       => $ip,
                        ':method'   => $method,
                        ':protocol' => $protocol,
                        ':date'     => $date,
                        ':ua'       => $ua
                    )
                );
        		$whois    = self::blackhole_whois();
        		$host     = self::blackhole_sanitize(gethostbyaddr($ip));
        		$message  = $date . "\n\n";
        		$message .= 'URL Request: '  . $request . "\n";
        		$message .= 'IP Address: '   . $ip . "\n";
        		$message .= 'Host Name: '    . $host . "\n";
        		$message .= 'User Agent: '   . $ua . "\n\n";
        		$message .= 'GeoIP Lookup: ' . "\n\n" . 'https://whatismyipaddress.com/ip/'. $ip . "\n\n";
        		$message .= 'Whois Lookup: ' . "\n\n" . $whois . "\n\n";
                self::mail()->sendMail(
                    self::getSetting('contact_email'),
                    self::getSetting('contact_email'),
                    'BotTrap',
                    $message,
                    'BlackCat CMS BotTrap',
                );
                if(self::asJSON()) {
                    \CAT\Helper\Json::printError('You have been banned!');
                } else {
                    self::tpl()->setPath(__DIR__.'/../templates/default');
            		self::tpl()->output(
                        'trap1',
                        array('ip'=>$ip,'host'=>$host,'whois'=>$whois,'date'=>$date)
                    );
                }
                exit;
        	}
        }

/*

	Title:        Blackhole for Bad Bots
	Description:  Automatically trap and block bots that don't obey robots.txt rules
	Project URL:  https://perishablepress.com/blackhole-bad-bots/
	Author:       Jeff Starr ( @perishable )
	Version:      4.3 / 20190824
	License:      GPLv2 or later
	License URI:  https://www.gnu.org/licenses/gpl-2.0.txt

	For complete documentation, visit https://perishablepress.com/blackhole-bad-bots/

	Using WordPress? Check out the plugin versions of Blackhole:

	Free version: https://wordpress.org/plugins/blackhole-bad-bots/
	Pro version:  https://plugin-planet.com/blackhole-pro/

    NOTE: original functions adapted and modified for use with BlackCat CMS

*/

        private static function blackhole_checkbot(string $ip,string $ua,string $request,string $date) : int
        {
        	$badbot = 0;
        	if (self::blackhole_whitelist($ip,$ua)) return -1;

            $result = self::db()->query(
                'SELECT `ip`, `visits` FROM `:prefix:mod_bottrap_log` WHERE `ip`=?',
                array($ip)
            );
            $data = $result->fetch();
            if(!empty($data)) {
                $v = intval($data['visits']) + 1;
                self::db()->query(
                    'UPDATE `:prefix:mod_bottrap_log` SET `date`=?, `visits`=? WHERE `ip`=?',
                    array($date,$v,$ip)
                );
                return intval($data['visits']);
            }
            return 0;
        }

        private static function blackhole_get_vars() {
        	$ip       = \CAT\Base::getVisitorIP();

        	$ua       = isset($_SERVER['HTTP_USER_AGENT']) ? self::blackhole_sanitize($_SERVER['HTTP_USER_AGENT']) : null;
        	$request  = isset($_SERVER['REQUEST_URI'])     ? self::blackhole_sanitize($_SERVER['REQUEST_URI'])     : null;
        	$protocol = isset($_SERVER['SERVER_PROTOCOL']) ? self::blackhole_sanitize($_SERVER['SERVER_PROTOCOL']) : null;
        	$method   = isset($_SERVER['REQUEST_METHOD'])  ? self::blackhole_sanitize($_SERVER['REQUEST_METHOD'])  : null;

        	date_default_timezone_set('UTC');

        	$date = date('l, F jS Y @ H:i:s');

        	$time = time();

        	return array($ip, $ua, $request, $protocol, $method, $date, $time);
        }

        private static function blackhole_whitelist(string $ip, string $ua) : bool
        {
            $whitelist_ips = self::getWhitelistedIPs();
            if(in_array($ip, $whitelist_ips)) {
                return true;
            }
            list($whitelist_ua,$blacklist_ua) = self::getKnownBots();
        	if (preg_match("/(".implode('|',array_values($whitelist_ua)).")/i", $ua, $m)) {
        		return true;
        	}
        	return false;
        }

        private static function blackhole_sanitize($string) {
        	$string = trim($string);
        	$string = strip_tags($string);
        	$string = htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
        	$string = str_replace("\n", "", $string);
        	$string = trim($string);
        	return $string;
        }


        private static function blackhole_whois() {
        	$msg = '';
        	$extra = '';
        	$buffer = '';
        	$server = 'whois.arin.net';

        	$ip = \CAT\Base::getVisitorIP();

        	if (!$ip = gethostbyname($ip)) {

        		$msg .= 'Can&rsquo;t perform lookup without an IP address.'. "\n\n";

        	} else {

        		if (!$sock = fsockopen($server, 43, $num, $error, 20)) {

        			unset($sock);
        			$msg .= 'Timed-out connecting to $server (port 43).'. "\n\n";

        		} else {

        			// fputs($sock, "$ip\n");
        			fputs($sock, "n $ip\n");
        			$buffer = '';
        			while (!feof($sock)) $buffer .= fgets($sock, 10240);
        			fclose($sock);

        		}

        		if (stripos($buffer, 'ripe.net')) {

        			$nextServer = 'whois.ripe.net';

        		} elseif (stripos($buffer, 'nic.ad.jp')) {

        			$nextServer = 'whois.nic.ad.jp';
        			$extra = '/e'; // suppress JaPaNIC characters

        		} elseif (stripos($buffer, 'registro.br')) {

        			$nextServer = 'whois.registro.br';

        		}

        		if (isset($nextServer)) {

        			$buffer = '';
        			$msg .= 'Deferred to specific whois server: '. $nextServer .'...'. "\n\n";

        			if (!$sock = fsockopen($nextServer, 43, $num, $error, 10)) {

        				unset($sock);
        				$msg .= 'Timed-out connecting to '. $nextServer .' (port 43)'. "\n\n";

        			} else {

        				fputs($sock, $ip . $extra . "\n");
        				while (!feof($sock)) $buffer .= fgets($sock, 10240);
        				fclose($sock);

        			}
        		}

        		$replacements = array("\n", "\n\n", "");
        		$patterns = array("/\\n\\n\\n\\n/i", "/\\n\\n\\n/i", "/#(\s)?/i");
        		$buffer = preg_replace($patterns, $replacements, $buffer);
        		$buffer = htmlentities(trim($buffer), ENT_QUOTES, 'UTF-8');

        		// $msg .= nl2br($buffer);
        		$msg .= $buffer;

        	}

        	return $msg;

        }
    }
}