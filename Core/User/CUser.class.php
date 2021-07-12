<?php
/**
 * 
 * DressAPI
 * @version 1.0
 * @license This file is under Apache 2.0 license
 * @author Tufano Pasquale
 * @url https://dressapi.com
 * 
 * 
 * User authentication and authorization
 *  
 */

namespace DressApi\Core\User;

use Exception;
use Firebase\JWT\JWT;
use DressApi\Core\DBMS\CMySqlDB as CDB;
use DressApi\Core\Request\CRequest;
use DressApi\Core\Cache\CFileCache as CCache; // An alternative is CRedisCache


class CUser extends CDB
{
    private ?int $id = 0;
    private string $token = '';

    // List of user permissions  [usertype]
    private array $role_permissions = []; // [string|int ROLE][string PERMISSION];

    // Current User Roles
    private array $user_roles = []; 


    /**
     * Constructor
     * 
     */
    public function __construct() 
    {
        $this->id = 0;
    }


    /**
     * Check if the username/password pair identifies a valid user
     * 
     * @param string $username username of a user
     * @param string $password password of a user
     * 
     * @return ?int a user id if exists, otherwise is 0
     */
    public function checkValidUser(string $username, string $password) : ?int
    {
        $ret = 0;

        $db_composer = 'DressApi\\Core\DBMS\\C'.DBMS_TYPE.'Composer';
        $sc = new $db_composer();

        $sql = $sc->select(USER_ITEM_ID)->from(USER_TABLE)->
                    where(USER_ITEM_USERNAME."='$username' AND ".
                          USER_ITEM_PASSWORD."='". hash(PASSWORD_ENC_ALGORITHM, $password)."' AND ".
                          "status='Verified'");

        // echo "\n$sql\n";
        return $this->getQueryDataValue($sql);
    }


    /**
     * Check if the username/password pair identifies a valid user
     * 
     * @param string $username username of a user
     * @param string $password password of a user
     * 
     * @return int a user id if exists, otherwise is 0
     */
    public function authenticate(string $username, string $password)
    {
        $token = 'Invalid login';
        // Validate the credentials against a database, or other data store.
        // ...
        // For the purposes of this example, we'll assume that they're valid
        $this->id = $this->checkValidUser($username, $password);

        if ($this->id<1) 
            throw new Exception('Invalid login');
        else
        {
            $tokenId    = base64_encode(random_bytes(16));
            $issuedAt   = new \DateTimeImmutable();
            $expire     = $issuedAt->modify('+'.TOKEN_DURATION)->getTimestamp();      // Add TOKEN_DURATION time

            // Create the token as an array
            $data = [
                'iat'  => $issuedAt->getTimestamp(),    // Issued at: time when the token was generated
                'jti'  => $tokenId,                     // Json Token Id: an unique identifier for the token
                'iss'  => DOMAIN_NAME,                  // Issuer
                'nbf'  => $issuedAt->getTimestamp(),    // Start validate (Not before)
                'exp'  => $expire,                      // Expire
                'data' => [                             // Data related to the signer user
                    'username' => $username,            // User name
                    'id'  => $this->id
                ]
            ];

            // Encode the array to a JWT string.
            $this->token = JWT::encode(
                $data,                  // Data to be encoded in the JWT
                SECRET_KEY,             // The signing key
                TOKEN_ENC_ALGORITHM     // Algorithm used to sign the token, see https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40#section-3
            );

        }
        return $this->token;
    }

    
    public function checkToken( string $token )
    {

        $matches = [];
        $ret = true;

        if ($token===null || $token=='')
            $ret = false;
        else // Attempt to extract the token from the Bearer header
        if (!preg_match('/Bearer\s(\S+)/', $token, $matches)) 
        {
            $ret = false;
            // header('HTTP/1.0 400 Bad Request');
            throw new Exception('Token not found in request');    
        }
        else
        {
            $jwt = $matches[1];
            if (!$jwt) 
            {
                $ret = false;
                // No token was able to be extracted from the authorization header
                // header('HTTP/1.0 400 Bad Request');
                throw new Exception('Invalid Token');    
            }    
        }

        if ( $ret == true )
        {
            JWT::$leeway += 60;
            $token = JWT::decode((string)$jwt, SECRET_KEY, [TOKEN_ENC_ALGORITHM]);
            $now = new \DateTimeImmutable();
    
            $this->id = $token->data->id; 
            if ($token->iss !== DOMAIN_NAME ||
                $token->nbf > $now->getTimestamp() ||
                $token->exp < $now->getTimestamp())
            {
                // header('HTTP/1.1 401 Unauthorized');
                throw new Exception('Unauthorized');    
                $ret = false;
            }    
        }

        return $ret;
    }

