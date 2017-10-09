<?php
/**
 * shared memory cache
 * 
 * @package FormsFramework
 * @subpackage base
 * @author Samuele Diella <samuele.diella@gmail.com>
 * @copyright Copyright (c) 2004-2017, Samuele Diella
 * @license https://opensource.org/licenses/LGPL-3.0
 * @link http://www.formsphpframework.com
 */

if (!defined("FF_CACHE_MEMCACHED_SERVER")) define("FF_CACHE_MEMCACHED_SERVER", "localhost");
if (!defined("FF_CACHE_MEMCACHED_PORT")) define("FF_CACHE_MEMCACHED_PORT", 11211);

/**
 * shared memory cache
 *
 * @package FormsFramework
 * @subpackage base
 * @author Samuele Diella <samuele.diella@gmail.com>
 * @copyright Copyright (c) 2004-2017, Samuele Diella
 * @license https://opensource.org/licenses/LGPL-3.0
 * @link http://www.formsphpframework.com
 */
class ffCache_memcached extends ffCacheAdapter
{
	/**
	 * @var Memcached
	 */
	private $conn	= null;
	private $bNoTblRel = false;
	
	function __construct($bNoTblRel)
	{
		$this->bNoTblRel = $bNoTblRel;
		$this->conn = new Memcached(APPID);
		$this->conn->addServer(FF_CACHE_MEMCACHED_SERVER, FF_CACHE_MEMCACHED_PORT);
		
		if ($this->bNoTblRel)
			return;
		
		$ret = $this->conn->get("__relation_table__");
		
		if ($this->conn->getResultCode() === Memcached::RES_NOTFOUND)
		{
			$this->relation_table = array(APPID => array());
		}
		else
		{
			$this->relation_table = unserialize($ret);
		}
	}
	
	function addServer($server, $port, $weight = 0)
	{
		$this->conn->addServer($server, $port);
	}
	
	/**
	 * Inserisce un elemento nella cache
	 * Oltre ai parametri indicati, accetta un numero indefinito di chiavi per relazione i valori memorizzati
	 * @param String $name il nome dell'elemento
	 * @param int $ttl il numero di secondi in cui la variabile dev'essere memorizzata, se null|0 rimane fino al clear
	 * @param Mixed $value l'elemento
	 * @return bool if storing both value and rel table will success
	 */
	function set($name, $ttl, $value)
	{
		if ($ttl === null)
			$ttl = 0;
		
		if ($value === null)
		{
			$this->conn->delete($name);
			$res = true; // delete anyway from the rel table
		}
		else
			$res = $this->conn->set($name, $value, $ttl);
		
		if (!$this->bNoTblRel && $res)
		{
			if (func_num_args() > 3)
			{
				$args = func_get_args();
				for ($i = 3; $i < count($args); $i++)
				{
					if ($value === null)
						unset($this->relation_table[$args[$i]][APPID . $name]);
					else
						$this->relation_table[$args[$i]][APPID . $name] = true;
				}
			}

			if ($value === null)
				unset($this->relation_table[APPID][$name]);
			else
				$this->relation_table[APPID][$name] = true;

			$res = $this->conn->set("__relation_table__", serialize($this->relation_table), 0);
		}
		
		return $res;
	}

	/**
	 * Recupera un elemento dalla cache
	 * @param <type> $name il nome dell'elemento
	 * @param <type> $success un puntatore ad una variabile che indica il successo dell'operazione
	 * @return Mixed l'elemento 
	 */
	function get($name, &$success)
	{
		$ret = $this->conn->get($name);
		/*if ($ret === false)
			$success = false;
		else
			$success = true;
		*/
		if ($this->conn->getResultCode() === Memcached::RES_SUCCESS)
			$success = true;
		else
			$success = false;
		
		return $ret;
	}

	/**
	 * Pulisce la cache
	 * Accetta un numero indefinito di parametri che possono essere utilizzati per cancellare i dati basandosi sulle relazioni
	 * Se non si specificano le relazioni, verrà cancellata l'intera cache
	 */
	function clear()
	{
		// global reset
		if ($this->bNoTblRel || !func_num_args())
		{
			$this->conn->flush();
			$this->relation_table = array(APPID => array());
			return;
		}
		
		// by assoc
		$args = func_get_args();
		foreach ($args as $key => $value)
		{
			if (count($this->relation_table[$value]))
			{
				foreach ($this->relation_table[$value] as $subkey => $subvalue)
				{
					$this->conn->delete($subkey);
				}
			}
			unset($this->relation_table[$value]);
		}
		$this->conn->set("__relation_table__", serialize($this->relation_table));
	}
	
	function stats()
	{
		echo "<pre>";
		print_r($this->conn->getStats());
		exit;		
	}
	
	function getConn()
	{
		return $this->conn;
	}
}
