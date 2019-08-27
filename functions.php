<?php

$db = mysqli_connect("localhost", "root", "", "comments-reply");

$comments_query_result = mysqli_query($db, "SELECT * FROM comments ORDER BY created_at DESC");
$comments = mysqli_fetch_all($comments_query_result, MYSQLI_ASSOC);

function getRepliesByCommentId($id) {
    global $db;
    $result = mysqli_query($db, "SELECT * FROM replies WHERE comment_id=$id ORDER BY created_at DESC");
    $replies = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $replies;
}

function getCommentsCount() {
    global $db;
    $resultComments = mysqli_query($db,"SELECT id FROM comments");
    $resultReplies = mysqli_query($db,"SELECT id FROM replies");
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

    if (empty($comment_text) || empty($name) || empty($email)) {
        return false;
    } elseif (filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
        return false;
    } else {
        $sql = "INSERT INTO comments (body, name, email) VALUES ('$comment_text','$name','$email')";
        $result = mysqli_query($db, $sql);
        $inserted_id = $db->insert_id;
        $res = mysqli_query($db, "SELECT * FROM comments WHERE id=$inserted_id");
        $inserted_comment = mysqli_fetch_assoc($res);
        if ($result) {
            $comment = "<div class='comment clearfix'>
					<div class='comment-details'>
					
						<span class='comment-name'>" . $inserted_comment['name'] . "</span>
						<span class='comment-date'>" . $inserted_comment['created_at'] . "</span>
                        <a class='reply-btn pull-right btn btn-primary' href='#' data-id='" . $inserted_comment['id'] . "'>Reply</a>


						<p>" . $inserted_comment['body'] . "</p>
					</div>
					<!-- reply form -->
					<form action='index.php' class='reply_form clearfix' id='comment_reply_form_" . $inserted_comment['id'] . "' data-id='" . $inserted_comment['id'] . "'>
					    <div class=\"form-row mb-4\">
					    <div id=\"reply-error-div\"></div>
                                    <div class=\"col\">
                                        <label for=\"email\">Email*</label>
                                        <input type=\"email\" id=\"reply_email\" class=\"form-control\" name=\"reply_email\" placeholder=\"Enter email\">
                                    </div>
                                    <div class=\"col\">
                                        <label for=\"name\">Name*</label>
                                        <input type=\"text\" id=\"reply_name\" class=\"form-control\" name=\"reply_name\" placeholder=\"Enter name\">
                                    </div>
                                </div>
                                <div class=\"form-group\">
                                    <label for=\"comment\">Comment*</label>
                                    <textarea class=\"form-control\" id=\"reply_text\" name=\"reply_comment\"></textarea>
                                </div>
						<button class='btn btn-primary pull-right submit-reply'>Submit reply</button>
					</form>
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
}

if (isset($_POST['reply_posted'])) {
    global $db;
    $reply_text = $_POST['reply_text'];
    $comment_id = $_POST['comment_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];

    if (empty($reply_text) || empty($name) || empty($email)) {
        return false;
    } elseif (filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
        return false;
    } else {
        $sql = "INSERT INTO replies (comment_id, body, name, email) VALUES ('$comment_id', '$reply_text', '$name', '$email')";
        $result = mysqli_query($db, $sql);
        $inserted_id = $db->insert_id;
        $res = mysqli_query($db, "SELECT * FROM replies WHERE id=$inserted_id ORDER BY created_at DESC");
        $inserted_reply = mysqli_fetch_assoc($res);
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
}
