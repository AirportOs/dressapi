<?php
/**
 * 
 * DressAPI
 * @version 1.1
 * @license This file is under Apache 2.0 license
 * @author Tufano Pasquale
 * @copyright Tufano Pasquale
 * @url https://dressapi.com
 * 
 * 
 * User authentication and authorization
 *  
 */

namespace DressApi\core\user;

use Exception;
use DressApi\core\dbms\CSqlComposerBase;

use Firebase\JWT\JWT;
use DressApi\core\dbms\CMySqlDB as CDB;
use DressApi\core\dbms\CMySqlComposer as CSqlComposer;
use DressApi\core\request\CRequest;
use DressApi\core\response\CResponse;
use DressApi\core\cache\CFileCache as CCache; // An alternative is CRedisCache


use DressApi\core\Mail\CMail;

//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
//use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\SMTP;
// use PHPMailer\PHPMailer\Exception;

class CUser extends CDB
{
    private const ROLE_ID_ADMIN = 1;
    private const ROLE_ID_ANONYMOUS = 2;
    private const USER_ID_ANONYMOUS = 2;
    
    private ?int $id = 0;
    private string $name = 'Anonymous';
    private string $username = 'nobody';
    private string $token = '';

    protected CRequest $request;
    protected CCache $cache;

    // List of user permissions  [usertype]
    private array $role_permissions = []; // [string|int ROLE][string PERMISSION];

    // Current User Roles
    private array $user_roles = []; 


    /**
     * Constructor
     */
    public function __construct(CRequest $request, CCache $cache) 
    {
        $this->id = 0;
        $this->username = 'nobody';
        $this->request = $request;
        $this->cache = $cache;
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
        $sc = new CSqlComposer();

        $sql = (string)$sc->select(USER_ITEM_ID.','.USER_ITEM_NAME)->from(USER_TABLE)->
                    where(USER_ITEM_USERNAME."='$username' AND (".USER_ITEM_PASSWORD."='' OR ".
                          USER_ITEM_PASSWORD."='". hash(PASSWORD_ENC_ALGORITHM, $password)."') AND ".
                          "status='Verified'");

        // echo "\n$sql\n";
        $v = $this->getQueryFetchRow($sql);
        if ($v) 
            [$this->id,$this->name] = $v;
        else
        {
            $this->id = 0;
            $this->name = '';    
        }
        return $this->id;
    }


    /**
     * Check if the username/password pair identifies a valid user
     * 
     * @param string $username username of a user
     * @param string $password password of a user
     * 
     * @return string a user token if the user exists, otherwise is a string 'Invalid login'
     */
    public function authenticate(array $params) : string
    {
        $this->token = '';
        $username = (isset($params['dusername']))?$params['dusername']:''; 
        $password = (isset($params['dpassword']))?$params['dpassword']:'';
        if ($username=='' && $password=='')
        {
            if (CRequest::getFormat()=='html' && $this->token)
            {
                $decoded = JWT::decode($this->token,
                                       SECRET_KEY,             // The signing key
                                       [TOKEN_ENC_ALGORITHM]);   // Algorithm used to sign the token, see https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40#section-3
                $this->id = $decoded['elements']['id'];
                $this->name = $decoded['elements']['name'];
            }
            else
                $this->id = self::USER_ID_ANONYMOUS;
        }
        else
        {
            // Validate the credentials against a database, or other data store.
            // ...
            // For the purposes of this example, we'll assume that they're valid
            $this->id = $this->checkValidUser($username, $password);
            if ($this->id<1) 
            {
                throw new Exception('Invalid login',401);
            }
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
                    'elements' => [                             // Data related to the signer user
                        'username' => $username,            // User name
                        'name' => $this->name,
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
        } 

        if ($this->token) // CRequest::getFormat()=='html' && 
            $_SESSION[DB_NAME]['token'] = 'Bearer '.$this->token;
        return $this->token;
    }

    
    /**
     * @param string $token
     * 
     * @return bool
     */
    public function checkToken( string $token ) : bool
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
            throw new Exception('Token not found in request',CResponse::HTTP_STATUS_UNAUTHORIZED);    
        }
        else
        {
            $jwt = $matches[1];
            if (!$jwt) 
            {
                $ret = false;
                // No token was able to be extracted from the authorization header
                throw new Exception('Invalid Token',CResponse::HTTP_STATUS_FORBIDDEN);    
            }    
        }

