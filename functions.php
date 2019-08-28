<?php

// connect to database
$db = mysqli_connect("localhost", "root", "", "comments-reply");

// Get all comments from database
$comments_query_result = mysqli_query($db, "SELECT * FROM comments ORDER BY created_at DESC");
$comments = mysqli_fetch_all($comments_query_result, MYSQLI_ASSOC);

// Receives a comment id and returns the username
function getRepliesByCommentId($id)
{
    global $db;
    $result = mysqli_query($db, "SELECT * FROM replies WHERE comment_id=$id");
    $replies = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $replies;
}

function getCommentsCount()
{
    global $db;
    $resultComments = mysqli_query($db, "SELECT id FROM comments");
    $resultReplies = mysqli_query($db, "SELECT id FROM replies");
    $numComments = $resultComments->num_rows;
    $numReplies = $resultReplies->num_rows;
    $result = $numComments + $numReplies;
    return $result;
}

if (isset($_POST['comment_posted'])) {
    global $db;
    $comment_text = $_POST['comment_text'];
    $name = $_POST['name'];
    $email = $_POST['email'];

    $sql = "INSERT INTO comments (body, name, email) VALUES ('$comment_text', '$name', '$email')";
    $result = mysqli_query($db, $sql);

    // Query same comment from database to send back to be displayed
    $inserted_id = $db->insert_id;
    $res = mysqli_query($db, "SELECT * FROM comments WHERE id=$inserted_id");
    $inserted_comment = mysqli_fetch_assoc($res);

    // if insert was successful, get that same comment from the database and return it
    if ($result) {
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
    } else {
        echo "error";
        exit();
    }
}

// If the user clicked submit on reply form...
if (isset($_POST['reply_posted'])) {
    global $db;
    $reply_text = $_POST['reply_text'];
    $comment_id = $_POST['comment_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];

    $sql = "INSERT INTO replies (comment_id, body, name, email) VALUES ('$comment_id', '$reply_text', '$name', '$email')";
    $result = mysqli_query($db, $sql);

    $inserted_id = $db->insert_id;
    $res = mysqli_query($db, "SELECT * FROM replies WHERE id=$inserted_id");
    $inserted_reply = mysqli_fetch_assoc($res);

    // if insert was successful, get that same reply from the database and return it
    if ($result) {
        $reply = "<div class='comment reply clearfix'>
					<div class='comment-details'>
						<span class='comment-name'>" . $inserted_reply['name'] . "</span>
						<span class='comment-date'>" . $inserted_reply['created_at'] . "</span>
						<p>" . $inserted_reply['body'] . "</p>
					</div>
				</div>";
        echo $reply;
        exit();
    } else {
        echo "error";
        exit();
    }
}