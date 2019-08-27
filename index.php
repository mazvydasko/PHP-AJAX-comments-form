<?php include('functions.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comments</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" />
    <link rel="stylesheet" href="main.css">
</head>
<body>
<div class="container">
    <hr>
    <h1 class="text-center">Comment form</h1>
    <hr>
    <div class="row">
        <div class="col-md-6 col-md-offset-3 comments-section">
                <form class="clearfix" action="index.php" method="post" id="comment_form">
                    <div class="form-row mb-4">
                        <div id="error-div"></div>
                        <div class="col">
                            <label for="email">Email*</label>
                            <input type="email" id="email" class="form-control" name="email" placeholder="Enter email">
                        </div>
                        <div class="col">
                            <label for="name">Name*</label>
                            <input type="text" id="name" class="form-control" name="name" placeholder="Enter name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="comment">Comment*</label>
                        <textarea class="form-control" id="comment_text" name="comment"></textarea>
                    </div>
                    <button class="btn btn-primary pull-right" id="submit_comment">Submit comment</button>
                </form>
            <hr>
            <!-- Display total number of comments -->
            <h2><span id="comments_count"><?php echo getCommentsCount() ?></span> Comment(s)</h2>
            <hr>
            <!-- comments wrapper -->
            <div id="comments-wrapper">
                <?php if (isset($comments)): ?>
                    <!-- Display comments -->
                    <?php foreach ($comments as $comment): ?>
                        <!-- comment -->
                        <div class="comment clearfix">
                            <div class="comment-details">
                                <span class="comment-name"><?php echo$comment['name'] ?></span>
                                <span class="comment-date"><?php echo $comment["created_at"]; ?></span>
                                <a class="reply-btn pull-right btn btn-primary" href="#" data-id="<?php echo $comment['id']; ?>">Reply</a>
                                <p><?php echo $comment['body']; ?></p>
                            </div>

                            <!-- reply form -->
                            <form action="index.php" class="reply_form clearfix" id="comment_reply_form_<?php echo $comment['id'] ?>" data-id="<?php echo $comment['id']; ?>">
                                <div class="form-row mb-4">
                                    <div id="reply-error-div"></div>
                                    <div class="col">
                                        <label for="email">Email*</label>
                                        <input type="email" id="reply_email" class="form-control" name="reply_email" placeholder="Enter email">
                                    </div>
                                    <div class="col">
                                        <label for="name">Name*</label>
                                        <input type="text" id="reply_name" class="form-control" name="reply_name" placeholder="Enter name">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="comment">Comment*</label>
                                    <textarea class="form-control" id="reply_text" name="reply_text"></textarea>
                                </div>
                                <button class="btn btn-primary pull-right submit-reply">Submit reply</button>
                            </form>

                            <!-- GET ALL REPLIES -->
                            <?php $replies = getRepliesByCommentId($comment['id']) ?>
                            <div class="replies_wrapper_<?php echo $comment['id']; ?>">
                                <?php if (isset($replies)): ?>
                                    <?php foreach ($replies as $reply): ?>
                                        <!-- reply -->
                                        <div class="comment reply clearfix">
                                            <div class="comment-details">
                                                <span class="comment-name"><?php echo $reply['name'] ?></span>
                                                <span class="comment-date"><?php echo $reply["created_at"]; ?></span>
                                                <p><?php echo $reply['body']; ?></p>
                                            </div>
                                        </div>
                                    <?php endforeach ?>
                                <?php endif ?>
                            </div>
                        </div>
                        <!-- // comment -->
                    <?php endforeach ?>
                <?php endif ?>
            </div><!-- comments wrapper -->
        </div><!-- // all comments -->
    </div>
    <br>
</div>
<!-- Javascripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<!-- Bootstrap Javascript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="scripts.js"></script>
</body>
</html>