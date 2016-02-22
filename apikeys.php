<?php ob_start();
include __DIR__ . '/head.php';
include __DIR__ . '/nav.php';

if (isset ($_GET['action'], $_GET['id'])) {
    doAction($_GET['action'], $_GET['id']);
} else if (isset ($_GET['action']) && !isset ($_GET['id'])) {
    $id = 0;
    doAction($_GET['action'], $id);
}

function getAPIInfo($keyID, $vCode)
{
    $map_url = 'https://api.eveonline.com//account/APIKeyInfo.xml.aspx?keyID=' . $keyID . '&vCode=' . $vCode;
    if (($response_xml_data = file_get_contents($map_url)) === false) {
        echo "Error fetching XML\n";
    } else {
        libxml_use_internal_errors(true);
        $data = simplexml_load_string($response_xml_data);
        if (!$data) {
            echo "Error loading XML\n";
            foreach (libxml_get_errors() as $error) {
                echo "\t", $error->message;
            }
        } else {
            return $data;
        }
    }
    return false;
}

function getAPIType($data)
{
    return $data->result->key['type'];
}

function checkForError($data)
{
    return $data->error['code'];
}

function doAction($action, $id)
{
    if ($action === 'setActive') {
        setActive($id);
        header('Location: index.php');
        die();
    } else if ($action === 'delete') {
        deleteKey($id);
        header('Location: apikeys.php?char=0');
        die();
    } else if ($action === 'addKey') {
        addKey();
        header('Location: apikeys.php?char=0');
        die();
    }
}

function addKey()
{
    setAllKeysInactive();
    $db_connection = createDatabaseConnection();
    $keyName = $_POST['keyName'];
    $keyID = $_POST['keyID'];
    $vCode = $_POST['vCode'];
    $keyXML = getAPIInfo($keyID, $vCode);
    if ($keyXML !== '') {
        $keyType = getAPIType($keyXML);
        $isActive = 1;
        $sql = 'INSERT INTO apikeys (user_name,apikey_name,apikey_keyid,apikey_vcode,apikey_type,apikey_isactive) VALUES (\'' . $_SESSION['user_name'] . '\',\'' . $keyName . '\',\'' . $keyID . '\',\'' . $vCode . '\',\'' . $keyType . '\',\'' . $isActive . '\');';
        //$query = $db_connection->prepare($sql);
        $db_connection->exec($sql);
        //$query->execute();
        $_SESSION['keyID'] = $keyID;
        $_SESSION['vCode'] = $vCode;
        $_SESSION['selectedCharacter'] = 0;
    }
}

function setAllKeysInactive()
{
    $db_connection = createDatabaseConnection();
    $sql2 = 'UPDATE apikeys SET apikey_isactive = 0 WHERE user_name = \'' . $_SESSION['user_name'] . '\'';
    //$query = $db_connection->prepare($sql2);
    //$query->execute();
    $db_connection->exec($sql2);
}

function setActive($id)
{
    $db_connection = createDatabaseConnection();
    $sql = 'SELECT user_name,apikey_keyid,apikey_vcode FROM apikeys WHERE apikey_id=' . $id . ';';
    foreach ($db_connection->query($sql) as $row) {
        if ($row['user_name'] === $_SESSION['user_name']) {
            $sql2 = 'UPDATE `apikeys` SET `apikey_isactive`= 1 WHERE `apikey_id`=\'' . $id . '\'';
            $db_connection->exec($sql2);
            //$query->execute();
            $sql3 = 'UPDATE `apikeys` SET `apikey_isactive`= 0 WHERE `apikey_vcode`=\'' . $_SESSION['vCode'] . '\'';
            $db_connection->exec($sql3);
            //$query->execute();
            $_SESSION['keyID'] = $row['apikey_keyid'];
            $_SESSION['vCode'] = $row['apikey_vcode'];
            $_SESSION['selectedCharacter'] = 0;
        } else {
            echo '<script type="text/javascript">';
            echo 'alert("That API key does not belong to you.")';
            echo '</script>';
        }
    }
}

function deleteKey($id)
{
    $db_connection = createDatabaseConnection();
    $sql = 'SELECT user_name FROM apikeys WHERE apikey_id=' . $id . ';';
    foreach ($db_connection->query($sql) as $row) {
        if ($row['user_name'] === $_SESSION['user_name']) {
            $sql2 = 'DELETE FROM `apikeys` WHERE `user_name` =\'' . $_SESSION['user_name'] . '\' AND `apikey_id`=\'' . $id . '\'';
            $db_connection->exec($sql2);
            unset($_SESSION['keyID'], $_SESSION['vCode']);
        } else {
            echo '<script type="text/javascript">';
            echo 'alert("That API key does not belong to you!")';
            echo '</script>';
        }
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
        $apikey_id = $row['apikey_id'];
        $apikey_name = $row['apikey_name'];
        $apikey_keyid = $row['apikey_keyid'];
        $apikey_vcode = $row['apikey_vcode'];
        $apikey_type = $row['apikey_type'];
        $apikey_isactive = $row['apikey_isactive'];
        $apikey_dateadded = $row['apikey_dateadded'];
        //var_dump($row);
        if ((int)$apikey_isactive === 1) {
            echo '<tr class="success">';
            //echo '<td><i class="fa fa-check"></i>';
            echo '<td><button class="btn btn-xs" disabled><i class="fa fa-check"></i> Set active</button>';
            $_SESSION['keyID'] = $apikey_keyid;
            $_SESSION['vCode'] = $apikey_vcode;
            $_SESSION['selectedCharacter'] = 0;
            //header("Location: /eve/index.php?char=0");
            //die();
        } else {
            echo '<tr>';
            //echo '<td><button data-toggle="modal" data-target="#apikeyModal" type="button" class="btn btn-xs btn-default ">Make active</button></td>';
            echo '<td><a class="btn btn-success btn-xs" href="?char=0&id=' . $apikey_id . '&action=setActive"><i class="fa fa-check"></i> Set active</a>';
        }
        echo ' <a class="btn btn-danger btn-xs" href="?char=0&id=' . $apikey_id . '&action=delete"><i class="fa fa-times"></i> Delete</a></td>';
        echo '<td>' . $apikey_name . '</td>';
        echo '<td>' . $apikey_keyid . '</td>';
        echo '<td>' . $apikey_vcode . '</td>';
        echo '<td>' . $apikey_type . '</td>';
        echo '<td>' . $apikey_dateadded . '</td>';
        echo '</tr>';
    }
}


?>

    <table class="table table-hover table-condensed" style="width:100%">
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
    <button data-toggle="modal" data-target="#apikeyModal" type="button" class="btn btn-primary ">Add API Key</button>

    <div id="apikeyModal" class="modal fade">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="post" action="?char=0&action=addKey" name="temporaryform">
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
    </body>
    </html>
<?php ob_flush(); ?>