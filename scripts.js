

$(document).ready(function () {
    $(document).on('click', '#submit_comment', function (e) {
        e.preventDefault();
        var max = parseInt($('#comments_count').html());
        var comment_text = $('#comment_text').val();
        var name = $('#name').val();
        var email = $('#email').val();
        if (name != "" && comment_text != "") {
            if (validateEmail(email) == false) {
                $('#error-div').show();
                $('#error-div').html("<p>Invalid Email</p>")
            } else {
                $.ajax({
                    url: "functions.php",
                    type: "POST",
                    data: {
                        comment_text: comment_text,
                        name: name,
                        email: email,
                        comment_posted: 1
                    },
                    success: function (data) {
                        var response = JSON.parse(data);
                        if (data === "error") {
                            alert('There was an error adding comment. Please try again');
                        } else {
                            max++;
                            $('#comments-wrapper').prepend(response.comment);
                            $('#comments_count').text(response.comments_count);
                            $('#comment_text').val('');
                            $('#email').val('');
                            $('#name').val('');
                            $('#error-div').hide()
                            $('#comments_count').html(max);
                        }
                    }
                });
            }
        } else {
            $('#error-div').show();
            $('#error-div').html("<p>Please fill all fields!</p>")
        }
    });


    $(document).on('click', '.reply-btn', function (e) {
        e.preventDefault();
        var max = parseInt($('#comments_count').html());
        var comment_id = $(this).data('id');
        $(this).parent().siblings('form#comment_reply_form_' + comment_id).toggle();
        $(document).on('click', '.submit-reply', function (e) {
            e.preventDefault();
            var reply_textarea = $(this).siblings('textarea');
            var name = $('#reply_name').val();
            var email = $('#reply_email').val();
            var reply_text = $('#reply_text').val();
            if (name != "" && reply_text != "") {
                if (validateEmail(email) == false) {
                    $('#reply-error-div').show();
                    $('#reply-error-div').html("<p>Invalid Email</p>")
                } else {
                    $.ajax({
                        url: "functions.php",
                        type: "POST",
                        data: {
                            comment_id: comment_id,
                            reply_text: reply_text,
                            name: name,
                            email: email,
                            reply_posted: 1
                        },
                        success: function (data) {
                            if (data === "error") {
                                alert('There was an error adding reply. Please try again');
                            } else {
                                max++;
                                $('.replies_wrapper_' + comment_id).append(data);
                                reply_textarea.hide();
                                $('.reply_form').hide();
                                $('#reply_name').val('');
                                $('#reply_email').val('');
                                $('#reply_text').val('');
                                $('#reply-error-div').hide()
                                $('#comments_count').html(max);

                            }
                        }
                    });
                }
            } else {
                $('#reply-error-div').show();
                $('#reply-error-div').html("<p>Please fill all fields!</p>")
            }
        });
    });

    function validateEmail(email) {
        var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(String(email).toLowerCase());
    }


});