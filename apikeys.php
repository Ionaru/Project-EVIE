<?php
include __DIR__ . '/head.php';
include __DIR__ . '/nav.php';

if (isset ($_GET['action'], $_GET['pid'])) {
    doAction($_GET['action'], $_GET['pid']);
} else if (isset ($_GET['action']) && !isset ($_GET['pid'])) {
    $pid = '0';
    doAction($_GET['action'], $pid);
}

function getAPIInfo($keyID, $vCode)
{
    $map_url = 'https://api.eveonline.com//account/APIKeyInfo.xml.aspx?keyID=' . $keyID . '&vCode=' . $vCode;
    if (($response_xml_data = file_get_contents($map_url)) === false) {
        echo '<script type="text/javascript">';
        echo 'alert("The key you entered appears to be invalid or could not be verified.")';
        echo '</script>';
    } else {
        libxml_use_internal_errors(true);
        $data = $response_xml_data;
        if (!$data) {
            echo '<script type="text/javascript">';
            echo 'alert("The key you entered appears to be invalid or could not be verified.")';
            echo '</script>';
        } else {
            return $data;
        }
    }
    return '';
}

function getAPIType($data)
{
    $data = simplexml_load_string($data);
    return $data->result->key['type'];
}

function checkForError($data)
{
    return $data->error['code'];
}

function doAction($action, $pid)
{
    if ($action === 'setActive') {
        setActive($pid);
    } else if ($action === 'delete') {
        deleteKey($pid);
    } else if ($action === 'addKey') {
        addKey();
    }
}

function addKey()
{
    $db_connection = createDatabaseConnection();
    $pid = '0';
    $keyName = $_POST['keyName'];
    $keyID = $_POST['keyID'];
    $vCode = $_POST['vCode'];
    $keyXML = getAPIInfo($keyID, $vCode);
    if ($keyXML !== '') {
        $uniquepid = false;
        while(!$uniquepid){
            $pid = createRandomString();
            $pidcheck = 'SELECT apikey_pid FROM apikeys WHERE apikey_pid=\'' . $pid . '\';';
            $others = 0;
            foreach ($db_connection->query($pidcheck) as $row) {
                $others++;
            }
            if($others === 0){
                $uniquepid = true;
            }
        }
        setAllKeysInactive();
        $keyType = getAPIType($keyXML);
        $isActive = 1;
        $sql = 'INSERT INTO apikeys (apikey_pid,user_name,apikey_name,apikey_keyid,apikey_vcode,apikey_type,apikey_isactive) VALUES (\'' . $pid . '\',\'' . $_SESSION['user_name'] . '\',\'' . $keyName . '\',\'' . $keyID . '\',\'' . $vCode . '\',\'' . $keyType . '\',\'' . $isActive . '\');';
        $db_connection->exec($sql);
        $_SESSION['keyID'] = $keyID;
        $_SESSION['vCode'] = $vCode;
        $_SESSION['selectedCharacter'] = 0;
    }
}

function setAllKeysInactive()
{
    $db_connection = createDatabaseConnection();
    $sql2 = 'UPDATE apikeys SET apikey_isactive = 0 WHERE user_name = \'' . $_SESSION['user_name'] . '\'';
    $db_connection->exec($sql2);
}

function setActive($pid)
{
    $db_connection = createDatabaseConnection();
    $sql = 'SELECT user_name,apikey_keyid,apikey_vcode,apikey_isactive FROM apikeys WHERE apikey_pid=\'' . $pid . '\';';
    $rows = 0;
    foreach ($db_connection->query($sql) as $row) {
        $rows++;
        if ($row['user_name'] === $_SESSION['user_name']) {
            if ((int) $row['apikey_isactive'] === 0) {
                setAllKeysInactive();
                $sql2 = 'UPDATE `apikeys` SET `apikey_isactive`= 1 WHERE `apikey_pid`=\'' . $pid . '\'';
                $db_connection->exec($sql2);
                $_SESSION['keyID'] = $row['apikey_keyid'];
                $_SESSION['vCode'] = $row['apikey_vcode'];
                $_SESSION['selectedCharacter'] = 0;
            }
        } else {
            echo '<script type="text/javascript">';
            echo 'alert("That API key does not belong to you.")';
            echo '</script>';
        }
    }
    if ($rows === 0) {
        echo '<script type="text/javascript">';
        echo 'alert("That API key does not exist.")';
        echo '</script>';
    }
}