    public function getId()
    {
        return $this->id;
    }


    public function verify()
    {
        // Parameters
        $sets = file_get_contents('php://input');
        $params = [];            
        if ($sets)
            parse_str($sets, $params);    

        // Method
        $method = ((isset($_SERVER['REQUEST_METHOD']))?($_SERVER['REQUEST_METHOD']):('GET'));

        // Verifica del login
        if ($method=='POST' && isset($params['username']) && isset($params['password']))
            return $this->authenticate($params['username'], $params['password']);
        else
        {
            $token = '';
            if (isset($_SERVER['HTTP_AUTHORIZATION']))
                $token = $_SERVER['HTTP_AUTHORIZATION'];

            if ($token!='' && !$this->checkToken($token))
                throw new Exception('Invalid login');

            return 'OK';
        }
    }


    public function getToken()
    {
        return $this->token;
    }


    /**
     * Set a User Role
     * 
     * @param array $user_roles role of user where role can be "Admin", "Publisher" and anything else
     *                               or a number 1=Admin, 2=Publisher, 3=Normal User
     *                               the role name and type depends from your app
     */
    public function setUserRole( array $user_roles )
    {
        $this->user_roles = $user_roles;
    }


    /**
     * Reset a User Role
     * 
     */
    public function resetUserRole( )
    {
        $this->user_roles = [];
    }


    /**
     * Add a Role to the user
     * 
     * @param string|int $role role of user where role can be "Admin", "Publisher" and anything else or a number 1=Admin, 2=Publisher, 3=Normal User
     *                         the role name and type depends from your app
     */
    public function addUserRole( string|int $role )
    {
        $this->user_roles = [$role];
    }


    /**
     * Set a User Role (Normally detected by DB)
     * 
     * @param string|int $role role of user where role can be "Admin", "Publisher" and anything else or a number 1=Admin, 2=Publisher, 3=Normal User
     *                         the role name and type depends from your app
     * @param string $module normally is the name of DB table but could be not in an custom extension 
     * @param array $permissions Name of permissions [GET, POST, PATCh, PUT, DELETE, OPTIONS] 
     *               or if you prefer you can use C.R.U.D.O. letters [C=POST (Insert), R=GET (Read), U=PATCH|PUT (Modify), D=DELETE, O=OPTIONS].
     *               Finally, you can use * for all valid permission
     */
    public function setRolePermissions( string|int $role, string $module, array $permissions )
    {
        $this->role_permissions[$role] = [];
        array_map(function($key,&$value) use($role, $module) 
                  { 
                        $this->addRolePermission( $role, $module, $value ); 
                  }, $permissions);
    }


    /**
     * Set a User Role (Normally detected by DB)
     * 
     * @param string|int $role role of user where role can be "Admin", "Publisher" and anything else or a number 1=Admin, 2=Publisher, 3=Normal User
     *                         the role name and type depends from your app
     * @param string $module normally is the name of DB table but could be not in an custom extension 
     * @param string Name of permission [GET, HEAD, POST, PATCH, PUT, DELETE, OPTIONS] 
     *               or if you prefer you can use C.R.U.D.O. letters [C=POST, R=GET, U=PATCH|PUT, D=DELETE, O=OPTIONS].
     *               Finally, you can use * for all valid permission (for example with Admin user)
     */
    public function addRolePermission( string|int $role, string $module, string $permission )
    {
        $module = strtolower($module);

        if (!isset($this->role_permissions[$role]))
            $this->role_permissions[$role] = [];

        if (!isset($this->role_permissions[$role][$module]))
            $this->role_permissions[$role][$module] = [];
            
        if (strlen($permission)==1)
            switch($permission)
            {
                case 'C':
                    $this->role_permissions[$role][$module][] = 'POST';
                    $this->role_permissions[$role][$module][] = 'Upload'; // Is a special POST method
                    $this->role_permissions[$role][$module][] = 'UPLOAD'; // Is a special POST method
                    break;

                case 'R':
                    $this->role_permissions[$role][$module][] = 'GET';
                    $this->role_permissions[$role][$module][] = 'HEAD';
                    break;
                        
                case 'U':
                    $this->role_permissions[$role][$module][] = 'PATCH';
                    $this->role_permissions[$role][$module][] = 'PUT';
                    break;
                        
                case 'D':
                    $this->role_permissions[$role][$module][] = 'DELETE';
                    break;
                        
                case 'O':
                    $this->role_permissions[$role][$module][] = 'OPTIONS';
                    break;

                case '*':
                    $this->role_permissions[$role][$module][] = 'POST';
                    $this->role_permissions[$role][$module][] = 'Upload';
                    $this->role_permissions[$role][$module][] = 'UPLOAD'; // Is a special POST method
                    $this->role_permissions[$role][$module][] = 'GET';
                    $this->role_permissions[$role][$module][] = 'HEAD';
                    $this->role_permissions[$role][$module][] = 'PATCH';
                    $this->role_permissions[$role][$module][] = 'PUT';
                    $this->role_permissions[$role][$module][] = 'DELETE';
                    $this->role_permissions[$role][$module][] = 'OPTIONS';
                    break;
                    
                default:
                    $this->role_permissions[$role][$module][] = strtoupper($permission);
                    break;
            }

    }