        if ( $ret == true )
        {
            JWT::$leeway += 60;
            $atoken = JWT::decode((string)$jwt, SECRET_KEY, [TOKEN_ENC_ALGORITHM]);
            $now = new \DateTimeImmutable();
    
            $this->id = $atoken->elements->id; 
            $timestamp = $now->getTimestamp();
            $this->username = $atoken->elements->username; 
            if ($atoken->iss !== DOMAIN_NAME ||
                $atoken->nbf > $timestamp ||
                $atoken->exp < $timestamp)
            {
                // header('HTTP/1.1 401 Unauthorized');
                throw new Exception('Unauthorized',401);    
                $ret = false;
            }    
        }

        return $ret;
    }


    /**
     * @return ?int get the id of current user
     */
    public function getId() : ?int
    {
        return $this->id;
    }


    /**
     * @return string get the username of current user
     */
    public function getUsername() : string
    {
        return $this->username;
    }


    /**
     * @return string get the name of current user
     */
    public function getName() : string
    {
        return $this->name;
    }


    /**
     * @return string true if the current user is Admin
     * Note: Using this method is not recommended as it may cause inconsistencies
     *       with permissions. If possible use checkPermission()
     */
    public function isAdmin() : bool
    {
        return (isset($this->role_permissions[self::ROLE_ID_ADMIN]));
    }


    /**
     * @return string true if the current user is Anonymous
     */
    public function isAnonymous() : bool
    {
        return ($this->id==self::USER_ID_ANONYMOUS);
    }


    /**
     * @return string true if the current user is NOT Anonymous
     */
    public function isAuthenticated() : bool
    {
        return ($this->id!=self::USER_ID_ANONYMOUS);
    }


    /**
     * @return string OK if is done
     */
    public function run() : string
    {
        // Parameters
        $params = $this->request->getParameters();

        // Method
        $method = $this->request->getMethod();

        // Verifica del login
        if ($method=='POST' && isset($params['do']) && $params['do']=='subscribe')
            return $this->subscribe($params);
        else
        if ($method=='POST' && isset($params['do']) && $params['do']=='login')
            return $this->authenticate($params);
        else
        {
            if (USER_TABLE!='')
            {
                $token = $this->request->getHttpAuthorization();
                if (!$token)
                    return $this->authenticate($params);
                if (!$this->checkToken($token))
                {
                    sleep(2); // 2 seconds delay
                    throw new Exception('Invalid token', CResponse::HTTP_STATUS_UNAUTHORIZED);
                }
            }

            return 'OK';
        }
    }


    /**
     * @return string|null
     */
    public function getToken() :?string
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
        $this->user_roles[] = $role;
    }


    /**
     * Set a User Role (Normally detected by DB)
     * 
     * @param string|int $role role of user where role can be "Admin", "Publisher" and anything else or a number 1=Admin, 2=Publisher, 3=Normal User
     *                         the role name and type depends from your app
     * @param string $module_name the name of module (see "_module" db table) 
     * @param array  $permissions Name of permissions [GET, POST, PATCh, PUT, DELETE, OPTIONS] 
     *               or if you prefer you can use C.R.U.D.O. letters [C=POST (Insert), R=GET (Read), U=PATCH|PUT (Modify), D=DELETE, O=OPTIONS].
     *               Finally, you can use * for all valid permission
     * @param bool $only_owner if true the permission is valid only for the owners (see id__user in the table)
     */
    public function setRolePermissions( string|int $role, string $module_name, array $permissions, $only_owner = false )
    {
        $this->role_permissions[$role] = [];
        array_map(function($key,&$value) use($role, $module_name, $only_owner) 
                  { 
                        $this->addRolePermission( $role, $module_name, $value, $only_owner ); 
                  }, $permissions);
    }


    /**
     * Set a User Role (Normally detected by DB)
     * 
     * @param string|int $role role of user where role can be "Admin", "Publisher" and anything else or a number 1=Admin, 2=Publisher, 3=Normal User
     *                         the role name and type depends from your app
     * @param string $module_name the name of module (see "_module" db table) 
     * @param array $permissions list of permission (can_read, can_insert, can_delete, can_udate) 
     * @param bool $only_owner if true the permission is valid only for the owners (see id__user in the table)
     *              
     */
    public function addRolePermission( string|int $role, string $module_name, array $permissions, $only_owner = false )
    {
        $role = $role ?? '*';

        if (!isset($this->role_permissions[$role]))
            $this->role_permissions[$role] = [];

        if (!isset($this->role_permissions[$role][$module_name]))
            $this->role_permissions[$role][$module_name] = [];


        if ($only_owner)
            $this->role_permissions[$role][$module_name][] = 'ONLY_OWNER';

        if ($permissions['can_insert']=='YES')
            $this->role_permissions[$role][$module_name][] = 'POST';

        if ($permissions['can_read']=='YES')
        {
            $this->role_permissions[$role][$module_name][] = 'GET';
            $this->role_permissions[$role][$module_name][] = 'HEAD';
            $this->role_permissions[$role][$module_name][] = 'OPTIONS';
        }
                        
        if ($permissions['can_update']=='YES')
        {
            $this->role_permissions[$role][$module_name][] = 'PATCH';
            $this->role_permissions[$role][$module_name][] = 'PUT';
        }
                        
        if ($permissions['can_delete']=='YES')
            $this->role_permissions[$role][$module_name][] = 'DELETE';
    }


    /**
     * Set a User Role (Normally detected by DB)
     * 
     * @param string $module_name the name of module (see "_module" db table) 
     * @param string $method method of operation [GET, POST, PATCH, PUT, DELETE, OPTIONS, HEAD] 
     * @return bool if true the permission is valid only for the owners (see id__user in the table)
     *              
     */
    public function isOnlyOwnerPermissions( string $module_name, $method ) : bool
    {
        if (isset($this->role_permissions))
            foreach( $this->role_permissions as &$role )
                if (isset($role[$module_name]) &&
                    ($role[$module_name][0] == 'ONLY_OWNER' &&
                     in_array($method, $role[$module_name]) 
                    )
                ) return true;
        return false;
    }


    /**
     * Check permissions by User Role (Normally detected by DB)
     * 
     * @param string $module_name the name of module (see "_module" db table) 
     * @param string $method method of operation [GET, POST, PATCH, PUT, DELETE, OPTIONS, HEAD] 
     *               Each method corresponds to a C.R.U.D. permission 
     *               [C=Create=POST, R=Read=GET/HEAD/OPTIONS, U=Update=PATCH|PUT, D=DELETE].
     * @return true if the current user have the permission for this method
     */
    public function checkPermission(string $module_name, string $method) : bool
    {
        foreach($this->user_roles as $user_role)
            if (isset($this->role_permissions[$user_role]))
            {
                if (isset($this->role_permissions[$user_role][$module_name]) &&
                    in_array($method, $this->role_permissions[$user_role][$module_name]))
                        return true;
                else // check if it can access all modules
                if (isset($this->role_permissions[$user_role]['*']) &&
                    in_array($method,$this->role_permissions[$user_role]['*']))
                        return true;
            }

        return false;
    }


    /**
     * Set a User Role (Normally detected by DB)
     * 
     * @param string $module_name the name of module (see "_module" db table) 
     * 
     * return array contains all permissions for selected module
     */
    public function getPermissions(string $module_name) : array
    {
        $permissions = [];
        $user_roles = array_merge(['*'], $this->user_roles ?? []);
        foreach($user_roles as $role)
        {
            if (isset($this->role_permissions[$role]) && isset($this->role_permissions[$role][$module_name]))
            {
                $rp = &$this->role_permissions[$role][$module_name];
                
                if (in_array('GET',$rp))    $permissions['can_read']   = 'can_read';
                if (in_array('POST',$rp))   $permissions['can_insert'] = 'can_insert';
                if (in_array('PATCH',$rp))  $permissions['can_update'] = 'can_update';
                if (in_array('DELETE',$rp)) $permissions['can_delete'] = 'can_delete';
            }    
        }

        return $permissions;
    }


    /**
     * Role Condition for sql query
     */
    private function _setRoleConditions()
    {
        $role_conditions = '(id__role IS NULL)';
        if (in_array('*',$this->user_roles))
            $role_conditions = '';
        else
            if (count($this->user_roles)>0)
                $role_conditions = '(id__role IN ('.implode(',',$this->user_roles).') OR id__role IS NULL) ';
        
        return $role_conditions;
    }


    private function _queryCache(string $sql, bool $as_ids_array = false, string $area_name = '', bool $is_global = false)
    {
        $get = 'get'.(($is_global)?('Global'):(''));
        $set = 'set'.(($is_global)?('Global'):(''));
        
        $hash = hash(PASSWORD_ENC_ALGORITHM, $sql);
        $data = null;
        if ($this->cache) 
            $data = $this->cache->$get($hash, $area_name);

        if ($data === null)
        {
            $data = [];
            $this->query($sql);
            if ($as_ids_array) 
                $data = $this->getIDsArray();
            else
                $this->getDataTable($data);
            if ($data !== null)     
                $this->cache->$set($hash, $data, $area_name);    
        }

        return $data;
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
     * The "Role" table contains the possible roles:
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
     * The "User" table contains all the user subscribed:
     * 
     *  id    name
     *  ===================
     *   1    Administrator   // Can make all
     *   2    Anonymous       // Can read page and comment
     *   3    Editor          // Full power: can read, update, delete or update a page or a comment
     *   4    Writer          // Can read, write, update a page
     *   5    Commentator     // Can read a page and read or write a comment
     *
     * 
     * 
     *  
     *  id    role     module   can_read    can_insert    can_update  can_delete
     *  ========================================================================
     *   1     1       *         YES            YES             YES         YES
     *   2     2       page      YES             NO              NO          NO     
     *   3     2       comment   YES             NO              NO          NO     
     *   4     3       page      YES            YES             YES         YES 
     *   5     3       comment   YES            YES             YES         YES 
     *   6     4       page      YES            YES             YES          NO 
     *   6     5       comment   YES            YES              NO          NO 
     * 
     * 
     * For identificate the roles of current user is necessary use a cross table like this
     *
     *  id    role           user
     *  =========================
     *   1    1                 1
     *   2    2                 2
     *   3    3                 5
     *    
     */
    public function importACL(CRequest $request) : bool
    {
        $module_name = CRequest::getModuleName(); 

        $sc = new CSqlComposer();
        $sql = (string)$sc->select('id')->from(MODULE_TABLE)->where("name='$module_name'");       
        $module_info = $this->_queryCache($sql, true, MODULE_TABLE, true);
        if ($module_info===null)
            $module_id = 0; 
        else 
            $module_id = $module_info[0];

        $sc = new CSqlComposer();
        $sql = (string)$sc->select('id__role')->from(USER_ROLE_TABLE)->where('id__user='.$this->id);       
        $user_role = $this->_queryCache($sql, true, USER_ROLE_TABLE);       

        $role_conditions = (($user_role)?('(id__role IS NULL OR id__role IN ('.implode(',',$user_role).'))'):('FALSE'));

        $module_conditions = "(id__module IS NULL OR id__module IN ($module_id))";
        $sc->clear();
        $sql = (string)$sc->select('id__role,can_read,can_update,can_insert,can_delete,only_owner')->
                    from(ACL_TABLE)->
                    where("($role_conditions AND $module_conditions)");

        $hash = hash(PASSWORD_ENC_ALGORITHM, $sql);
        $data = null;
        if ($this->cache) 
            $data = $this->cache->get($hash,ACL_TABLE);

        if ($data === null)
        {
            $data = [];
            $this->getQueryDataTable($data, $sql);
            if ($data !== null)     
                $this->cache->set($hash, $data, ACL_TABLE);           
        }

        if ($data !== null)
            foreach($data as $row)
            {
                $role = $row['id__role'] ?? '*';
                $only_owner = ($row['only_owner']=='YES');
                $this->addUserRole( $role );
                $this->addRolePermission( $role, $module_name, $row, $only_owner );
            }

        return $data !== null;
    }

    
    /**
     * 
     * Check if a user have a role
     * 
     * @return bool true if the user have the role, false otherwise
     */
    public function hasRole(string $name) : bool
    {
        $sc = new CSqlComposer();
        $sql = $sc->select('count(*)')->from(USER_ROLE_TABLE)->
                    where("id__user=$this->id AND id__role IN (SELECT id FROM ".ROLE_TABLE." WHERE name='$name')");

        return (bool)$this->getQueryDataValue($sql);
    }


    /**
     * 
     * Check if a user can view all modules (as an Administrator)
     * 
     * @return bool true if the user can view all modules
     */
    public function canViewAllModules() : bool
    {
        $sc = new CSqlComposer();
        $role_conditions = $this->_setRoleConditions();

        $sql = $sc->select('count(*)')->from(ACL_TABLE)->where("$role_conditions AND id__module IS NULL");

        $hash = 'acl/'.hash(PASSWORD_ENC_ALGORITHM, $sql);
        $data = null;
        if ($this->cache) 
            $data = $this->cache->get($hash);

        if ($data === null)
        {
            $data = (bool)$this->getQueryDataValue($sql);
            if ($data !== null)     
                $this->cache->set($hash, $data);           
        }
         
        return (bool)$data;
    }
    

    /**
     * 
     * Returns the list of modules available to the user
     * 
     * @return array the list of modules available to the user
     */
    public function getAllAvaiableModules() : array
    {
        $sc = new CSqlComposer();
        $role_conditions = $this->_setRoleConditions();
        if ($role_conditions!=='')
            $role_conditions .= ' AND'; 
        $sql = $sc->select('DISTINCT name')->from(ACL_TABLE)->
                    leftJoin(MODULE_TABLE, 'mt.id='.ACL_TABLE.'.id__module OR id__module IS NULL', 'mt')->
                    where("$role_conditions ".ACL_TABLE.".can_read='YES' AND visible='yes'");

        $hash = 'acl/'.hash(PASSWORD_ENC_ALGORITHM, $sql);
        
        $data = [];
        if ($this->cache) 
            $data = $this->cache->get($hash);

        if (!$data)
        {
            $this->query($sql);
            $dat = $this->getIdsArray();
            if ($dat !== null)
            {
                $data = $dat;
                $this->cache->set($hash, $data);           
            }     
        }
               
        return $data;
    }


    /**
     * @return [type]
     */
    public function subscribe()
    {
        // CONTACT
        // id,name,surname,address,zip_code,city,state,email
        $params = $this->request->getParameters();

        if (strlen($params['dusername'])<8)
            throw new Exception('The username must be at least 8 characters');
        else
        {
            $sc = new CSqlComposer();
            $sc->select('count(*)')->from(USER_TABLE)->where('username=\''.str_replace("'","''",$params['dusername']).'\'');
            if ($this->getQueryDataValue($sc))
                throw new Exception('The username already exists');
        }
        if (strlen($params['name'])==0) throw new Exception('The name can\'t be empty');
        if (strlen($params['surname'])==0) throw new Exception('The surname can\'t be empty');
        if (strlen($params['address'])==0) throw new Exception('The address can\'t be empty');
        if (strlen($params['zip_code'])==0) throw new Exception('The zip_code can\'t be empty');
        if (strlen($params['city'])==0) throw new Exception('The city can\'t be empty');
        if (strlen($params['email'])==0) throw new Exception('The email can\'t be empty');
        else
        {
            $m = [];
            if (!preg_match('/([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9_-]+)/i', $params['email'], $m ))
                throw new Exception('The email address is not valid');
            else
            {
                $sc = new CSqlComposer();
                $sc->select('count(*)')->from('_contact')->where('email=\''.str_replace("'","''",$params['email']).'\'');
                if ($this->getQueryDataValue($sc))
                    throw new Exception('The email address already exists');
            }
        }


        $contact = [];
        $contact['name'] = $params['name'];
        $contact['surname'] = $params['surname'];
        $contact['address'] = $params['address'];
        $contact['zip_code'] = $params['zip_code'];
        $contact['city'] = $params['city'];
        $contact['state'] = $params['state'];
        $contact['email'] = $params['email'];

        $types = [];
        $types[] = 'VARCHAR';
        $types[] = 'VARCHAR';
        $types[] = 'VARCHAR';
        $types[] = 'VARCHAR';
        $types[] = 'VARCHAR';
        $types[] = 'VARCHAR';
        $types[] = 'VARCHAR';

        if ($this->insertRecord('_contact', $contact, $types))
        {
            // USER
            // id, name, id__contact, domain, nickname, username, pwd, status
            $user = [];
            $user['name'] =  $contact['name'][0].'.'.$contact['surname'];
            $user['id__contact'] = $this->getLastID();
            $user['domain'] = DOMAIN_NAME;
            $user['nickname'] = $params['nickname'];
            $user['username'] = $params['dusername'];            

            // Random password
            $data = '!?*#@123456789ABCDEFGHIJKLMNPQRSTUVWXYZabcefghijklmnpqrstuvwxyz';
            $clear_pwd = substr(str_shuffle($data), 0, 8);
            $user['pwd'] =  hash(PASSWORD_ENC_ALGORITHM, $clear_pwd);
            $user['status'] = 'Verified';
            // $user['status'] = 'Subscribed';
            
            $types = [];
            $types[] = 'VARCHAR';
            $types[] = 'INT';
            $types[] = 'VARCHAR';
            $types[] = 'VARCHAR';
            $types[] = 'VARCHAR';
            $types[] = 'VARCHAR';
            $types[] = 'VARCHAR';
            $ret = $this->insertRecord(USER_TABLE, $user, $types);    
            if ($ret)
            {
                $mail = new \DressApi\core\Mail\CMail();
                $body_html = "Hi ".$user['name'].",\n\n<br><br>Your account is:\n\n<br><br>".
                             "USERNAME: ".$user['username']."\n\n<br><br>".
                             "PASSWORD: $clear_pwd\n<br>";
                $mail->setFrom(MAIL_TO_REPLY);
                $mail->send($contact['email'], $contact['name'].' '.$contact['surname'], DOMAIN_NAME.": your account", $body_html);
                // $clear_pwd;
            }
        }
        else
            throw new Exception("Error on subscribe");


            

        // USER
        // id, name, id__contact, domain, nickname, username, pwd, status
        

      //   throw new Exception("subscribe todo");
    }


    /**
     * @return [type]
     */
    public function logout()
    {
        // normally the client loose his token for logout (like deleting cookies)
        // but if logout is necessary we can make a black list on file
        // not through the db because it would always require a connection and slow down the service 
        throw new Exception("logout todo");
    }
}