function deleteKey($pid)
{
    $db_connection = createDatabaseConnection();
    $sql = 'SELECT user_name FROM apikeys WHERE apikey_pid=\'' . $pid . '\';';
    $rows = 0;
    foreach ($db_connection->query($sql) as $row) {
        $rows++;
        if ($row['user_name'] === $_SESSION['user_name']) {
            $sql2 = 'DELETE FROM `apikeys` WHERE `user_name` =\'' . $_SESSION['user_name'] . '\' AND `apikey_pid`=\'' . $pid . '\';';
            $db_connection->exec($sql2);
            unset($_SESSION['keyID'], $_SESSION['vCode']);
        } else {
            echo '<script type="text/javascript">';
            echo 'alert("That API key does not belong to you!")';
            echo '</script>';
        }
    }
    if ($rows === 0) {
        echo '<script type="text/javascript">';
        echo 'alert("That API key does not exist.")';
        echo '</script>';
    }
}

function createDatabaseConnection()
{
    try {
        $config = parse_ini_file('config/EVIEdatabase.ini');
        $DB_Host = $config['DB_Host'];
        $DB_Name = $config['DB_Name'];
        $DB_User = $config['DB_User'];
        $DB_Password = $config['DB_Password'];
    } catch (Exception $e) {
        return false;
    }
    try {
        $db_connection = new PDO('mysql:host=' . $DB_Host . ';dbname=' . $DB_Name . ';charset=utf8', $DB_User, $DB_Password);
        return $db_connection;
    } catch (PDOException $e) {
        $this->feedback = 'PDO database connection problem: ' . $e->getMessage();
    } catch (Exception $e) {
        $this->feedback = 'General problem: ' . $e->getMessage();
    }
    return false;
}

function getUserAPIKeys()
{
    $db_connection = createDatabaseConnection();
    global $apikey_name;
    global $apikey_keyid;
    global $apikey_vcode;
    global $apikey_type;
    global $apikey_isactive;
    global $apikey_dateadded;
    $sql = 'SELECT * FROM apikeys WHERE user_name = \'' . $_SESSION['user_name'] . '\'';
    foreach ($db_connection->query($sql) as $row) {
        $apikey_pid = $row['apikey_pid'];
        $apikey_name = $row['apikey_name'];
        $apikey_keyid = $row['apikey_keyid'];
        $apikey_vcode = $row['apikey_vcode'];
        $apikey_type = $row['apikey_type'];
        $apikey_isactive = $row['apikey_isactive'];
        $apikey_dateadded = $row['apikey_dateadded'];
        if ((int)$apikey_isactive === 1) {
            echo '<tr class="success">';
            echo '<td class="text-right"><button class="btn btn-sm pull-left" disabled><i class="fa fa-check"></i> Set active</button>';
            $_SESSION['keyID'] = $apikey_keyid;
            $_SESSION['vCode'] = $apikey_vcode;
            $_SESSION['selectedCharacter'] = 0;
        } else {
            echo '<tr>';
            echo '<td class="text-right"><a class="btn btn-success btn-sm pull-left" href="?pid=' . $apikey_pid . '&action=setActive"><i class="fa fa-check"></i> Set active</a>';
        }
        echo ' <a class="btn btn-danger btn-sm" href="?pid=' . $apikey_pid . '&action=delete"><i class="fa fa-times"></i> Delete</a></td>';
        echo '<td data-label="Key Name">' . $apikey_name . '</td>';
        echo '<td class="hidden-xs">' . $apikey_keyid . '</td>';
        echo '<td class="hidden-xs">' . $apikey_vcode . '</td>';
        echo '<td class="hidden-xs">' . $apikey_type . '</td>';
        echo '<td data-label="Date Added">' . $apikey_dateadded . '</td>';
        echo '</tr>';
    }
}

?>

<table class="table table-hover table-condensed apitable">
    <thead>
    <tr>
        <th>Actions</th>
        <th>Key Name</th>
        <th>Key ID</th>
        <th>Verification Code</th>
        <th>Key Type</th>
        <th>Date Added</th>
    </tr>
    </thead>
    <tbody>
    <?php getUserAPIKeys(); ?>
    </tbody>
</table>
<button data-toggle="modal" data-target="#apikeyModal" type="button" class="btn btn-primary">Add API Key</button>

<div id="apikeyModal" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" action="?action=addKey" name="temporaryform">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Add an API Key</h4>
                </div>
                <div class="modal-body">
                    <label for="input_KeyName">Name your API Key</label>
                    <input type="text" class="form-control" id="keyName" name="keyName" required
                           title="input_KeyName">
                    <hr>
                    <label for="login_KeyID">Key ID</label>
                    <input type="text" class="form-control" id="keyID" name="keyID" required title="login_KeyID">
                    <br>
                    <label for="login_vCode">Verification Code</label>
                    <input type="text" class="form-control" id="vCode" name="vCode" required title="login_vCode">
                </div>
                <div class="modal-footer">
                    <input type="submit" class="btn btn-info" name="login" value="Add Key"/>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/foot.php'; ?>

<script>
    function executePage() {

    }
</script>

</body>
</html>