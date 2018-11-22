<?php
/**
 * SQL database access through ffDB_sql
 * 
 * @package FormsFramework
 * @subpackage OAUTH2
 * @author Samuele Diella <samuele.diella@gmail.com>
 * @copyright Copyright &copy; 2015-2017, Samuele Diella
 * @license https://opensource.org/licenses/LGPL-3.0
 * @link http://www.formsphpframework.com
 */

namespace OAuth2\Storage;

class FF implements
    AccessTokenInterface,
    ClientCredentialsInterface
{
	/**
	 *
	 * @var ffDB_sql
	 */
	protected $db = null;
	protected $config = null;
	
    public function __construct(\ffDB_Sql $connection = null, $config = array())
    {
		if ($connection === null)
			$this->db = \ffDB_Sql::factory();
		else
			$this->db = $connection;
	
        $this->config = array_merge(array(
            'client_table' => 'oauth_clients',
            'access_token_table' => 'oauth_access_tokens',
            'refresh_token_table' => 'oauth_refresh_tokens',
            'code_table' => 'oauth_authorization_codes',
            'user_table' => 'oauth_users',
            'jwt_table'  => 'oauth_jwt',
            'jti_table'  => 'oauth_jti',
            'scope_table'  => 'oauth_scopes',
            'public_key_table'  => 'oauth_public_keys',
        ), $config);
	}
	
    public function getClientDetails($client_id)
	{
		$this->db->query("SELECT * FROM `" . $this->config['client_table'] . "` WHERE `client_id` = " . $this->db->toSql($client_id));
		if ($this->db->nextRecord())
			return $this->db->record;
		else
			return false;
	}

    /**
     * Get the scope associated with this client
     *
     * @return
     * STRING the space-delineated scope list for the specified client_id
     */
    public function getClientScope($client_id)
	{
        if (!$clientDetails = $this->getClientDetails($client_id))
		{
            return false;
        }

        if (isset($clientDetails['scope']))
		{
            return $clientDetails['scope'];
        }

        return null;
	}

    /**
     * Check restricted grant types of corresponding client identifier.
     *
     * If you want to restrict clients to certain grant types, override this
     * function.
     *
     * @param $client_id
     * Client identifier to be check with.
     * @param $grant_type
     * Grant type to be check with
     *
     * @return
     * TRUE if the grant type is supported by this client identifier, and
     * FALSE if it isn't.
     *
     * @ingroup oauth2_section_4
     */
    public function checkRestrictedGrantType($client_id, $grant_type)
	{
        $details = $this->getClientDetails($client_id);
        if (isset($details['grant_types']))
		{
            $grant_types = explode(' ', $details['grant_types']);

            return in_array($grant_type, (array) $grant_types);
        }

        // if grant_types are not defined, then none are restricted
        return true;
	}
	
    /**
     * Look up the supplied oauth_token from storage.
     *
     * We need to retrieve access token data as we create and verify tokens.
     *
     * @param $oauth_token
     * oauth_token to be check with.
     *
     * @return
     * An associative array as below, and return NULL if the supplied oauth_token
     * is invalid:
     * - expires: Stored expiration in unix timestamp.
     * - client_id: (optional) Stored client identifier.
     * - user_id: (optional) Stored user identifier.
     * - scope: (optional) Stored scope values in space-separated string.
     * - id_token: (optional) Stored id_token (if "use_openid_connect" is true).
     *
     * @ingroup oauth2_section_7
     */
    public function getAccessToken($oauth_token)
	{
		$this->db->query("SELECT * FROM `" . $this->config['access_token_table'] . "` WHERE `access_token` = " . $this->db->toSql($oauth_token));
		if ($this->db->nextRecord())
		{
			$record = $this->db->record;
			$record['expires'] = strtotime($record['expires']);
			return $record;
		}
		else
			return null;
	}

    /**
     * Store the supplied access token values to storage.
     *
     * We need to store access token data as we create and verify tokens.
     *
     * @param $oauth_token    oauth_token to be stored.
     * @param $client_id      client identifier to be stored.
     * @param $user_id        user identifier to be stored.
     * @param int    $expires expiration to be stored as a Unix timestamp.
     * @param string $scope   OPTIONAL Scopes to be stored in space-separated string.
     *
     * @ingroup oauth2_section_4
     */
    public function setAccessToken($oauth_token, $client_id, $user_id, $expires, $scope = null)
	{
        // convert expires to datestring
        $expires = date('Y-m-d H:i:s', $expires);

        // if it exists, update it.
        if ($this->getAccessToken($oauth_token)) 
		{
			$sSQL = "UPDATE `" . $this->config['access_token_table'] . "`
						SET
							`client_id` = " . $this->db->toSql($client_id) . "
							, `expires` = " . $this->db->toSql(new \ffData($expires, "DateTime", "ISO9075")) . "
							, `user_id` = " . $this->db->toSql($user_id) . "
							, `scope` = " . $this->db->toSql($scope) . "
						WHERE
							`access_token` = " . $this->db->toSql($oauth_token) . "
				";
        } 
		else 
		{
			$sSQL = "INSERT INTO `" . $this->config['access_token_table'] . "`
							(
								access_token
								, client_id
								, expires
								, user_id
								, scope
							) VALUES (
								" . $this->db->toSql($oauth_token) . "
								, " . $this->db->toSql($client_id) . "
								, " . $this->db->toSql(new \ffData($expires, "DateTime", "ISO9075")) . "
								, " . $this->db->toSql($user_id) . "
								, " . $this->db->toSql($scope) . "
							)
				";
        }
		
		//$this->db->execute($sSQL);
		//\ffErrorHandler::raise("TEST", E_USER_ERROR, $this, get_defined_vars());
		return $this->db->execute($sSQL);
	}
	
    /**
     * Make sure that the client credentials is valid.
     *
     * @param $client_id
     * Client identifier to be check with.
     * @param $client_secret
     * (optional) If a secret is required, check that they've given the right one.
     *
     * @return
     * TRUE if the client credentials are valid, and MUST return FALSE if it isn't.
     * @endcode
     *
     * @see http://tools.ietf.org/html/rfc6749#section-3.1
     *
     * @ingroup oauth2_section_3
     */
    public function checkClientCredentials($client_id, $client_secret = null)
	{
		$this->db->query("SELECT * FROM `" . $this->config['client_table'] . "` WHERE `client_id` = " . $this->db->toSql($client_id));
		if ($this->db->nextRecord())
		{
			return $this->db->getField("client_secret", "Text", true) == $client_secret;
		}
		else
			return false;
	}

    /**
     * Determine if the client is a "public" client, and therefore
     * does not require passing credentials for certain grant types
     *
     * @param $client_id
     * Client identifier to be check with.
     *
     * @return
     * TRUE if the client is public, and FALSE if it isn't.
     * @endcode
     *
     * @see http://tools.ietf.org/html/rfc6749#section-2.3
     * @see https://github.com/bshaffer/oauth2-server-php/issues/257
     *
     * @ingroup oauth2_section_2
     */
    public function isPublicClient($client_id)
	{
		$this->db->query("SELECT * FROM `" . $this->config['client_table'] . "` WHERE `client_id` = " . $this->db->toSql($client_id));
		if ($this->db->nextRecord())
		{
			return strlen($this->db->getField("client_secret", "Text", true));
		}
		else
			return false;
	}
}
