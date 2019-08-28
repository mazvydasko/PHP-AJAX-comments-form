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
                        url: 'functions.php',
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
                                $('#comments-wrapper').prepend(response.comment)
                                $('#comments_count').text(response.comments_count);
                                $('#comment_text').val('');
                                $('#email').val('');
                                $('#name').val('');
                                $('#error-div').hide();
                                $('#comments_count').html(max);
                            }
                        }
                    });
                }
            } else {
                $('#error-div').show();
                $('#error-div').html("<p>Please fill all fields!</p>")
            }
        }
    );

    $(document).on('click', '.reply-btn', function (e) {
        e.preventDefault();
        var max = parseInt($('#comments_count').html());
        var comment_id = $(this).data('id');
        this.submitting = false;
        $(this).parent().siblings('form#comment_reply_form_' + comment_id).toggle();
        $(document).on('click', '.submit-reply', function (e) {
            e.preventDefault();
            var comment_id = $(this).parent()[0].getAttribute('data-id');
            var reply_textarea = $(this).siblings('textarea'); // reply textarea element
            var reply_text = $(this).siblings('textarea').val();
            var name = $('form#comment_reply_form_' + comment_id).find('input[name="reply_name"]').val();
            var email = $('form#comment_reply_form_' + comment_id).find('input[name="reply_email"]').val();

            if (name != "" && reply_text != "") {
                if (validateEmail(email) == false) {
                    $('form#comment_reply_form_' + comment_id).find('#reply-error-div').show();
                    $('form#comment_reply_form_' + comment_id).find('#reply-error-div').html("<p>Invalid Email</p>");
                } else {
                    if (!this.submitting) {
                        this.submitting = true;
                        var self = this;
                        $.ajax({
                            url: 'functions.php',
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
                                    self.submitting = false;
                                    alert('There was an error adding reply. Please try again');
                                } else {
                                    max++;
                                    $('.replies_wrapper_' + comment_id).append(data);
                                    reply_textarea.val('');
                                    $('.reply_form').hide();
                                    $('form#comment_reply_form_' + comment_id).find('input[name="reply_name"]').val('');
                                    $('form#comment_reply_form_' + comment_id).find('input[name="reply_email"]').val('');
                                    $('form#comment_reply_form_' + comment_id).find('#reply-error-div').hide();
                                    $('#comments_count').html(max);
                                    self.submitting = false;
                                }
                            }
                        });
                    }
                }
            } else {
                $('form#comment_reply_form_' + comment_id).find('#reply-error-div').show();
                $('form#comment_reply_form_' + comment_id).find('#reply-error-div').html("<p>Please fill all fields!</p>")
            }
        });
    });

    function validateEmail(email) {
        var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(String(email).toLowerCase());
    }
});