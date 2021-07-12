<div>
    <h1>DressApi</h1>
    DressApi is an <b>open source framework under Apache 2.0 license for create a modern REST API</b>.
    The name "DressApi" means it "dress up" your database, substantially it provides a quick REST API, to your db schema. 
    DressApi maps your database as an <b>ORM</b> (Object-relational mapping) and it does it dynamically. Although it is structured as an <b>MVC</b> (Model, View, Controller) it does not need to define a model for each table in the DB but if it automatically reads and manage it from the DB. 
    The most obvious advantage is that if the data structure changes over time, even significantly, the model fits automatically without touching a line of your code.
</div>
<div>
    <h2>Test API with an example</h2>
    <ul>
        <li>Import <b>file dressapi-test.sql</b> db into new database named "dressapi-test". The file is in <b>_tests/</b> folder.</li>
        <li>Set the parameters for your database: you can leave it like this or change the parameters depending on how yours will be accessible.
            The parameters is in root <b>config.php</b>, be careful that it is the root config.php because there are other config.php files, in fact there is one for each module that we will see later.
            <ul>
                <li><b>define('DB_HOST', 'localhost');</b> // server name or IP address of the server hosting the database<br></li>  
                <li><b>define('DB_PORT', 3306);</b>// Port of DB, for mysql the default is 3306<br></li>
                 <li><b>define('DB_NAME', 'dressapi-test');</b>// name of DB<br></li>
                <li><b>define('DB_USERNAME', 'root');</b>// Username of db user<br></li>
                <li><b>define('DB_PASSWORD', '');</b>// Password of db user<br></li>
            </ul>
        </li>
        </li>
    Set how the database identifies users, i.e. primary key name (<b> id </b>), table name (<b> user </b>),
    the name of the username and password fields. The settings are those used by the dressapi-test.sql database.
                <ul>
                <li><b>define('USER_ITEM_ID', 'id');</b></li>  
                <li><b>define('USER_TABLE', 'user');</b></li>
                <li><b>define('USER_ITEM_USERNAME', 'username');</b></li>
                <li><b>define('USER_ITEM_PASSWORD', 'pwd');</b></li>
            </ul>
        </li>
        <li>Try to run a login request as admin:<br>
            <b>curl -X POST http://dressapi/api/user/ -d "username=admin&password=admin"</b>
        </li>
        <li>Copy your token, it will be your passkey for all future requests as an admin user until the token expires. It must be like this:<br><b>            eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpYXQiOjE2MjYwNDAwMjYsImp0aSI6IlJkSUx2SHdJT3FxQ3pXMkorUVdZdGc9PSIsImlzcyI6IkRyZXNzQXBpLmNvbSIsIm5iZiI6MTYyNjA0MDAyNiwiZXhwIjoxNjQxOTQxMjI2LCJkYXRhIjp7InVzZXJuYW1lIjoiYWRtaW4iLCJpZCI6MX19.CqBqDHEPWs5ZAmwew5FaOqAeQgM7XWbESEHlkceRwaPhfg_jL3xvrWPVs7hj8obEljQ9av_JJQVg29-u0s8VMw</b>
        </li>
        <li>Now make your request inside DB<br>
            <b>curl -H  "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpYXQiOjE2MjYwNDAwMjYsImp0aSI6IlJkSUx2SHdJT3FxQ3pXMkorUVdZdGc9PSIsImlzcyI6IkRyZXNzQXBpLmNvbSIsIm5iZiI6MTYyNjA0MDAyNiwiZXhwIjoxNjQxOTQxMjI2LCJkYXRhIjp7InVzZXJuYW1lIjoiYWRtaW4iLCJpZCI6MX19.CqBqDHEPWs5ZAmwew5FaOqAeQgM7XWbESEHlkceRwaPhfg_jL3xvrWPVs7hj8obEljQ9av_JJQVg29-u0s8VMw" -X GET http://dressapi/api/page/1</b>
        </li>
    </ul>
</div>
