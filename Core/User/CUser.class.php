<?php
/**
 * 
 * DressAPI
 * @version 1.0
 * @license This file is under Apache 2.0 license
 * @author Tufano Pasquale
 * @copyright Tufano Pasquale
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
use DressApi\Core\DBMS\CMySqlComposer as CSqlComposer;
use DressApi\Core\Request\CRequest;
use DressApi\Core\Response\CResponse;
use DressApi\Core\Cache\CFileCache as CCache; // An alternative is CRedisCache

use DressApi\Core\Mail\CMail;

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

        $sql = $sc->select(USER_ITEM_ID.','.USER_ITEM_NAME)->from(USER_TABLE)->
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
            $this->id = self::USER_ID_ANONYMOUS;
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
     * @param array $permissions list of permission (can_read, can_insert, can_delete, can_udate) 
     *              
     */
    public function addRolePermission( string|int $role, string $module, array $permissions )
    {
        if (!isset($this->role_permissions[$role]))
            $this->role_permissions[$role] = [];

        if (!isset($this->role_permissions[$role][$module]))
            $this->role_permissions[$role][$module] = [];
            
        if ($permissions['can_insert']=='YES')
            $this->role_permissions[$role][$module][] = 'POST';

        if ($permissions['can_read']=='YES')
        {
            $this->role_permissions[$role][$module][] = 'GET';
            $this->role_permissions[$role][$module][] = 'HEAD';
            $this->role_permissions[$role][$module][] = 'OPTIONS';
        }
                        
        if ($permissions['can_update']=='YES')
        {
            $this->role_permissions[$role][$module][] = 'PATCH';
            $this->role_permissions[$role][$module][] = 'PUT';
        }
                        
        if ($permissions['can_delete']=='YES')
            $this->role_permissions[$role][$module][] = 'DELETE';                        
    }


    /**
     * Set a User Role (Normally detected by DB)
     * 
     * @param string $module normally is the name of DB table but could be not in an custom extension 
     * @param string $method method of operation [GET, POST, PATCH. PUT, DELETE, OPTIONS, HEAD] 
     *               Each method corresponds to a C.R.U.D. permission 
     *               [C=Create=POST, R=Read=GET/HEAD/OPTIONS, U=Update=PATCH|PUT, D=DELETE].
     * @return true if the current user have the permission for this method
     */
    public function checkPermission(string $module, string $method)
    {
        foreach($this->user_roles as $user_role)
            if (isset($this->role_permissions[$user_role]))
            {
                if (isset($this->role_permissions[$user_role][$module]) &&
                    in_array($method, $this->role_permissions[$user_role][$module]))
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
     * @param string $module normally is the name of DB table but could be not in an custom extension 
     * 
     * return array contains all permissions for selected module
     */
    public function getPermissions(string $module)
    {
        $permissions = [];
        foreach($this->user_roles as $role)
        {
            $quit = false;
            do // specific module and * if exists
            {
                if (isset($this->role_permissions[$role]) && isset($this->role_permissions[$role][$module]))
                {
                    $rp = &$this->role_permissions[$role][$module];
                    
                    if (in_array('GET',$rp))    $permissions['can_read']   = 'can_read';
                    if (in_array('POST',$rp))   $permissions['can_insert'] = 'can_insert';
                    if (in_array('PATCH',$rp))  $permissions['can_update'] = 'can_update';
                    if (in_array('DELETE',$rp)) $permissions['can_delete'] = 'can_delete';
                }
    
                if ($module=='*')
                    $quit = true;
                else
                    $module = '*';
            }while($quit);
        }

        return $permissions;
    }


    /**
     * Role Condition for sql query
     */
    private function _setRoleConditions()
    {
        $role_conditions = '(id_role IS NULL)';
        if (in_array('*',$this->user_roles))
            $role_conditions = '';
        else
            if (count($this->user_roles)>0)
                $role_conditions = '(id_role IN ('.implode(',',$this->user_roles).') OR id_role IS NULL) ';
        
        return $role_conditions;
    }

    private function _queryCache(string $sql, bool $as_ids_array = false, string $folder_cache = 'acl')
    {
        $hash = $folder_cache.'/'.hash(PASSWORD_ENC_ALGORITHM, $sql);
        $data = null;
        if ($this->cache) 
            $data = $this->cache->get($hash);

        if ($data === null)
        {
            $data = [];
            $this->query($sql);
            if ($as_ids_array) 
                $data = $this->getIDsArray();
            else
                $this->getDataTable($data);
            if ($data !== null)     
                $this->cache->set($hash, $data);           
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
        $module = CRequest::getModule(); 

        $sc = new CSqlComposer();
        $sql = (string)$sc->select('id_role')->from('user_role')->where('id_user='.$this->id);       
        $user_role = $this->_queryCache($sql, true);       

        $role_conditions = (($user_role)?('(id_role IS NULL OR id_role IN ('.implode(',',$user_role).'))'):('FALSE'));

        $sc->clear();
        $sql = (string)$sc->select('id_role,can_read,can_update,can_insert,can_delete')->
                    from('acl')->
                    where("($role_conditions AND (id_module IS NULL OR id_module IN (SELECT id FROM module WHERE name='$module')))");

        $hash = 'acl/'.hash(PASSWORD_ENC_ALGORITHM, $sql);
        $data = null;
        if ($this->cache) 
            $data = $this->cache->get($hash);

        if ($data === null)
        {
            $data = [];
            $this->getQueryDataTable($data, $sql);
            if ($data !== null)     
                $this->cache->set($hash, $data);           
        }

        if ($data !== null)
            foreach($data as $row)
            {
                $role = $row['id_role'] ?? '*';
                $this->addUserRole( $role );
                $this->addRolePermission( $role, $module, $row );
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
        $sql = $sc->select('count(*)')->from('user_role')->
                    where("id_user=$this->id AND id_role IN (SELECT id FROM role WHERE name='$name')");

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

        $sql = $sc->select('count(*)')->from('acl')->where("$role_conditions AND id_module IS NULL");

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
        $sql = $sc->select('name')->from('acl')->
                    leftJoin('module', 'mt.id=acl.id_module OR id_module IS NULL', 'mt')->
                    where("$role_conditions acl.can_read='YES'");

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
                $this->cache->set($hash, $dat);           
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
            $sc->select('count(*)')->from('user')->where('username=\''.str_replace("'","''",$params['dusername']).'\'');
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
                $sc->select('count(*)')->from('contact')->where('email=\''.str_replace("'","''",$params['email']).'\'');
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

        if ($this->insertRecord('contact', $contact, $types))
        {
            // USER
            // id, name, id_contact, domain, nickname, username, pwd, status
            $user = [];
            $user['name'] =  $contact['name'][0].'.'.$contact['surname'];
            $user['id_contact'] = $this->getLastID();
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
            $ret = $this->insertRecord('user', $user, $types);    
            if ($ret)
            {
                $mail = new \DressApi\Core\Mail\CMail();
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
        // id, name, id_contact, domain, nickname, username, pwd, status
        

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
