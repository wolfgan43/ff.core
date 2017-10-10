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
class ffCache_apc extends ffCacheAdapter
{
	private $bNoTblRel = false;
	
	function __construct($bNoTblRel)
	{
		$this->bNoTblRel = $bNoTblRel;
		
		if ($this->bNoTblRel)
			return;
		
		$res = apc_fetch(APPID . "__relation_table__", $success);
		if ($success)
		{
			//ffErrorHandler::raise("asd", E_USER_ERROR, $this, get_defined_vars());
			$this->relation_table = unserialize($res);
		}
		else
			$this->relation_table[APPID] = array();
	}
	
	/**
	 * Inserisce un elemento nella cache
	 * Oltre ai parametri indicati, accetta un numero indefinito di chiavi per relazione i valori memorizzati
	 * @param String $name il nome dell'elemento
	 * @param int $ttl il numero di secondi in cui la variabile dev'essere memorizzata, se null rimane fino al clear
	 * @param Mixed $value l'elemento
	 * @return bool if storing both value and rel table will success
	 */
	function set($name, $ttl, $value)
	{
		if ($value === null)
		{
			@apc_delete(APPID . $name);
			$res = true;
		}
		else
			$res = @apc_store(APPID . $name, $value, $ttl);
		
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

			$res = @apc_store(APPID . "__relation_table__", serialize($this->relation_table), null);
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
		return apc_fetch(APPID . $name, $success);
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
			apc_clear_cache("user");
			foreach ($this->relation_table[APPID] as $key => $value)
			{
				@apc_delete(APPID . $value);
			}
			$this->relation_table = array(APPID => array());
			apc_store(APPID . "__relation_table__", serialize($this->relation_table));
			return;
		}
		
		$args = func_get_args();
		foreach ($args as $key => $value)
		{
			if (count($this->relation_table[$value]))
			{
				foreach ($this->relation_table[$value] as $subkey => $subvalue)
				{
					@apc_delete($subkey);
				}
			}
			unset($this->relation_table[$value]);
		}
		apc_store(APPID . "__relation_table__", serialize( $this->relation_table));
	}
}
