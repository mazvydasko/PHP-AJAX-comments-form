<?php

$db["db_user"] = "root";
$db["db_pass"] = "";

foreach ($db as $key => $value){
    define(strtoupper($key), $value);
}

try {
    $conn = new PDO("mysql:host=localhost;dbname=comments-reply", DB_USER, DB_PASS);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//    echo "Connected successfully";
}
catch(PDOException $e)
{
    echo "Connection failed: " . $e->getMessage();
}


// Get all comments from database
$stmt = $conn->prepare("SELECT * FROM comments ORDER BY created_at DESC");
$stmt->execute();
$comments = $stmt->fetchAll();

// Receives a comment id and returns the username
function getRepliesByCommentId($id)
{
    global $conn;
    $stmt =  $conn->prepare("SELECT * FROM replies WHERE comment_id=$id");
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $replies = $stmt -> fetchAll();
    return $replies;
}

function getCommentsCount()
{
    global $conn;
    $resultCommentsStm = $conn->prepare( "SELECT id FROM comments");
    $resultCommentsStm->execute();
    $resultRepliesStm = $conn->prepare( "SELECT id FROM replies");
    $resultRepliesStm->execute();
    $numComments = $resultCommentsStm->rowCount();
    $numReplies = $resultRepliesStm->rowCount();
    $result = $numComments + $numReplies;
    return $result;
}

if (isset($_POST['comment_posted'])) {
    global $conn;
    $comment_text = (string) htmlspecialchars($_POST['comment_text']);
    $name = (string) htmlspecialchars($_POST['name']);
    $email = (string) htmlspecialchars($_POST['email']);

    $sql = "INSERT INTO comments (body, name, email) VALUES (?,?,?)";
    $stmt= $conn->prepare($sql);
    $result = $stmt->execute([$comment_text, $name, $email]);

    // Query same comment from database to send back to be displayed
    $inserted_id = $conn->lastInsertId(); ;
    $stmt = $conn->prepare( "SELECT * FROM comments WHERE id=?");
    $stmt->execute([$inserted_id]);
//    $inserted_comment = $stmt->fetch(PDO::FETCH_ASSOC);


//
//
//    // if insert was successful, get that same comment from the database and return it
    if ($result) {
        while ($inserted_comment = $stmt->fetch(PDO::FETCH_ASSOC)){
            $comment = "<div class='comment clearfix'>
					    <div class='comment-details'>
						    <span class='comment-name'>" . $inserted_comment['name'] . "</span>
						    <span class='comment-date'>" . $inserted_comment['created_at'] . "</span>
                            <a class='reply-btn btn btn-primary pull-right' href='#' data-id='" . $inserted_comment['id'] . "'>Reply</a>
						    <p>" . $inserted_comment['body'] . "</p>
					    </div>
                        <!-- reply form -->
                        <form action='functions.php' class='reply_form clearfix dom' id='comment_reply_form_" . $inserted_comment['id'] . "' data-id='" . $inserted_comment['id'] . "'>
                        <div class=\"form-row mb-4\">
                            <div id=\"reply-error-div\"></div>
                                <div class=\"col\">
                                    <label for=\"email\">Email*</label>
                                    <input type=\"email\" id=\"reply_email\" class=\"form-control\" name=\"reply_email\"
                                               placeholder=\"Enter email\">
                                    </div>
                                    <div class=\"col\">
                                        <label for=\"name\">Name*</label>
                                        <input type=\"text\" id=\"reply_name\" class=\"form-control\" name=\"reply_name\"
                                               placeholder=\"Enter name\">
                                    </div>
                                </div>
                                <label for=\"comment\">Comment*</label>
                            <textarea class='form-control' name='reply_text' id='reply_text' cols='30' rows='2'></textarea>
                            <button class='btn btn-primary btn-xs pull-right submit-reply'>Submit reply</button>
                        </form>
                        <div class='replies_wrapper_" . $inserted_comment['id'] . "'></div>
				        </div>";
            $comment_info = array(
                'comment' => $comment,
            );
            echo json_encode($comment_info);
            exit();
        }

    } else {
        echo "error";
        exit();
    }
}

// If the user clicked submit on reply form...
if (isset($_POST['reply_posted'])) {
    global $conn;
    $reply_text = (string) htmlspecialchars($_POST['reply_text']);
    $comment_id = (string) htmlspecialchars($_POST['comment_id']);
    $name = (string) htmlspecialchars($_POST['name']);
    $email = (string) htmlspecialchars($_POST['email']);

    $sql = "INSERT INTO replies (comment_id, body, name, email) VALUES (?,?,?,?)";
    $stmt= $conn->prepare($sql);
    $result = $stmt->execute([$comment_id,$reply_text, $name, $email]);

    $inserted_id = $conn->lastInsertId(); ;
    $stmt = $conn->prepare( "SELECT * FROM replies WHERE id=?");
    $stmt->execute([$inserted_id]);

    // if insert was successful, get that same reply from the database and return it
    if ($result) {
        while ($inserted_reply = $stmt->fetch(PDO::FETCH_ASSOC)){
            $reply = "<div class='comment reply clearfix'>
					<div class='comment-details'>
						<span class='comment-name'>" . $inserted_reply['name'] . "</span>
						<span class='comment-date'>" . $inserted_reply['created_at'] . "</span>
						<p>" . $inserted_reply['body'] . "</p>
					</div>
				</div>";
            echo $reply;
            exit();
        }

    } else {
        echo "error";
        exit();
    }
}