    /**
     * Set a User Role (Normally detected by DB)
     * 
     * @param string $module normally is the name of DB table but could be not in an custom extension 
     * @param string Name of permission [Get, Post, Patch, Put, Delete, Options] 
     *               or if you prefer you can use C.R.U.D.O. letters [C=Post, R=Get, U=Patch|Put, D=Delete, O=Options].
     *               Finally, you can use * for all valid permission (for example with Admin user)
     */
    public function checkPermission(string $module, string $permission)
    {
        foreach($this->user_roles as $user_role)
            if (isset($this->role_permissions[$user_role]))
            {
                if (isset($this->role_permissions[$user_role][$module]) &&
                    in_array($permission,$this->role_permissions[$user_role][$module]))
                    return true;
                else // check if it can access all modules
                if (isset($this->role_permissions[$user_role]['*']) &&
                    in_array($permission,$this->role_permissions[$user_role]['*']))
                    return true;
            }

        return false;
    }


    /**
     * This method import automatically all permssions of the current user
     * if exists an table named module_role_permission 
     * and the relative tables: role and user_role
     *
     * For convenience "controller" and "permission" are not indexes of external tables but if you want,
     * you can create and manage them in a derived CUser class 
     * 
     * For example:
     * 
     * The Roles must be in a specific table name "user"
     * 
     *  id    name
     *  ===================
     *   1    Administrator
     *   2    Anonymous   // All permissions for anonymous is valid for all user
     *   3    Pasquale Tufano
     *   4    Michael Franks
     *   5    Joe Sample
     *
     *
     * The Roles must be in a specific table name "role"
     * 
     *  id    name
     *  ===================
     *   1    Administrator   // Can make all
     *   2    Anonymous       // Can read - All permissions for anonymous is valid for all user
     *   3    Editor          // Full power: can Delete or publish a post
     *   4    Writer          // Can write a post
     *   5    Commentator     // Can write a comment
     *
     * 
     * 
     *  
     *  id    module   role  permission
     *  =================================
     *   1    *         1        *
     *   2    page      3        C 
     *   3    page      2        R 
     *   4    page      3        U 
     *   5    page      4        D 
     *   6    page      3        O
     * 
     * 
     * For identificate the roles of current user is necessary use a cross table like this
     *
     *  id    role           user
     *  =========================
     *   1    1                 1
     *   2    2                 2
     *   3    3               101
     *    
     */
    public function importPermissionsByDB(CRequest $request, CCache $cache)
    {
        $moduletable = $request->getModule(); 
        $sql = "SELECT id_role,permission FROM moduletable_role_permission ". 
               "WHERE (id_role IN (SELECT id_role FROM user_role WHERE id_user=$this->id) OR id_role IS NULL) ".
               "AND (id_moduletable IN (SELECT id FROM moduletable WHERE name='$moduletable') OR id_moduletable IS NULL) ";

        $hash = hash(PASSWORD_ENC_ALGORITHM, $sql);
        $data = null;
        if ($cache) 
            $data = $cache->get($hash);

        if ($data === null )
        {
            $data = [];
            $this->getQueryDataTable($data, $sql);     
            $cache->set($hash, $data);           
        }

        foreach($data as $row)
        {
            $role = $row['id_role'] ?? '*';
            $this->addUserRole( $role );
            $this->addRolePermission( $role, $moduletable, $row['permission'] ?? '*' );
        }
    }


    public function subscribe()
    {
        throw new Exception("subscribe todo");
    }


    public function logout()
    {
        // normally the client loose his token for logout (like deleting cookies)
        // but if logout is necessary we can make a black list on file
        // not through the db because it would always require a connection and slow down the service 
        throw new Exception("logout todo");
    }
}
