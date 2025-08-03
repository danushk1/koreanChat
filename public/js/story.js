$(document).ready(function () {
    $('.see-more').click(function (e) {
        e.preventDefault();
        const storyId = $(this).data('id');

        $.ajax({
            url: `/stories/${storyId}`,
            type: 'GET',
            success: function (response) {
                $(`#preview-${storyId}`).hide(); // hide short content
                $(`#full-${storyId}`).removeClass('hidden').html(response.content);
            },
            error: function () {
                alert('Error loading story content.');
            }
        });

        $(this).hide(); // optionally hide "See more" link
    });
});
